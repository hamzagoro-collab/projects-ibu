<?php

/**
 * Configuration for GameVault.
 *
 * Copy this file to config.php (git-ignored) and put your own MySQL password,
 * JWT secret and RAWG API key into it. Environment variables with the same
 * names override the values below. NEVER commit config.php.
 */
class Config
{
    /* ---------------- Database ---------------- */

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

    /* ---------------- Authentication (JWT) ---------------- */

    /** Secret used to sign login tokens. MUST be changed outside local development. */
    public static function JWT_SECRET()
    {
        return Config::get_env("JWT_SECRET", "gamevault-dev-secret-change-me-before-deploying-0123456789");
    }

    /** Token lifetime in seconds (default: 24 hours). */
    public static function JWT_EXPIRY()
    {
        return (int) Config::get_env("JWT_EXPIRY", 86400);
    }

    /* ---------------- External API (RAWG Video Games Database) ---------------- */

    /** Free key from https://rawg.io/apidocs . Keep the real value in the environment or config/local.php. */
    public static function RAWG_API_KEY()
    {
        return Config::get_env("RAWG_API_KEY", "YOUR_RAWG_API_KEY");
    }

    public static function RAWG_BASE_URL()
    {
        return Config::get_env("RAWG_BASE_URL", "https://api.rawg.io/api");
    }

    /** Maximum seconds to wait for the whole external request. */
    public static function RAWG_TIMEOUT()
    {
        return (int) Config::get_env("RAWG_TIMEOUT", 5);
    }

    /** Maximum seconds to wait while connecting to the external server. */
    public static function RAWG_CONNECT_TIMEOUT()
    {
        return (int) Config::get_env("RAWG_CONNECT_TIMEOUT", 3);
    }

    /* ---------------- HTTP ---------------- */

    /** Origin allowed to call the API from a browser (CORS). */
    public static function CORS_ORIGIN()
    {
        return Config::get_env("CORS_ORIGIN", "*");
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
