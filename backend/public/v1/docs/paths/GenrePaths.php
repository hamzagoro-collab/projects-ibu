<?php

use OpenApi\Attributes as OA;

/*
 * OpenAPI documentation of rest/routes/GenreRoutes.php.
 * swagger-php reads the #[OA\...] attributes; the methods are empty, they only carry the documentation.
 * Keep it in sync with the routes: one method per endpoint.
 */
class GenrePaths
{
    #[OA\Get(
        path: '/genres',
        tags: ['genres'],
        summary: 'List all genres',
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'All genres',
                content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Genre'))
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
            )
        ]
    )]
    public function getGenres() {}

    #[OA\Get(
        path: '/genres/{id}',
        tags: ['genres'],
        summary: 'Get one genre',
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
                description: 'The genre',
                content: new OA\JsonContent(ref: '#/components/schemas/Genre')
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
    public function getGenresById() {}

    #[OA\Post(
        path: '/genres',
        tags: ['genres'],
        summary: 'Create a genre (admin only)',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/GenreInput')
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Created genre',
                content: new OA\JsonContent(ref: '#/components/schemas/Genre')
            ),
            new OA\Response(
                response: 400,
                description: 'Missing name, bad length or the name already exists',
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
    public function postGenres() {}

    #[OA\Put(
        path: '/genres/{id}',
        tags: ['genres'],
        summary: 'Update a genre (admin only)',
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
            content: new OA\JsonContent(ref: '#/components/schemas/GenreInput')
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Updated genre',
                content: new OA\JsonContent(ref: '#/components/schemas/Genre')
            ),
            new OA\Response(
                response: 400,
                description: 'Missing name, bad length or the name already exists',
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
    public function putGenresById() {}

    #[OA\Delete(
        path: '/genres/{id}',
        tags: ['genres'],
        summary: 'Delete a genre (admin only)',
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
                description: 'Genre deleted',
                content: new OA\JsonContent(ref: '#/components/schemas/Message')
            ),
            new OA\Response(
                response: 400,
                description: 'The genre still contains games',
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
    public function deleteGenresById() {}

    #[OA\Get(
        path: '/genres/{id}/games',
        tags: ['genres', 'games'],
        summary: 'The games of one genre',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                example: 2,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Games of the genre',
                content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Game'))
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
    public function getGenresByIdGames() {}
}
