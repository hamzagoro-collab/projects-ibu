<?php

use OpenApi\Attributes as OA;

/*
 * OpenAPI documentation of rest/routes/GameRoutes.php.
 * swagger-php reads the #[OA\...] attributes; the methods are empty, they only carry the documentation.
 * Keep it in sync with the routes: one method per endpoint.
 */
class GamePaths
{
    #[OA\Get(
        path: '/games',
        tags: ['games'],
        summary: 'List, search or filter the game catalog',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'search',
                in: 'query',
                required: false,
                description: 'Text in the title (2-50 characters)',
                example: 'witcher',
                schema: new OA\Schema(type: 'string')
            ),
            new OA\Parameter(
                name: 'genre_id',
                in: 'query',
                required: false,
                description: 'Only the games of this genre',
                example: 2,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Matching games',
                content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Game'))
            ),
            new OA\Response(
                response: 400,
                description: 'Invalid search text or genre_id',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            ),
            new OA\Response(
                response: 401,
                description: 'Not logged in (missing, invalid or expired token)',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            ),
            new OA\Response(
                response: 404,
                description: 'Genre not found',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            ),
            new OA\Response(
                response: 500,
                description: 'Unexpected server error',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            )
        ]
    )]
    public function getGames() {}

    #[OA\Get(
        path: '/games/{id}',
        tags: ['games'],
        summary: 'Get one catalog game',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                example: 1,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'The game',
                content: new OA\JsonContent(ref: '#/components/schemas/Game')
            ),
            new OA\Response(
                response: 401,
                description: 'Not logged in (missing, invalid or expired token)',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            ),
            new OA\Response(
                response: 404,
                description: 'Game not found',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            ),
            new OA\Response(
                response: 500,
                description: 'Unexpected server error',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            )
        ]
    )]
    public function getGamesById() {}

    #[OA\Post(
        path: '/games',
        tags: ['games'],
        summary: 'Add a game to the catalog (admin only)',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/GameInput')
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Created game',
                content: new OA\JsonContent(ref: '#/components/schemas/Game')
            ),
            new OA\Response(
                response: 400,
                description: 'Missing title, bad date, unknown genre or the title already exists',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            ),
            new OA\Response(
                response: 401,
                description: 'Not logged in (missing, invalid or expired token)',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            ),
            new OA\Response(
                response: 403,
                description: 'Logged in, but not allowed to do this',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            ),
            new OA\Response(
                response: 500,
                description: 'Unexpected server error',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            )
        ]
    )]
    public function postGames() {}

    #[OA\Put(
        path: '/games/{id}',
        tags: ['games'],
        summary: 'Update a catalog game (admin only)',
        description: 'title must be sent; optional fields that are left out stay unchanged.',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                example: 1,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/GameInput')
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Updated game',
                content: new OA\JsonContent(ref: '#/components/schemas/Game')
            ),
            new OA\Response(
                response: 400,
                description: 'Missing title, bad date, unknown genre or the title already exists',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            ),
            new OA\Response(
                response: 401,
                description: 'Not logged in (missing, invalid or expired token)',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            ),
            new OA\Response(
                response: 403,
                description: 'Logged in, but not allowed to do this',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            ),
            new OA\Response(
                response: 404,
                description: 'Game not found',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            ),
            new OA\Response(
                response: 500,
                description: 'Unexpected server error',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            )
        ]
    )]
    public function putGamesById() {}

    #[OA\Delete(
        path: '/games/{id}',
        tags: ['games'],
        summary: 'Delete a game from the catalog (admin only)',
        description: 'The game also disappears from every user\'s collection.',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                example: 1,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Game deleted',
                content: new OA\JsonContent(ref: '#/components/schemas/Message')
            ),
            new OA\Response(
                response: 401,
                description: 'Not logged in (missing, invalid or expired token)',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            ),
            new OA\Response(
                response: 403,
                description: 'Logged in, but not allowed to do this',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            ),
            new OA\Response(
                response: 404,
                description: 'Game not found',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            ),
            new OA\Response(
                response: 500,
                description: 'Unexpected server error',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            )
        ]
    )]
    public function deleteGamesById() {}
}
