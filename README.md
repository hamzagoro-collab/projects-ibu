# GameVault — Web Programming Project (Milestone 2)

A video game collection manager. Users keep a personal library of games (with platform, status and
rating), a wishlist, and a dashboard summarizing their collection.

```
.
├── game_collection_manager.sql   MySQL schema + demo data (database: gamevault)
├── erd.png                       Entity relationship diagram
├── backend/                      PHP DAO layer (see backend/README.md)
└── frontend/                     jQuery SPApp single-page frontend (see frontend/views/README.md)
```

## Run it

1. **Database:** `mysql -u root -p < game_collection_manager.sql`
2. **Backend tests:** `php backend/tests/tests.php` (credentials via `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASSWORD`)
3. **Frontend:** from the `frontend/` folder run `python -m http.server`, then open `http://localhost:8000`.
   It must be served over HTTP because views are loaded with AJAX and scripts are ES modules.

The frontend still uses in-memory sample data; connecting it to the backend through routes/services is
planned for a later milestone.
