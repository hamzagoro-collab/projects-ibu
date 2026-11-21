<?php

/* Documentation: public/v1/docs/paths/GenrePaths.php */

Flight::route('GET /genres', function () {
    Flight::auth_middleware()->authenticate();
    Flight::json(Flight::genre_service()->get_all());
});

Flight::route('GET /genres/@id:[0-9]+', function ($id) {
    Flight::auth_middleware()->authenticate();
    Flight::json(Flight::genre_service()->get_by_id($id));
});

Flight::route('POST /genres', function () {
    Flight::auth_middleware()->require_role(Roles::ADMIN);
    $data = Flight::request()->data->getData();
    Flight::json(Flight::genre_service()->add($data), 201);
});

Flight::route('PUT /genres/@id:[0-9]+', function ($id) {
    Flight::auth_middleware()->require_role(Roles::ADMIN);
    $data = Flight::request()->data->getData();
    Flight::json(Flight::genre_service()->update($data, $id));
});

Flight::route('DELETE /genres/@id:[0-9]+', function ($id) {
    Flight::auth_middleware()->require_role(Roles::ADMIN);
    Flight::genre_service()->delete($id);
    Flight::json(['message' => 'Genre deleted.']);
});

// the games of one genre
Flight::route('GET /genres/@id:[0-9]+/games', function ($id) {
    Flight::auth_middleware()->authenticate();
    Flight::json(Flight::game_service()->get_by_genre($id));
});
