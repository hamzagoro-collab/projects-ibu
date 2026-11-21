<?php
/**
 * GameVault API front controller: every request enters here.
 *
 *   composer install
 *   cp config/config.example.php config/config.php      (then edit it)
 *   php -S localhost:8000 server.php                      (in the project's main folder)
 *
 * API:     http://localhost:8000/backend/
 * Swagger: http://localhost:8000/backend/public/v1/docs/
 */

require __DIR__ . '/vendor/autoload.php';

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/utils/exceptions.php';
require_once __DIR__ . '/utils/Validator.php';
require_once __DIR__ . '/utils/Roles.php';
require_once __DIR__ . '/middleware/AuthMiddleware.php';
require_once __DIR__ . '/rest/services/AuthService.php';
require_once __DIR__ . '/rest/services/UserService.php';
require_once __DIR__ . '/rest/services/GenreService.php';
require_once __DIR__ . '/rest/services/PlatformService.php';
require_once __DIR__ . '/rest/services/GameService.php';
require_once __DIR__ . '/rest/services/UserGameService.php';
require_once __DIR__ . '/rest/services/RawgService.php';

/* The services (and the middleware) are created when they are first used. */
Flight::register('auth_service', 'AuthService');
Flight::register('user_service', 'UserService');
Flight::register('genre_service', 'GenreService');
Flight::register('platform_service', 'PlatformService');
Flight::register('game_service', 'GameService');
Flight::register('user_game_service', 'UserGameService');
Flight::register('rawg_service', 'RawgService');
Flight::register('auth_middleware', 'AuthMiddleware');

/* CORS: the frontend may run on another port. */
header('Access-Control-Allow-Origin: ' . Config::CORS_ORIGIN());
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
    http_response_code(204);
    exit;
}

/*
 * One handler for every exception that a route or a service throws:
 * - HttpException (ValidationException, NotFoundException, ...): its status and its message are safe to show
 * - anything else (PDOException, a bug): a general 500; the details go only to the server log
 */
Flight::map('error', function (Throwable $e) {
    if ($e instanceof HttpException) {
        $status = $e->get_status();
        $message = $e->getMessage();
    } else {
        $status = 500;
        $message = 'Something went wrong on the server. Please try again later.';
        error_log($e);
    }
    Flight::response()
        ->clearBody()
        ->status($status)
        ->header('Content-Type', 'application/json')
        ->write(json_encode(['status' => $status, 'error' => $message]))
        ->send();
});

/* A URL that matches no route: say which endpoint is missing. */
Flight::map('notFound', function () {
    $request = Flight::request();
    $path = strtok($request->url, '?');
    $message = "Endpoint {$request->method} $path does not exist.";
    Flight::response()
        ->clearBody()
        ->status(404)
        ->header('Content-Type', 'application/json')
        ->write(json_encode(['status' => 404, 'error' => $message]))
        ->send();
});

Flight::route('GET /', function () {
    Flight::json(['message' => 'The backend is running.']);
});

require_once __DIR__ . '/rest/routes/AuthRoutes.php';
require_once __DIR__ . '/rest/routes/UserRoutes.php';
require_once __DIR__ . '/rest/routes/GenreRoutes.php';
require_once __DIR__ . '/rest/routes/PlatformRoutes.php';
require_once __DIR__ . '/rest/routes/GameRoutes.php';
require_once __DIR__ . '/rest/routes/UserGameRoutes.php';
require_once __DIR__ . '/rest/routes/RawgRoutes.php';

Flight::start();
