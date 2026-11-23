<?php

use OpenApi\Attributes as OA;

/*
 * OpenAPI documentation of rest/routes/AuthRoutes.php.
 * swagger-php reads the #[OA\...] attributes; the methods are empty, they only carry the documentation.
 * Keep it in sync with the routes: one method per endpoint.
 */
class AuthPaths
{
    #[OA\Post(
        path: '/auth/register',
        tags: ['auth'],
        summary: 'Create an account and log in',
        description: 'Always creates a regular user. Username and email must be unique; the password needs 8 to 72 characters, including a letter and a number.',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/RegisterInput')
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Account created; the token is valid for 24 hours',
                content: new OA\JsonContent(ref: '#/components/schemas/AuthResponse')
            ),
            new OA\Response(
                response: 400,
                description: 'Invalid data or a broken business rule',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            ),
            new OA\Response(
                response: 500,
                description: 'Unexpected server error',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            )
        ]
    )]
    public function postAuthRegister() {}

    #[OA\Post(
        path: '/auth/login',
        tags: ['auth'],
        summary: 'Log in and receive a JWT',
        description: 'Copy the token, click Authorize and paste it to call the other endpoints.',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/LoginInput')
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Logged in',
                content: new OA\JsonContent(ref: '#/components/schemas/AuthResponse')
            ),
            new OA\Response(
                response: 400,
                description: 'Invalid data or a broken business rule',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            ),
            new OA\Response(
                response: 401,
                description: 'Wrong email or password',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            ),
            new OA\Response(
                response: 500,
                description: 'Unexpected server error',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            )
        ]
    )]
    public function postAuthLogin() {}

    #[OA\Get(
        path: '/auth/me',
        tags: ['auth'],
        summary: 'The logged-in user',
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'The current user',
                content: new OA\JsonContent(ref: '#/components/schemas/User')
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
    public function getAuthMe() {}
}
