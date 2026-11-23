<?php

require_once __DIR__ . '/exceptions.php';

/**
 * Reusable validation rules. Every method throws a ValidationException (400)
 * with a message that can be shown to the client.
 */
class Validator
{
    /** Every field in $fields must be present and not empty (0 and false are allowed values). */
    public static function required(array $data, array $fields): void
    {
        foreach ($fields as $field) {
            $value = $data[$field] ?? null;
            if ($value === null || (is_string($value) && trim($value) === '')) {
                throw new ValidationException("Field '$field' is required.");
            }
        }
    }

    /** Text with $min to $max characters (after trimming). */
    public static function length($value, string $field, int $min, int $max): void
    {
        if (!is_string($value)) {
            throw new ValidationException("Field '$field' must be a string.");
        }
        $length = mb_strlen(trim($value));
        if ($length < $min || $length > $max) {
            throw new ValidationException(
                $min > 0
                    ? "Field '$field' must be between $min and $max characters long."
                    : "Field '$field' must be at most $max characters long."
            );
        }
    }

    /** Whole number, optionally within $min..$max. Returns it as int. */
    public static function integer($value, string $field, ?int $min = null, ?int $max = null): int
    {
        $number = (is_bool($value) || is_array($value)) ? false : filter_var($value, FILTER_VALIDATE_INT);
        if ($number === false) {
            throw new ValidationException("Field '$field' must be a whole number.");
        }
        if ($min !== null && $number < $min) {
            throw new ValidationException("Field '$field' must be at least $min.");
        }
        if ($max !== null && $number > $max) {
            throw new ValidationException("Field '$field' must be at most $max.");
        }
        return $number;
    }

    public static function one_of($value, string $field, array $allowed): void
    {
        if (!is_string($value) || !in_array($value, $allowed, true)) {
            throw new ValidationException("Field '$field' must be one of: " . implode(', ', $allowed) . '.');
        }
    }

    public static function email($value, string $field = 'email'): void
    {
        if (!is_string($value) || filter_var(trim($value), FILTER_VALIDATE_EMAIL) === false) {
            throw new ValidationException("Field '$field' must be a valid email address.");
        }
    }

    /** Date in the format YYYY-MM-DD that really exists (no 2026-02-31). */
    public static function date($value, string $field): void
    {
        $date = is_string($value) ? DateTime::createFromFormat('!Y-m-d', $value) : false;
        if ($date === false || $date->format('Y-m-d') !== $value) {
            throw new ValidationException("Field '$field' must be a valid date in the format YYYY-MM-DD.");
        }
    }

    /** At least 8 characters (bcrypt reads at most 72), with a letter and a number. */
    public static function password($value, string $field = 'password'): void
    {
        if (
            !is_string($value) || strlen($value) < 8 || strlen($value) > 72
            || !preg_match('/[A-Za-z]/', $value) || !preg_match('/[0-9]/', $value)
        ) {
            throw new ValidationException("Field '$field' must have 8 to 72 characters, including a letter and a number.");
        }
    }

    /** A < or > in the text could become JavaScript on a page (XSS), so such text is refused. */
    public static function no_html($value, string $field): void
    {
        if (preg_match('/[<>]/', (string) $value) === 1) {
            throw new ValidationException("Field '$field' must not contain HTML.");
        }
    }
}
