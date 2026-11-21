<?php

require_once __DIR__ . '/LookupService.php';
require_once __DIR__ . '/../dao/GenreDao.php';
require_once __DIR__ . '/../dao/GameDao.php';

class GenreService extends LookupService
{
    public function __construct(GenreDao $dao = new GenreDao(), private GameDao $game_dao = new GameDao())
    {
        parent::__construct($dao, 'Genre');
    }

    protected function find_by_name(string $name)
    {
        return $this->dao->get_genre_by_name($name);
    }

    protected function delete_blocker($id): ?string
    {
        $games = $this->game_dao->count_by_genre($id);
        return $games > 0
            ? "This genre still contains $games game(s). Move or delete them first."
            : null;
    }
}
