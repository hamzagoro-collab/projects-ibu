<?php

require_once __DIR__ . '/BaseDao.php';

/**
 * A user's collection: one row per (user, game, platform) with a play status
 * and an optional 1-5 rating.
 */
class UserGameDao extends BaseDao
{
    protected $table_name;

    /** Same columns every "collection with details" query returns. */
    private const DETAIL_SELECT =
        'SELECT ug.user_game_id, ug.user_id, ug.game_id, ug.platform_id,
                ug.status, ug.rating, ug.date_added,
                g.title, g.release_date, g.cover_url,
                ge.name AS genre_name,
                p.name AS platform_name
         FROM user_games ug
         INNER JOIN games g ON g.game_id = ug.game_id
         LEFT JOIN genres ge ON ge.genre_id = g.genre_id
         INNER JOIN platforms p ON p.platform_id = ug.platform_id';

    public function __construct()
    {
        $this->table_name = "user_games";
        parent::__construct($this->table_name, "user_game_id");
    }

    /** The whole collection of one user, newest additions last. */
    public function get_collection_for_user($user_id)
    {
        return $this->query(
            self::DETAIL_SELECT . ' WHERE ug.user_id = :user_id ORDER BY ug.user_game_id',
            ['user_id' => $user_id]
        );
    }

    /** $status must be 'Playing', 'Completed' or 'Want to play'. */
    public function get_by_status($user_id, $status)
    {
        return $this->query(
            self::DETAIL_SELECT . ' WHERE ug.user_id = :user_id AND ug.status = :status ORDER BY ug.user_game_id',
            ['user_id' => $user_id, 'status' => $status]
        );
    }

    public function get_wishlist_for_user($user_id)
    {
        return $this->get_by_status($user_id, 'Want to play');
    }

    /** Games whose title contains $term, limited to one user's collection. */
    public function search_collection($user_id, $term)
    {
        return $this->query(
            self::DETAIL_SELECT . ' WHERE ug.user_id = :user_id AND g.title LIKE :term ORDER BY g.title',
            ['user_id' => $user_id, 'term' => '%' . $term . '%']
        );
    }

    /** Same game on the same platform for the same user (the unique key), or false. */
    public function get_entry($user_id, $game_id, $platform_id)
    {
        return $this->query_unique(
            'SELECT * FROM ' . $this->table_name . '
             WHERE user_id = :user_id AND game_id = :game_id AND platform_id = :platform_id',
            ['user_id' => $user_id, 'game_id' => $game_id, 'platform_id' => $platform_id]
        );
    }

    /** Rows per status, e.g. [['status' => 'Playing', 'total' => 4], ...]. */
    public function count_by_status($user_id)
    {
        return $this->query(
            'SELECT status, COUNT(*) AS total
             FROM ' . $this->table_name . '
             WHERE user_id = :user_id
             GROUP BY status',
            ['user_id' => $user_id]
        );
    }

    /** Collection size per genre for one user (feeds the dashboard chart). */
    public function count_by_genre($user_id)
    {
        return $this->query(
            'SELECT COALESCE(ge.name, \'Unknown\') AS genre_name, COUNT(*) AS total_games
             FROM ' . $this->table_name . ' ug
             INNER JOIN games g ON g.game_id = ug.game_id
             LEFT JOIN genres ge ON ge.genre_id = g.genre_id
             WHERE ug.user_id = :user_id
             GROUP BY genre_name
             ORDER BY total_games DESC, genre_name',
            ['user_id' => $user_id]
        );
    }

    public function add_to_collection($params)
    {
        return $this->add($params);
    }

    public function set_status($user_game_id, $status)
    {
        return $this->update(['status' => $status], $user_game_id);
    }

    public function set_rating($user_game_id, $rating)
    {
        return $this->update(['rating' => $rating], $user_game_id);
    }

    public function remove_from_collection($user_game_id)
    {
        $this->delete($user_game_id);
    }
}
