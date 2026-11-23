<?php

require_once __DIR__ . '/UserService.php';
require_once __DIR__ . '/../dao/UserDao.php';
require_once __DIR__ . '/../../config/config.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

/** Sign-up, login and JWT handling. */
class AuthService
{
    public function __construct(
        private UserDao $user_dao = new UserDao(),
        private UserService $user_service = new UserService()
    ) {}

    /** Public sign-up: the role is always 'user'. */
    public function register(array $data): array
    {
        $user = $this->user_service->add($data, false);
        return ['token' => $this->generate_token($user), 'user' => $user];
    }

    public function login(array $data): array
    {
        Validator::required($data, ['email', 'password']);
        $row = $this->user_dao->get_by_email(strtolower(trim((string) $data['email'])));
        if (!$row || !is_string($data['password']) || !password_verify($data['password'], $row['password_hash'])) {
            throw new UnauthorizedException('Invalid email or password.');
        }
        unset($row['password_hash']);
        return ['token' => $this->generate_token($row), 'user' => $row];
    }

    private function generate_token(array $user): string
    {
        $now = time();
        return JWT::encode(
            ['iat' => $now, 'exp' => $now + Config::JWT_EXPIRY(), 'sub' => (int) $user['user_id']],
            Config::JWT_SECRET(),
            'HS256'
        );
    }

    /**
     * Checks the token and returns the CURRENT user from the database, so a
     * deleted user or a changed role takes effect immediately.
     */
    public function verify_token(string $token): array
    {
        try {
            $payload = JWT::decode($token, new Key(Config::JWT_SECRET(), 'HS256'));
        } catch (\Firebase\JWT\ExpiredException $e) {
            throw new UnauthorizedException('Your session has expired. Please log in again.');
        } catch (\Throwable $e) {
            throw new UnauthorizedException('Invalid token.');
        }
        $row = $this->user_dao->get_by_id($payload->sub ?? 0);
        if (!$row) {
            throw new UnauthorizedException('Invalid token: this user no longer exists.');
        }
        unset($row['password_hash']);
        return $row;
    }
}
