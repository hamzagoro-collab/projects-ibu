<?php

use OpenApi\Attributes as OA;

/*
 * General API information + reusable schemas (referenced from the paths with ref: '#/components/schemas/...').
 * swagger-php 6 reads these PHP 8 attributes; the class itself is empty.
 * (Plain comments on purpose: swagger-php would turn a doc comment into a description.)
 */
#[OA\Info(
    title: 'GameVault API',
    description: 'REST API of GameVault, a video game collection manager. Log in with POST /auth/login, click Authorize and paste the token. Demo accounts: player1@example.com (admin) and player2@example.com (user), password: password.',
    version: '1.0',
    contact: new OA\Contact(name: 'GameVault', email: 'gamevault@example.com')
)]
#[OA\Server(url: API_BASE_URL, description: 'This server')]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'JWT'
)]
#[OA\Schema(
    schema: 'Error',
    properties: [
        new OA\Property(property: 'status', type: 'integer', example: 404),
        new OA\Property(property: 'error', type: 'string', example: 'Game not found.')
    ]
)]
#[OA\Schema(
    schema: 'Message',
    properties: [
        new OA\Property(property: 'message', type: 'string', example: 'Game deleted.')
    ]
)]
#[OA\Schema(
    schema: 'RegisterInput',
    required: ['username', 'email', 'password'],
    properties: [
        new OA\Property(property: 'username', type: 'string', example: 'newplayer'),
        new OA\Property(property: 'email', type: 'string', example: 'new@example.com'),
        new OA\Property(property: 'password', type: 'string', example: 'secret123')
    ]
)]
#[OA\Schema(
    schema: 'LoginInput',
    required: ['email', 'password'],
    properties: [
        new OA\Property(property: 'email', type: 'string', example: 'player1@example.com'),
        new OA\Property(property: 'password', type: 'string', example: 'password')
    ]
)]
#[OA\Schema(
    schema: 'UserInput',
    required: ['username', 'email', 'password'],
    properties: [
        new OA\Property(property: 'username', type: 'string', example: 'newplayer'),
        new OA\Property(property: 'email', type: 'string', example: 'new@example.com'),
        new OA\Property(property: 'password', type: 'string', example: 'secret123'),
        new OA\Property(property: 'role', type: 'string', enum: ['user', 'admin'], example: 'user')
    ]
)]
#[OA\Schema(
    schema: 'UserUpdateInput',
    required: ['username', 'email'],
    properties: [
        new OA\Property(property: 'username', type: 'string', example: 'newplayer'),
        new OA\Property(property: 'email', type: 'string', example: 'new@example.com'),
        new OA\Property(property: 'password', type: 'string', description: 'Optional', example: 'secret123'),
        new OA\Property(property: 'role', type: 'string', enum: ['user', 'admin'], description: 'Optional, admin only', example: 'user')
    ]
)]
#[OA\Schema(
    schema: 'User',
    properties: [
        new OA\Property(property: 'user_id', type: 'integer', example: 1),
        new OA\Property(property: 'username', type: 'string', example: 'playerone'),
        new OA\Property(property: 'email', type: 'string', example: 'player1@example.com'),
        new OA\Property(property: 'role', type: 'string', enum: ['user', 'admin'], example: 'admin'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time')
    ]
)]
#[OA\Schema(
    schema: 'AuthResponse',
    properties: [
        new OA\Property(property: 'token', type: 'string', description: 'JWT: send it as Authorization: Bearer <token>'),
        new OA\Property(property: 'user', ref: '#/components/schemas/User')
    ]
)]
#[OA\Schema(
    schema: 'GenreInput',
    required: ['name'],
    properties: [
        new OA\Property(property: 'name', type: 'string', example: 'RPG'),
        new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Role-playing games')
    ]
)]
#[OA\Schema(
    schema: 'Genre',
    allOf: [new OA\Schema(ref: '#/components/schemas/GenreInput')],
    properties: [
        new OA\Property(property: 'genre_id', type: 'integer', example: 1)
    ]
)]
#[OA\Schema(
    schema: 'PlatformInput',
    required: ['name'],
    properties: [
        new OA\Property(property: 'name', type: 'string', example: 'PC'),
        new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Personal computer')
    ]
)]
#[OA\Schema(
    schema: 'Platform',
    allOf: [new OA\Schema(ref: '#/components/schemas/PlatformInput')],
    properties: [
        new OA\Property(property: 'platform_id', type: 'integer', example: 1)
    ]
)]
#[OA\Schema(
    schema: 'GameInput',
    required: ['title'],
    properties: [
        new OA\Property(property: 'title', type: 'string', example: 'Hades'),
        new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Escape the underworld.'),
        new OA\Property(property: 'release_date', type: 'string', format: 'date', nullable: true, example: '2020-09-17'),
        new OA\Property(property: 'genre_id', type: 'integer', nullable: true, example: 2),
        new OA\Property(property: 'cover_url', type: 'string', nullable: true, example: 'https://example.com/hades.jpg')
    ]
)]
#[OA\Schema(
    schema: 'Game',
    allOf: [new OA\Schema(ref: '#/components/schemas/GameInput')],
    properties: [
        new OA\Property(property: 'game_id', type: 'integer', example: 1),
        new OA\Property(property: 'genre_name', type: 'string', nullable: true, example: 'RPG'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time')
    ]
)]
#[OA\Schema(
    schema: 'UserGameInput',
    required: ['game_id', 'platform_id'],
    properties: [
        new OA\Property(property: 'game_id', type: 'integer', example: 1),
        new OA\Property(property: 'platform_id', type: 'integer', example: 1),
        new OA\Property(property: 'status', type: 'string', enum: ['Playing', 'Completed', 'Want to play'], default: 'Want to play', example: 'Playing'),
        new OA\Property(property: 'rating', type: 'integer', minimum: 1, maximum: 5, nullable: true, description: 'Not allowed with status Want to play', example: 5),
        new OA\Property(property: 'user_id', type: 'integer', description: 'Admin only: add the entry for this user', example: 2)
    ]
)]
#[OA\Schema(
    schema: 'UserGameUpdateInput',
    properties: [
        new OA\Property(property: 'status', type: 'string', enum: ['Playing', 'Completed', 'Want to play'], example: 'Completed'),
        new OA\Property(property: 'rating', type: 'integer', minimum: 1, maximum: 5, nullable: true, example: 4),
        new OA\Property(property: 'platform_id', type: 'integer', example: 2)
    ]
)]
#[OA\Schema(
    schema: 'UserGame',
    properties: [
        new OA\Property(property: 'user_game_id', type: 'integer', example: 1),
        new OA\Property(property: 'user_id', type: 'integer', example: 1),
        new OA\Property(property: 'game_id', type: 'integer', example: 1),
        new OA\Property(property: 'platform_id', type: 'integer', example: 1),
        new OA\Property(property: 'status', type: 'string', enum: ['Playing', 'Completed', 'Want to play'], example: 'Playing'),
        new OA\Property(property: 'rating', type: 'integer', nullable: true, example: 5),
        new OA\Property(property: 'date_added', type: 'string', format: 'date', example: '2026-11-20'),
        new OA\Property(property: 'title', type: 'string', example: 'Hades'),
        new OA\Property(property: 'release_date', type: 'string', format: 'date', nullable: true, example: '2020-09-17'),
        new OA\Property(property: 'cover_url', type: 'string', nullable: true),
        new OA\Property(property: 'genre_name', type: 'string', nullable: true, example: 'RPG'),
        new OA\Property(property: 'platform_name', type: 'string', example: 'PC')
    ]
)]
#[OA\Schema(
    schema: 'CollectionStats',
    properties: [
        new OA\Property(property: 'user_id', type: 'integer', example: 1),
        new OA\Property(property: 'total', type: 'integer', example: 6),
        new OA\Property(property: 'by_status', type: 'object', properties: [
            new OA\Property(property: 'Playing', type: 'integer', example: 2),
            new OA\Property(property: 'Completed', type: 'integer', example: 3),
            new OA\Property(property: 'Want to play', type: 'integer', example: 1)
        ]),
        new OA\Property(property: 'by_genre', type: 'array', items: new OA\Items(properties: [
            new OA\Property(property: 'genre_name', type: 'string', example: 'RPG'),
            new OA\Property(property: 'total_games', type: 'integer', example: 3)
        ]))
    ]
)]
#[OA\Schema(
    schema: 'ExternalGame',
    properties: [
        new OA\Property(property: 'external_id', type: 'integer', example: 3498),
        new OA\Property(property: 'title', type: 'string', example: 'Grand Theft Auto V'),
        new OA\Property(property: 'release_date', type: 'string', nullable: true, example: '2013-09-17'),
        new OA\Property(property: 'cover_url', type: 'string', nullable: true),
        new OA\Property(property: 'rating', type: 'number', nullable: true, example: 4.47),
        new OA\Property(property: 'metacritic', type: 'integer', nullable: true, example: 92),
        new OA\Property(property: 'genres', type: 'array', items: new OA\Items(type: 'string')),
        new OA\Property(property: 'platforms', type: 'array', items: new OA\Items(type: 'string')),
        new OA\Property(property: 'description', type: 'string', nullable: true, description: 'Only in the details endpoint'),
        new OA\Property(property: 'website', type: 'string', nullable: true, description: 'Only in the details endpoint')
    ]
)]
#[OA\Schema(
    schema: 'ExternalSearchResult',
    properties: [
        new OA\Property(property: 'search', type: 'string', example: 'Hades'),
        new OA\Property(property: 'page', type: 'integer', example: 1),
        new OA\Property(property: 'total', type: 'integer', example: 42),
        new OA\Property(property: 'has_next', type: 'boolean', example: true),
        new OA\Property(property: 'results', type: 'array', items: new OA\Items(ref: '#/components/schemas/ExternalGame'))
    ]
)]
class OpenApiSpec {}
