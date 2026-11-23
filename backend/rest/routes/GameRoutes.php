<?php

/* Documentation: public/v1/docs/paths/GamePaths.php */

// ?search=witcher and/or ?genre_id=2; without parameters the whole catalog
Flight::route('GET /games', function () {
    Flight::auth_middleware()->authenticate();
    $query = Flight::request()->query;
    $filters = array_intersect_key($query->getData(), array_flip(['search', 'genre_id']));
    if (count($filters) > 0) {
        Flight::json(Flight::game_service()->filter($filters));
        return;
    }
    Flight::json(Flight::game_service()->get_all());
});

Flight::route('GET /games/@id:[0-9]+', function ($id) {
    Flight::auth_middleware()->authenticate();
    Flight::json(Flight::game_service()->get_by_id($id));
});

Flight::route('POST /games', function () {
    Flight::auth_middleware()->require_role(Roles::ADMIN);
    $data = Flight::request()->data->getData();
    Flight::json(Flight::game_service()->add($data), 201);
});

Flight::route('PUT /games/@id:[0-9]+', function ($id) {
    Flight::auth_middleware()->require_role(Roles::ADMIN);
    $data = Flight::request()->data->getData();
    Flight::json(Flight::game_service()->update($data, $id));
});

Flight::route('DELETE /games/@id:[0-9]+', function ($id) {
    Flight::auth_middleware()->require_role(Roles::ADMIN);
    Flight::game_service()->delete($id);
    Flight::json(['message' => 'Game deleted.']);
});
