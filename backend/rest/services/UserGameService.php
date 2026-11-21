<?php

require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../../utils/Roles.php';
require_once __DIR__ . '/../dao/UserGameDao.php';
require_once __DIR__ . '/../dao/UserDao.php';
require_once __DIR__ . '/../dao/GameDao.php';
require_once __DIR__ . '/../dao/PlatformDao.php';

/**
 * A user's collection (library + wishlist). Regular users see and change only
 * their own entries; administrators can work with everybody's.
 */
class UserGameService extends BaseService
{
    public const STATUSES = ['Playing', 'Completed', 'Want to play'];
    private const WISHLIST = 'Want to play';

    public function __construct(
        UserGameDao $dao = new UserGameDao(),
        private UserDao $user_dao = new UserDao(),
        private GameDao $game_dao = new GameDao(),
        private PlatformDao $platform_dao = new PlatformDao()
    ) {
        parent::__construct($dao, 'Collection entry');
    }

    /* ---------------- reading ---------------- */

    /** Collection of the actor (admins: of $user_id), optionally by status and/or title text. */
    public function list_for(array $actor, $user_id = null, $status = null, $search = null): array
    {
        $user_id = $this->target_user($actor, $user_id);

        if ($status !== null && $status !== '') {
            Validator::one_of($status, 'status', self::STATUSES);
        } else {
            $status = null;
        }
        $search = $search === null ? '' : trim((string) $search);
        if ($search !== '') {
            Validator::length($search, 'search', 2, 50);
            $rows = $this->dao->search_collection($user_id, $search);
            return $status === null
                ? $rows
                : array_values(array_filter($rows, fn($r) => $r['status'] === $status));
        }
        return $status !== null
            ? $this->dao->get_by_status($user_id, $status)
            : $this->dao->get_collection_for_user($user_id);
    }

    public function get_entry($id, array $actor): array
    {
        $row = $this->dao->get_detail_by_id($id);
        if (!$row) {
            throw new NotFoundException('Collection entry not found.');
        }
        $this->assert_access($row, $actor);
        return $row;
    }

    /** Dashboard numbers: entries per status and games per genre. */
    public function stats(array $actor, $user_id = null): array
    {
        $user_id = $this->target_user($actor, $user_id);

        $by_status = array_fill_keys(self::STATUSES, 0);
        foreach ($this->dao->count_by_status($user_id) as $row) {
            $by_status[$row['status']] = (int) $row['total'];
        }
        $by_genre = array_map(
            fn($row) => ['genre_name' => $row['genre_name'], 'total_games' => (int) $row['total_games']],
            $this->dao->count_by_genre($user_id)
        );
        return [
            'user_id' => $user_id,
            'total' => array_sum($by_status),
            'by_status' => $by_status,
            'by_genre' => $by_genre
        ];
    }

    /* ---------------- writing ---------------- */

    /** Adds a catalog game to the actor's collection (status defaults to the wishlist). */
    public function add_entry(array $data, array $actor): array
    {
        Validator::required($data, ['game_id', 'platform_id']);
        $user_id = $this->target_user($actor, $data['user_id'] ?? null);
        $game_id = Validator::integer($data['game_id'], 'game_id', 1);
        $platform_id = Validator::integer($data['platform_id'], 'platform_id', 1);

        $game = $this->game_dao->get_by_id($game_id);
        if (!$game) {
            throw new ValidationException('The selected game does not exist.');
        }
        if (!$this->platform_dao->get_by_id($platform_id)) {
            throw new ValidationException('The selected platform does not exist.');
        }
        if (!$this->user_dao->get_by_id($user_id)) {
            throw new ValidationException('The selected user does not exist.');
        }

        $status = $data['status'] ?? self::WISHLIST;
        Validator::one_of($status, 'status', self::STATUSES);
        $rating = $this->rating_or_null($data['rating'] ?? null);

        $this->assert_rating_matches_status($status, $rating);
        $this->assert_released_if_played($game, $status);

        if ($this->dao->get_entry($user_id, $game_id, $platform_id)) {
            throw new ValidationException("'{$game['title']}' is already in this collection on that platform.");
        }

        $created = $this->dao->add_to_collection([
            'user_id' => $user_id,
            'game_id' => $game_id,
            'platform_id' => $platform_id,
            'status' => $status,
            'rating' => $rating,
            'date_added' => date('Y-m-d')
        ]);
        return $this->dao->get_detail_by_id($created['user_game_id']);
    }

