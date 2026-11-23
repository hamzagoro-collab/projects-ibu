<?php

/* Documentation: public/v1/docs/paths/AuthPaths.php */

Flight::route('POST /auth/register', function () {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::auth_service()->register($data), 201);
});

Flight::route('POST /auth/login', function () {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::auth_service()->login($data));
});

Flight::route('GET /auth/me', function () {
    Flight::json(Flight::auth_middleware()->authenticate());
});
