<?php

require_once __DIR__ . '/BaseDao.php';

/** The shared game catalog (every game any user can add to a collection). */
class GameDao extends BaseDao
{
    protected $table_name;

    public function __construct()
    {
        $this->table_name = "games";
        parent::__construct($this->table_name, "game_id");
    }

    public function get_game_by_title($title)
    {
        return $this->query_unique(
            'SELECT * FROM ' . $this->table_name . ' WHERE title = :title',
            ['title' => $title]
        );
    }

    /** Case-insensitive "contains" search on the title. */
    public function search_by_title($term)
    {
        return $this->query(
            'SELECT * FROM ' . $this->table_name . ' WHERE title LIKE :term ORDER BY title',
            ['term' => '%' . $term . '%']
        );
    }

    public function get_games_by_genre($genre_id)
    {
        return $this->query(
            'SELECT * FROM ' . $this->table_name . ' WHERE genre_id = :genre_id ORDER BY title',
            ['genre_id' => $genre_id]
        );
    }

    /** Catalog games together with the name of their genre. */
    public function get_all_with_genre()
    {
        return $this->query(
            'SELECT g.*, ge.name AS genre_name
             FROM ' . $this->table_name . ' g
             LEFT JOIN genres ge ON ge.genre_id = g.genre_id
             ORDER BY g.title',
            []
        );
    }

    public function add_game($params)
    {
        return $this->add($params);
    }

    public function edit_game($params, $id)
    {
        return $this->update($params, $id);
    }

    public function delete_game($id)
    {
        $this->delete($id);
    }
}
