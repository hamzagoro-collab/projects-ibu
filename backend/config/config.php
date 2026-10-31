<?php

/**
 * Database configuration for GameVault.
 *
 * Values are read from environment variables when they are set, otherwise the
 * local development defaults below are used. Never commit real passwords:
 * set them through the environment (or a .env file, which is git-ignored).
 */
class Config
{
    public static function DB_NAME()
    {
        return Config::get_env("DB_NAME", "gamevault");
    }

    public static function DB_PORT()
    {
        return Config::get_env("DB_PORT", 3306);
    }

    public static function DB_USER()
    {
        return Config::get_env("DB_USER", "root");
    }

    public static function DB_PASSWORD()
    {
        return Config::get_env("DB_PASSWORD", "");
    }

    public static function DB_HOST()
    {
        return Config::get_env("DB_HOST", "127.0.0.1");
    }

    public static function get_env($name, $default)
    {
        $value = getenv($name);
        if ($value === false && isset($_ENV[$name])) {
            $value = $_ENV[$name];
        }
        return $value !== false && trim($value) != "" ? $value : $default;
    }
}
