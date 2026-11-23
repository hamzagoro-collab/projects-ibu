<?php

require_once __DIR__ . "/BaseDao.php";

class PlatformDao extends BaseDao
{
    protected $table_name;

    public function __construct()
    {
        $this->table_name = "platforms";
        parent::__construct($this->table_name, "platform_id");
    }

    public function get_platform_by_name($name)
    {
        return $this->query_unique(
            'SELECT * FROM ' . $this->table_name . ' WHERE name = :name',
            ['name' => $name]
        );
    }
}
