<?php

require_once __DIR__ . '/../utils/exceptions.php';
require_once __DIR__ . '/../utils/Roles.php';

/**
 * Login (401) and role (403) checks. Routes call these before they call a
 * service; a failed check is an exception that the error handler in
 * index.php answers.
 */
class AuthMiddleware
{
    /** Needs "Authorization: Bearer <jwt>"; returns the current user. */
    public function authenticate(): array
    {
        $header = $this->authorization_header();
        if (!$header || !preg_match('/^Bearer\s+(\S+)$/i', $header, $matches)) {
            throw new UnauthorizedException('Missing or malformed Authorization header. Expected: Bearer <token>.');
        }
        return Flight::auth_service()->verify_token($matches[1]);
    }

    /** Like authenticate(), but the user must have the given role. */
    public function require_role(string $role): array
    {
        $user = $this->authenticate();
        if ($user['role'] !== $role) {
            throw new ForbiddenException('You do not have permission to do this.');
        }
        return $user;
    }

    private function authorization_header(): ?string
    {
        $header = Flight::request()->getHeader('Authorization');
        if (!empty($header)) {
            return $header;
        }
        // Apache sometimes hides the header from PHP; .htaccess copies it into one of these
        return $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? null;
    }
}
