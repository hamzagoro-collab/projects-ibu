<?php
require_once __DIR__ . '/BaseDao.php';

class UserDao extends BaseDao
{
    protected $table_name;

    public function __construct()
    {
        $this->table_name = "users";
        parent::__construct($this->table_name, "user_id");
    }

    public function get_by_email($email)
    {
        return $this->query_unique(
            'SELECT * FROM ' . $this->table_name . ' WHERE email = :email',
            ['email' => $email]
        );
    }

    public function get_by_username($username)
    {
        return $this->query_unique(
            'SELECT * FROM ' . $this->table_name . ' WHERE username = :username',
            ['username' => $username]
        );
    }

    public function add_user($params)
    {
        return $this->add($params);
    }

    public function edit_user($params, $id)
    {
        return $this->update($params, $id);
    }

    /** Deleting a user also deletes their collection (ON DELETE CASCADE). */
    public function delete_user($id)
    {
        $this->delete($id);
    }
}
