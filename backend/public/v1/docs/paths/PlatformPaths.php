<?php

use OpenApi\Attributes as OA;

/*
 * OpenAPI documentation of rest/routes/PlatformRoutes.php.
 * swagger-php reads the #[OA\...] attributes; the methods are empty, they only carry the documentation.
 * Keep it in sync with the routes: one method per endpoint.
 */
class PlatformPaths
{
    #[OA\Get(
        path: '/platforms',
        tags: ['platforms'],
        summary: 'List all platforms',
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'All platforms',
                content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Platform'))
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
    public function getPlatforms() {}

    #[OA\Get(
        path: '/platforms/{id}',
        tags: ['platforms'],
        summary: 'Get one platform',
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
                description: 'The platform',
                content: new OA\JsonContent(ref: '#/components/schemas/Platform')
            ),
            new OA\Response(
                response: 401,
                description: 'Not logged in (missing, invalid or expired token)',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            ),
            new OA\Response(
                response: 404,
                description: 'Platform not found',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            ),
            new OA\Response(
                response: 500,
                description: 'Unexpected server error',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            )
        ]
    )]
    public function getPlatformsById() {}

    #[OA\Post(
        path: '/platforms',
        tags: ['platforms'],
        summary: 'Create a platform (admin only)',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/PlatformInput')
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Created platform',
                content: new OA\JsonContent(ref: '#/components/schemas/Platform')
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
    public function postPlatforms() {}

    #[OA\Put(
        path: '/platforms/{id}',
        tags: ['platforms'],
        summary: 'Update a platform (admin only)',
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
            content: new OA\JsonContent(ref: '#/components/schemas/PlatformInput')
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Updated platform',
                content: new OA\JsonContent(ref: '#/components/schemas/Platform')
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
                description: 'Platform not found',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            ),
            new OA\Response(
                response: 500,
                description: 'Unexpected server error',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            )
        ]
    )]
    public function putPlatformsById() {}

    #[OA\Delete(
        path: '/platforms/{id}',
        tags: ['platforms'],
        summary: 'Delete a platform (admin only)',
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
                description: 'Platform deleted',
                content: new OA\JsonContent(ref: '#/components/schemas/Message')
            ),
            new OA\Response(
                response: 400,
                description: 'The platform is still used by collection entries',
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
                description: 'Platform not found',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            ),
            new OA\Response(
                response: 500,
                description: 'Unexpected server error',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            )
        ]
    )]
    public function deletePlatformsById() {}
}
