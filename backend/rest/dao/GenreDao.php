<?php

require_once __DIR__ . "/BaseDao.php";

class GenreDao extends BaseDao
{
    protected $table_name;

    public function __construct()
    {
        $this->table_name = "genres";
        parent::__construct($this->table_name, "genre_id");
    }

    public function get_genre_by_name($name)
    {
        return $this->query_unique(
            'SELECT * FROM ' . $this->table_name . ' WHERE name = :name',
            ['name' => $name]
        );
    }

    /** How many catalog games exist in each genre (genres without games show 0). */
    public function get_game_count_by_genre()
    {
        return $this->query(
            'SELECT g.name AS genre_name, COUNT(gm.game_id) AS total_games
             FROM ' . $this->table_name . ' g
             LEFT JOIN games gm ON gm.genre_id = g.genre_id
             GROUP BY g.genre_id, g.name
             ORDER BY total_games DESC, g.name',
            []
        );
    }
}
