<?php

require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../../utils/Roles.php';
require_once __DIR__ . '/../dao/UserDao.php';

class UserService extends BaseService
{
    public function __construct(UserDao $dao = new UserDao())
    {
        parent::__construct($dao, 'User');
    }

    /** Allow-list; every value is trimmed except the password (spaces may be part of it). */
    private function clean(array $data, array $fields): array
    {
        $user = array_intersect_key($data, array_flip($fields));
        foreach ($user as $field => $value) {
            if ($field !== 'password' && is_string($value)) {
                $user[$field] = trim($value);
            }
        }
        return $user;
    }

    /** The password hash never leaves the service. */
    private function without_password(array $user): array
    {
        unset($user['password_hash']);
        return $user;
    }

    public function get_all(): array
    {
        return array_map([$this, 'without_password'], $this->dao->get_all());
    }

    public function get_by_id($id): array
    {
        return $this->without_password(parent::get_by_id($id));
    }

    /**
     * Create an account. Only callers that pass $allow_role = true (admins) can
     * choose the role; everybody else becomes a 'user'.
     */
    public function add(array $data, bool $allow_role = false): array
    {
        $allowed = ['username', 'email', 'password'];
        if ($allow_role) {
            $allowed[] = 'role';
        }
        $user = $this->clean($data, $allowed);
        $user['role'] = $user['role'] ?? Roles::USER;

        $this->validate($user);
        Validator::required($user, ['password']);
        Validator::password($user['password']);           // check BEFORE hashing

        $user['password_hash'] = password_hash($user['password'], PASSWORD_BCRYPT);
        unset($user['password']);
        $user['email'] = strtolower($user['email']);

        $created = $this->dao->add($user);
        return $this->get_by_id($created['user_id']);
    }

    /**
     * Users edit themselves, admins edit everybody. Username and email must be
     * sent; password and role are optional (only admins can change roles).
     */
    public function update(array $data, $id, ?array $actor = null): array
    {
        $current = parent::get_by_id($id);
        $this->assert_self_or_admin($actor, (int) $id, 'You can only edit your own account.');
        $is_admin = ($actor['role'] ?? null) === Roles::ADMIN;

        $user = $this->clean($data, ['username', 'email', 'password', 'role']);
        if (array_key_exists('role', $user) && !$is_admin) {
            throw new ForbiddenException('Only an administrator can change roles.');
        }
        $this->validate($user, (int) $id);

        if (array_key_exists('password', $user)) {
            Validator::password($user['password']);
            $user['password_hash'] = password_hash($user['password'], PASSWORD_BCRYPT);
            unset($user['password']);
        }
        $user['email'] = strtolower($user['email']);
        if (isset($user['role']) && $user['role'] !== Roles::ADMIN && $current['role'] === Roles::ADMIN) {
            $this->assert_not_last_admin();
        }

        $this->dao->update($user, $id);
        return $this->get_by_id($id);
    }

    public function delete($id, ?array $actor = null): void
    {
        $current = parent::get_by_id($id);
        $this->assert_self_or_admin($actor, (int) $id, 'You can only delete your own account.');
        if ($current['role'] === Roles::ADMIN) {
            $this->assert_not_last_admin();
        }
        $this->dao->delete($id);
    }

    protected function validate(array $user, ?int $id = null): void
    {
        Validator::required($user, ['username', 'email']);
        Validator::length($user['username'], 'username', 3, 50);
        if (!preg_match('/^[A-Za-z0-9_]+$/', $user['username'])) {
            throw new ValidationException("Field 'username' may only contain letters, digits and underscores.");
        }
        Validator::length($user['email'], 'email', 5, 120);
        Validator::email($user['email']);
        if (isset($user['role'])) {
            Validator::one_of($user['role'], 'role', Roles::all());
        }

        $by_name = $this->dao->get_by_username($user['username']);
        if ($by_name && (int) $by_name['user_id'] !== (int) $id) {
            throw new ValidationException("Username '{$user['username']}' is already taken.");
        }
        $by_email = $this->dao->get_by_email(strtolower($user['email']));
        if ($by_email && (int) $by_email['user_id'] !== (int) $id) {
            throw new ValidationException('An account with this email already exists.');
        }
    }

    private function assert_self_or_admin(?array $actor, int $id, string $message): void
    {
        if ($actor === null || ($actor['role'] !== Roles::ADMIN && (int) $actor['user_id'] !== $id)) {
            throw new ForbiddenException($message);
        }
    }

    private function assert_not_last_admin(): void
    {
        $admins = array_filter($this->dao->get_all(), fn($u) => $u['role'] === Roles::ADMIN);
        if (count($admins) <= 1) {
            throw new ValidationException('The last administrator cannot be deleted or demoted.');
        }
    }
}
