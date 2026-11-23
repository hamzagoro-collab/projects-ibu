# GameVault backend

PHP REST API (FlightPHP) in three layers, following the structure of the course labs 04-06:

```
project/
├── server.php                      router for PHP's built-in web server
└── backend/
    ├── index.php                   front controller: services, error handler, routes
    ├── composer.json               flightphp/core, firebase/php-jwt, zircote/swagger-php
    ├── .htaccess                   Apache (XAMPP): every request goes to index.php
    ├── config/config.example.php   copy to config.php (git-ignored) and edit
    ├── utils/                      exceptions.php, Validator.php, Roles.php
    ├── middleware/AuthMiddleware.php   401 / 403 checks
    ├── rest/
    │   ├── dao/                    data access (the only code that talks to MySQL)
    │   ├── services/               business logic + validation (the only code that calls the DAOs)
    │   └── routes/                 presentation layer: one *Routes.php per entity
    ├── public/v1/docs/             OpenAPI + Swagger UI
    │   ├── index.html              Swagger UI
    │   ├── swagger.php             generates the OpenAPI JSON
    │   ├── doc_setup.php           info, security scheme, schemas
    │   └── paths/*Paths.php        one class per routes file (PHP 8 attributes)
    └── tests/tests.php             manual DAO test script
```

## Setup

1. Import `game_collection_manager.sql` (it now has a `users.role` column; for an existing database run
   `ALTER TABLE users ADD COLUMN role ENUM('user','admin') NOT NULL DEFAULT 'user' AFTER password_hash;`
   `UPDATE users SET role='admin' WHERE user_id=1;`).
2. `cd backend && composer install` (PHP 8.2 or newer, with the curl, mbstring and pdo_mysql extensions).
3. `cp config/config.example.php config/config.php` and edit it: MySQL password/port, `JWT_SECRET`,
   and `RAWG_API_KEY` (free key from https://rawg.io/apidocs). Never commit `config.php`.
4. In the main project folder: `php -S localhost:8000 server.php`
   - API: http://localhost:8000/backend/ (`GET /` answers "The backend is running.")
   - Swagger UI: http://localhost:8000/backend/public/v1/docs/
   - XAMPP instead: copy the folder to `htdocs`; the base URL is `http://localhost/<folder>/backend`.

Demo logins (password `password`): `player1@example.com` (admin), `player2@example.com` (user).

## Layers

**Business logic (`rest/services`)**: one service per entity (`UserService`, `GenreService`, `PlatformService`,
`GameService`, `UserGameService`) plus `AuthService` and `RawgService`. All extend `BaseService`
(`get_all`, `get_by_id`, `add`, `update`, `delete`, a `validate()` hook and an allow-list `$fields`).
Services contain no SQL and no HTTP: they report problems by throwing `ValidationException` (400),
`UnauthorizedException` (401), `ForbiddenException` (403), `NotFoundException` (404) or
`ServiceUnavailableException` (503).

Rules: unique usernames, emails, genre/platform names and game titles; valid email and dates; password 8-72
characters with a letter and a number, hashed with bcrypt; a genre that still has games and a platform that is
still used cannot be deleted; collection rating 1-5, no rating for "Want to play", unreleased games only on the
wishlist, one entry per user + game + platform; users touch only their own collection and account; the last admin
cannot be deleted or demoted.

**Presentation (`rest/routes`)**: routes read the request, check login/role with `AuthMiddleware`, call one service
method and answer `Flight::json(...)` (201 for POST, 200 otherwise). There is no try/catch: the error handler in
`index.php` answers every exception as `{"status": 404, "error": "Game not found."}` (unexpected errors: general 500,
details only in the server log; unknown URLs: 404 `Endpoint GET /x does not exist.`).

| Area | Endpoints | Access |
|---|---|---|
| auth | `POST /auth/register`, `POST /auth/login`, `GET /auth/me` | public / logged in |
| users | `GET,POST /users`, `GET,PUT,DELETE /users/{id}` | list/create: admin; others: yourself or admin |
| genres | `GET,POST /genres`, `GET,PUT,DELETE /genres/{id}`, `GET /genres/{id}/games` | read: logged in; write: admin |
| platforms | `GET,POST /platforms`, `GET,PUT,DELETE /platforms/{id}` | read: logged in; write: admin |
| games | `GET,POST /games` (`?search=`, `?genre_id=`), `GET,PUT,DELETE /games/{id}` | read: logged in; write: admin |
| collection | `GET,POST /user-games` (`?status=`, `?search=`), `GET /user-games/stats`, `GET,PUT,DELETE /user-games/{id}` | own entries (admin: all) |
| external | `GET /external/games?search=`, `GET /external/games/{id}` | logged in |

Send `Authorization: Bearer <token>` (the token comes from `POST /auth/login`). PUT: required fields must be sent
again, optional fields that are left out stay unchanged.

**OpenAPI (`public/v1/docs`)**: every endpoint is documented with PHP 8 attributes in `paths/*Paths.php`
(`{id}` in the path, request bodies, every status the route can answer, bearer security). Open the Swagger UI,
call `POST /auth/login`, click **Authorize**, paste the token and use "Try it out".

## External API (RAWG)

`GET /external/games?search=hades` calls `https://api.rawg.io/api/games` through `RawgService`; the browser never
sees the key (`RAWG_API_KEY` in `config/config.php`). cURL uses a connect timeout (3 s) and a total timeout (5 s),
both set in the config. Answers: 200 result, 400 search text shorter than 2 characters or bad page, 404 unknown
game (details endpoint), 503 RAWG not available (timeout, no connection, rejected key, rate limit, invalid answer;
the technical reason is only in the server log), 500 when `RAWG_API_KEY` is not configured.

## Quick check with curl

```
curl -X POST localhost:8000/backend/auth/login -H "Content-Type: application/json" \
     -d '{"email":"player1@example.com","password":"password"}'
curl localhost:8000/backend/games -H "Authorization: Bearer <token>"
curl -i localhost:8000/backend/games/999 -H "Authorization: Bearer <token>"      # 404
curl -i localhost:8000/backend/games                                             # 401
```
