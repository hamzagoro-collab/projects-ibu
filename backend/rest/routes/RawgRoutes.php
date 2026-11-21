<?php

/* Documentation: public/v1/docs/paths/RawgPaths.php */

// External API (RAWG): the browser calls OUR endpoint, the API key stays on the server
Flight::route('GET /external/games', function () {
    Flight::auth_middleware()->authenticate();
    $query = Flight::request()->query;
    Flight::json(Flight::rawg_service()->search(
        $query['search'] ?? '',
        $query['page'] ?? 1,
        $query['page_size'] ?? 10
    ));
});

Flight::route('GET /external/games/@id:[0-9]+', function ($id) {
    Flight::auth_middleware()->authenticate();
    Flight::json(Flight::rawg_service()->get_game($id));
});
