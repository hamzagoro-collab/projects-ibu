<?php

use OpenApi\Attributes as OA;

/*
 * OpenAPI documentation of rest/routes/RawgRoutes.php.
 * swagger-php reads the #[OA\...] attributes; the methods are empty, they only carry the documentation.
 * Keep it in sync with the routes: one method per endpoint.
 */
class RawgPaths
{
    #[OA\Get(
        path: '/external/games',
        tags: ['external'],
        summary: 'Search the RAWG video games database by title',
        description: 'Our backend calls RAWG with the API key from config.php, so the key never reaches the browser. A request to RAWG is cancelled after a few seconds: timeouts, no connection, a rejected key and rate limits all answer 503.',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'search',
                in: 'query',
                required: true,
                description: 'Title to search for (2-100 characters)',
                example: 'Hades',
                schema: new OA\Schema(type: 'string')
            ),
            new OA\Parameter(
                name: 'page',
                in: 'query',
                required: false,
                description: 'Page number (default 1)',
                example: 1,
                schema: new OA\Schema(type: 'integer')
            ),
            new OA\Parameter(
                name: 'page_size',
                in: 'query',
                required: false,
                description: 'Results per page, 1-40 (default 10)',
                example: 10,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Search results',
                content: new OA\JsonContent(ref: '#/components/schemas/ExternalSearchResult')
            ),
            new OA\Response(
                response: 400,
                description: 'Search text too short or invalid page',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            ),
            new OA\Response(
                response: 401,
                description: 'Not logged in (missing, invalid or expired token)',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            ),
            new OA\Response(
                response: 500,
                description: 'Unexpected server error',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            ),
            new OA\Response(
                response: 503,
                description: 'RAWG is not available (timeout, no connection, rejected key or rate limit)',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            )
        ]
    )]
    public function getExternalGames() {}

    #[OA\Get(
        path: '/external/games/{id}',
        tags: ['external'],
        summary: 'Details of one RAWG game',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                example: 3498,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'The game',
                content: new OA\JsonContent(ref: '#/components/schemas/ExternalGame')
            ),
            new OA\Response(
                response: 401,
                description: 'Not logged in (missing, invalid or expired token)',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            ),
            new OA\Response(
                response: 404,
                description: 'Game not found in RAWG',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            ),
            new OA\Response(
                response: 500,
                description: 'Unexpected server error',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            ),
            new OA\Response(
                response: 503,
                description: 'RAWG is not available (timeout, no connection, rejected key or rate limit)',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            )
        ]
    )]
    public function getExternalGamesById() {}
}
