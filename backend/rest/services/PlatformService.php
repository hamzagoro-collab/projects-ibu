<?php

require_once __DIR__ . '/LookupService.php';
require_once __DIR__ . '/../dao/PlatformDao.php';
require_once __DIR__ . '/../dao/UserGameDao.php';

class PlatformService extends LookupService
{
    public function __construct(PlatformDao $dao = new PlatformDao(), private UserGameDao $user_game_dao = new UserGameDao())
    {
        parent::__construct($dao, 'Platform');
    }

    protected function find_by_name(string $name)
    {
        return $this->dao->get_platform_by_name($name);
    }

    protected function delete_blocker($id): ?string
    {
        $entries = $this->user_game_dao->count_by_platform($id);
        return $entries > 0
            ? "This platform is still used by $entries collection entr" . ($entries === 1 ? 'y' : 'ies') . '. Remove them first.'
            : null;
    }
}
