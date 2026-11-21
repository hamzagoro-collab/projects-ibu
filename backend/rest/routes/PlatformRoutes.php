<?php

/* Documentation: public/v1/docs/paths/PlatformPaths.php */

Flight::route('GET /platforms', function () {
    Flight::auth_middleware()->authenticate();
    Flight::json(Flight::platform_service()->get_all());
});

Flight::route('GET /platforms/@id:[0-9]+', function ($id) {
    Flight::auth_middleware()->authenticate();
    Flight::json(Flight::platform_service()->get_by_id($id));
});

Flight::route('POST /platforms', function () {
    Flight::auth_middleware()->require_role(Roles::ADMIN);
    $data = Flight::request()->data->getData();
    Flight::json(Flight::platform_service()->add($data), 201);
});

Flight::route('PUT /platforms/@id:[0-9]+', function ($id) {
    Flight::auth_middleware()->require_role(Roles::ADMIN);
    $data = Flight::request()->data->getData();
    Flight::json(Flight::platform_service()->update($data, $id));
});

Flight::route('DELETE /platforms/@id:[0-9]+', function ($id) {
    Flight::auth_middleware()->require_role(Roles::ADMIN);
    Flight::platform_service()->delete($id);
    Flight::json(['message' => 'Platform deleted.']);
});
