<?php

use OpenApi\Attributes as OA;

/*
 * OpenAPI documentation of rest/routes/UserGameRoutes.php.
 * swagger-php reads the #[OA\...] attributes; the methods are empty, they only carry the documentation.
 * Keep it in sync with the routes: one method per endpoint.
 */
class UserGamePaths
{
    #[OA\Get(
        path: '/user-games',
        tags: ['collection'],
        summary: 'Your collection (library and wishlist), optionally filtered',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'status',
                in: 'query',
                required: false,
                description: 'Only entries with this status',
                example: 'Playing',
                schema: new OA\Schema(type: 'string', enum: ['Playing', 'Completed', 'Want to play'])
            ),
            new OA\Parameter(
                name: 'search',
                in: 'query',
                required: false,
                description: 'Text in the game title (2-50 characters)',
                example: 'zelda',
                schema: new OA\Schema(type: 'string')
            ),
            new OA\Parameter(
                name: 'user_id',
                in: 'query',
                required: false,
                description: 'Admin only: the collection of this user',
                example: 2,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Collection entries',
                content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/UserGame'))
            ),
            new OA\Response(
                response: 400,
                description: 'Invalid status, search text or user_id',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            ),
            new OA\Response(
                response: 401,
                description: 'Not logged in (missing, invalid or expired token)',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            ),
            new OA\Response(
                response: 403,
                description: 'Another user\'s collection',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            ),
            new OA\Response(
                response: 500,
                description: 'Unexpected server error',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            )
        ]
    )]
    public function getUserGames() {}

    #[OA\Get(
        path: '/user-games/stats',
        tags: ['collection'],
        summary: 'Dashboard numbers: entries per status and games per genre',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'user_id',
                in: 'query',
                required: false,
                description: 'Admin only: statistics of this user',
                example: 2,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Statistics',
                content: new OA\JsonContent(ref: '#/components/schemas/CollectionStats')
            ),
            new OA\Response(
                response: 400,
                description: 'Invalid data or a broken business rule',
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
    public function getUserGamesStats() {}

    #[OA\Get(
        path: '/user-games/{id}',
        tags: ['collection'],
        summary: 'Get one collection entry',
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
                description: 'The entry',
                content: new OA\JsonContent(ref: '#/components/schemas/UserGame')
            ),
            new OA\Response(
                response: 401,
                description: 'Not logged in (missing, invalid or expired token)',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            ),
            new OA\Response(
                response: 403,
                description: 'Another user\'s entry',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            ),
            new OA\Response(
                response: 404,
                description: 'Collection entry not found',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            ),
            new OA\Response(
                response: 500,
                description: 'Unexpected server error',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            )
        ]
    )]
    public function getUserGamesById() {}

    #[OA\Post(
        path: '/user-games',
        tags: ['collection'],
        summary: 'Add a catalog game to your collection or wishlist',
        description: 'The status defaults to \'Want to play\'. Rules: rating 1-5; a \'Want to play\' game cannot have a rating; an unreleased game can only be \'Want to play\'; one entry per game and platform.',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/UserGameInput')
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Created entry',
                content: new OA\JsonContent(ref: '#/components/schemas/UserGame')
            ),
            new OA\Response(
                response: 400,
                description: 'Unknown game or platform, bad status or rating, a wishlist game with a rating, an unreleased game that is not on the wishlist, or the game is already in the collection on that platform',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            ),
            new OA\Response(
                response: 401,
                description: 'Not logged in (missing, invalid or expired token)',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            ),
            new OA\Response(
                response: 403,
                description: 'user_id of another user (non-admin)',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            ),
            new OA\Response(
                response: 500,
                description: 'Unexpected server error',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            )
        ]
    )]
    public function postUserGames() {}

    #[OA\Put(
        path: '/user-games/{id}',
        tags: ['collection'],
        summary: 'Change the status, rating or platform of an entry',
        description: 'Only the fields that are sent change. Moving a game back to \'Want to play\' removes its rating.',
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
            content: new OA\JsonContent(ref: '#/components/schemas/UserGameUpdateInput')
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Updated entry',
                content: new OA\JsonContent(ref: '#/components/schemas/UserGame')
            ),
            new OA\Response(
                response: 400,
                description: 'Same rules as POST; nothing to update',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            ),
            new OA\Response(
                response: 401,
                description: 'Not logged in (missing, invalid or expired token)',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            ),
            new OA\Response(
                response: 403,
                description: 'Another user\'s entry',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            ),
            new OA\Response(
                response: 404,
                description: 'Collection entry not found',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            ),
            new OA\Response(
                response: 500,
                description: 'Unexpected server error',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            )
        ]
    )]
    public function putUserGamesById() {}

    #[OA\Delete(
        path: '/user-games/{id}',
        tags: ['collection'],
        summary: 'Remove an entry from the collection',
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
                description: 'Entry removed',
                content: new OA\JsonContent(ref: '#/components/schemas/Message')
            ),
            new OA\Response(
                response: 401,
                description: 'Not logged in (missing, invalid or expired token)',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            ),
            new OA\Response(
                response: 403,
                description: 'Another user\'s entry',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            ),
            new OA\Response(
                response: 404,
                description: 'Collection entry not found',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            ),
            new OA\Response(
                response: 500,
                description: 'Unexpected server error',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            )
        ]
    )]
    public function deleteUserGamesById() {}
}
