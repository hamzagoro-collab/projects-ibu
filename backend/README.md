# GameVault backend

PHP data access layer (DAO) for the GameVault video game collection manager.

```
backend/
├── config/config.php     Database settings (environment variables with local defaults)
├── dao/
│   ├── BaseDao.php       Generic PDO CRUD: add, update, delete, get_all, get_by_id
│   ├── UserDao.php       users
│   ├── GenreDao.php      genres
│   ├── PlatformDao.php   platforms
│   ├── GameDao.php       games (the shared catalog)
│   └── UserGameDao.php   user_games (a user's collection: status + rating per platform)
├── tests/tests.php       Manual test script (reads demo data, then a self-cleaning CRUD round trip)
├── middleware/           Reserved for a later milestone
├── routes/               Reserved for a later milestone
└── services/             Reserved for a later milestone
```

## Setup

1. Import the database: `mysql -u root -p < game_collection_manager.sql` (creates the `gamevault` schema with demo data).
2. Set credentials if yours differ from the defaults (`root`, empty password, `127.0.0.1:3306`):
   `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASSWORD`.
3. Run the tests: `php backend/tests/tests.php`

Every DAO passes its table name and primary-key column to `BaseDao`, because GameVault's keys are
named `user_id`, `game_id`, `genre_id`, `platform_id` and `user_game_id` rather than `id`.
