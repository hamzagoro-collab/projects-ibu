<?php

/* Documentation: public/v1/docs/paths/UserPaths.php */

Flight::route('GET /users', function () {
    Flight::auth_middleware()->require_role(Roles::ADMIN);
    Flight::json(Flight::user_service()->get_all());
});

Flight::route('GET /users/@id:[0-9]+', function ($id) {
    $actor = Flight::auth_middleware()->authenticate();
    if ($actor['role'] !== Roles::ADMIN && (int) $actor['user_id'] !== (int) $id) {
        throw new ForbiddenException('You can only view your own account.');
    }
    Flight::json(Flight::user_service()->get_by_id($id));
});

Flight::route('POST /users', function () {
    Flight::auth_middleware()->require_role(Roles::ADMIN);
    $data = Flight::request()->data->getData();
    Flight::json(Flight::user_service()->add($data, true), 201);
});

Flight::route('PUT /users/@id:[0-9]+', function ($id) {
    $actor = Flight::auth_middleware()->authenticate();
    $data = Flight::request()->data->getData();
    Flight::json(Flight::user_service()->update($data, $id, $actor));
});

Flight::route('DELETE /users/@id:[0-9]+', function ($id) {
    $actor = Flight::auth_middleware()->authenticate();
    Flight::user_service()->delete($id, $actor);
    Flight::json(['message' => 'User deleted.']);
});
