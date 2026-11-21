<?php

require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/GameDao.php';
require_once __DIR__ . '/../dao/GenreDao.php';

/** The shared game catalog. */
class GameService extends BaseService
{
    protected array $fields = ['title', 'description', 'release_date', 'genre_id', 'cover_url'];

    public function __construct(GameDao $dao = new GameDao(), private GenreDao $genre_dao = new GenreDao())
    {
        parent::__construct($dao, 'Game');
    }

    /** Whole catalog, with the name of each genre. */
    public function get_all(): array
    {
        return $this->dao->get_all_with_genre();
    }

    public function get_by_id($id): array
    {
        $row = $this->dao->get_by_id_with_genre($id);
        if (!$row) {
            throw new NotFoundException('Game not found.');
        }
        return $row;
    }

    /** The games of one genre (404 if the genre does not exist). */
    public function get_by_genre($genre_id): array
    {
        if (!$this->genre_dao->get_by_id($genre_id)) {
            throw new NotFoundException('Genre not found.');
        }
        return $this->filter(['genre_id' => $genre_id]);
    }

    /**
     * Optional filters: search (text in the title, 2-50 characters) and genre_id.
     * They come from the URL, so they are checked first.
     */
    public function filter(array $filters): array
    {
        $search = null;
        if (isset($filters['search'])) {
            $search = trim((string) $filters['search']);
            Validator::length($search, 'search', 2, 50);
        }
        $genre_id = null;
        if (!empty($filters['genre_id'])) {
            $genre_id = Validator::integer($filters['genre_id'], 'genre_id', 1);
            if (!$this->genre_dao->get_by_id($genre_id)) {
                throw new NotFoundException('Genre not found.');
            }
        }

        $games = $this->dao->get_all_with_genre();
        if ($search !== null) {
            $ids = array_column($this->dao->search_by_title($search), 'game_id');
            $games = array_values(array_filter($games, fn($g) => in_array($g['game_id'], $ids)));
        }
        if ($genre_id !== null) {
            $games = array_values(array_filter($games, fn($g) => (int) $g['genre_id'] === $genre_id));
        }
        return $games;
    }

    protected function allowed(array $data): array
    {
        return $this->empty_to_null(
            parent::allowed($data),
            ['description', 'release_date', 'genre_id', 'cover_url']
        );
    }

    protected function validate(array $data, ?int $id = null): void
    {
        Validator::required($data, ['title']);
        Validator::length($data['title'], 'title', 1, 150);
        Validator::no_html($data['title'], 'title');

        if (!empty($data['description'])) {
            Validator::length($data['description'], 'description', 0, 5000);
            Validator::no_html($data['description'], 'description');
        }
        if (!empty($data['release_date'])) {
            Validator::date($data['release_date'], 'release_date');
        }
        if (!empty($data['genre_id'])) {
            Validator::integer($data['genre_id'], 'genre_id', 1);
            if (!$this->genre_dao->get_by_id($data['genre_id'])) {
                throw new ValidationException('The selected genre does not exist.');
            }
        }
        if (!empty($data['cover_url'])) {
            Validator::length($data['cover_url'], 'cover_url', 0, 500);
            Validator::no_html($data['cover_url'], 'cover_url');
        }

        $existing = $this->dao->get_game_by_title($data['title']);
        if ($existing && (int) $existing['game_id'] !== (int) $id) {
            throw new ValidationException("A game titled '{$data['title']}' already exists.");
        }
    }

    /** Deleting a game also removes it from every collection (ON DELETE CASCADE). */
    public function delete($id): void
    {
        $this->get_by_id($id);
        $this->dao->delete_game($id);
    }
}
