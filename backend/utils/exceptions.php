<?php

/**
 * Exceptions that carry an HTTP status code.
 * Services throw them; the error handler in index.php turns them into
 * {"status": 404, "error": "..."} answers. Anything that is NOT an
 * HttpException (PDOException, a bug) is answered with a general 500.
 */
class HttpException extends Exception
{
    private int $status;

    public function __construct(string $message, int $status = 400)
    {
        parent::__construct($message, $status);
        $this->status = $status;
    }

    public function get_status(): int
    {
        return $this->status;
    }
}

/** Input is missing or invalid, or a business rule is broken. */
class ValidationException extends HttpException
{
    public function __construct(string $message)
    {
        parent::__construct($message, 400);
    }
}

/** The client is not logged in (no, wrong or expired token / wrong credentials). */
class UnauthorizedException extends HttpException
{
    public function __construct(string $message = 'You must be logged in.')
    {
        parent::__construct($message, 401);
    }
}

/** The client is logged in but is not allowed to do this. */
class ForbiddenException extends HttpException
{
    public function __construct(string $message = 'You are not allowed to do this.')
    {
        parent::__construct($message, 403);
    }
}

/** The requested object does not exist. */
class NotFoundException extends HttpException
{
    public function __construct(string $message)
    {
        parent::__construct($message, 404);
    }
}

/** An external service the API depends on (RAWG) is not available. */
class ServiceUnavailableException extends HttpException
{
    public function __construct(string $message)
    {
        parent::__construct($message, 503);
    }
}
