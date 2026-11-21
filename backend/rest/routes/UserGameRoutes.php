<?php

/* Documentation: public/v1/docs/paths/UserGamePaths.php */

// ?status=Playing, ?search=zelda, ?user_id=2 (admin only)
Flight::route('GET /user-games', function () {
    $actor = Flight::auth_middleware()->authenticate();
    $query = Flight::request()->query;
    Flight::json(Flight::user_game_service()->list_for(
        $actor,
        $query['user_id'] ?? null,
        $query['status'] ?? null,
        $query['search'] ?? null
    ));
});

Flight::route('GET /user-games/stats', function () {
    $actor = Flight::auth_middleware()->authenticate();
    Flight::json(Flight::user_game_service()->stats($actor, Flight::request()->query['user_id'] ?? null));
});

Flight::route('GET /user-games/@id:[0-9]+', function ($id) {
    $actor = Flight::auth_middleware()->authenticate();
    Flight::json(Flight::user_game_service()->get_entry($id, $actor));
});

Flight::route('POST /user-games', function () {
    $actor = Flight::auth_middleware()->authenticate();
    $data = Flight::request()->data->getData();
    Flight::json(Flight::user_game_service()->add_entry($data, $actor), 201);
});

Flight::route('PUT /user-games/@id:[0-9]+', function ($id) {
    $actor = Flight::auth_middleware()->authenticate();
    $data = Flight::request()->data->getData();
    Flight::json(Flight::user_game_service()->update_entry($data, $id, $actor));
});

Flight::route('DELETE /user-games/@id:[0-9]+', function ($id) {
    $actor = Flight::auth_middleware()->authenticate();
    Flight::user_game_service()->delete_entry($id, $actor);
    Flight::json(['message' => 'Collection entry removed.']);
});