    /** Change status, rating and/or platform. Only the fields that are sent change. */
    public function update_entry(array $data, $id, array $actor): array
    {
        $row = $this->dao->get_by_id($id);
        if (!$row) {
            throw new NotFoundException('Collection entry not found.');
        }
        $this->assert_access($row, $actor);

        $changes = [];
        $status = $row['status'];

        if (array_key_exists('status', $data)) {
            $status = $data['status'];
            Validator::one_of($status, 'status', self::STATUSES);
            $this->assert_released_if_played($this->game_dao->get_by_id($row['game_id']), $status);
            $changes['status'] = $status;
        }
        if (array_key_exists('rating', $data)) {
            $rating = $this->rating_or_null($data['rating']);
            $this->assert_rating_matches_status($status, $rating);
            $changes['rating'] = $rating;
        } elseif ($status === self::WISHLIST && $row['rating'] !== null) {
            $changes['rating'] = null;      // back on the wishlist: the old rating is dropped
        }
        if (array_key_exists('platform_id', $data)) {
            $platform_id = Validator::integer($data['platform_id'], 'platform_id', 1);
            if (!$this->platform_dao->get_by_id($platform_id)) {
                throw new ValidationException('The selected platform does not exist.');
            }
            $duplicate = $this->dao->get_entry($row['user_id'], $row['game_id'], $platform_id);
            if ($duplicate && (int) $duplicate['user_game_id'] !== (int) $id) {
                throw new ValidationException('This game is already in the collection on that platform.');
            }
            $changes['platform_id'] = $platform_id;
        }
        if (!$changes) {
            throw new ValidationException("Send 'status', 'rating' and/or 'platform_id'.");
        }

        $this->dao->update($changes, $id);
        return $this->dao->get_detail_by_id($id);
    }

    public function delete_entry($id, array $actor): void
    {
        $row = $this->dao->get_by_id($id);
        if (!$row) {
            throw new NotFoundException('Collection entry not found.');
        }
        $this->assert_access($row, $actor);
        $this->dao->remove_from_collection($id);
    }

    /* ---------------- rules ---------------- */

    /** Whose collection a request is about; non-admins can only use their own. */
    private function target_user(array $actor, $requested): int
    {
        if ($requested === null || $requested === '') {
            return (int) $actor['user_id'];
        }
        $requested = Validator::integer($requested, 'user_id', 1);
        if ($actor['role'] !== Roles::ADMIN && $requested !== (int) $actor['user_id']) {
            throw new ForbiddenException("You cannot access another user's collection.");
        }
        return $requested;
    }

    private function assert_access(array $row, array $actor): void
    {
        if ($actor['role'] !== Roles::ADMIN && (int) $row['user_id'] !== (int) $actor['user_id']) {
            throw new ForbiddenException("You cannot access another user's collection.");
        }
    }

    /** null (no rating) or a whole number from 1 to 5. */
    private function rating_or_null($rating): ?int
    {
        if ($rating === null || $rating === '') {
            return null;
        }
        return Validator::integer($rating, 'rating', 1, 5);
    }

    /** A game you only "want to play" cannot be rated yet. */
    private function assert_rating_matches_status(string $status, ?int $rating): void
    {
        if ($status === self::WISHLIST && $rating !== null) {
            throw new ValidationException("A game with status 'Want to play' cannot have a rating.");
        }
    }

    /** Unreleased games can only be on the wishlist. */
    private function assert_released_if_played(array $game, string $status): void
    {
        if ($status !== self::WISHLIST && !empty($game['release_date']) && $game['release_date'] > date('Y-m-d')) {
            throw new ValidationException("'{$game['title']}' is not released yet: it can only have status 'Want to play'.");
        }
    }
}
