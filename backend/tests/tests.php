<?php
/**
 * Manual DAO test script for GameVault.
 *
 *   CLI:      php backend/tests/tests.php
 *   Browser:  http://localhost:8000/backend/tests/tests.php  (php -S localhost:8000 from the project root)
 *
 * Reads the seeded demo data first, then runs a create / update / delete round
 * trip on its own temporary rows and removes them again, so the database is
 * left exactly as it was found. Requires game_collection_manager.sql to be
 * imported first.
 */

require_once __DIR__ . '/../dao/UserDao.php';
require_once __DIR__ . '/../dao/GenreDao.php';
require_once __DIR__ . '/../dao/PlatformDao.php';
require_once __DIR__ . '/../dao/GameDao.php';
require_once __DIR__ . '/../dao/UserGameDao.php';

$is_cli = PHP_SAPI === 'cli';
$nl = $is_cli ? PHP_EOL : '<br/>';
if (!$is_cli) {
    echo '<pre style="font-family:monospace">';
}

function show($label, $data)
{
    global $nl;
    echo "== " . $label . " ==" . $nl;
    print_r($data);
    echo $nl;
}

$user_dao = new UserDao();
$genre_dao = new GenreDao();
$platform_dao = new PlatformDao();
$game_dao = new GameDao();
$user_game_dao = new UserGameDao();

/* ---------- 1. Read the seeded demo data ---------- */
show('All users', $user_dao->get_all());
show('User with id 1', $user_dao->get_by_id(1));
show('User by email', $user_dao->get_by_email('player1@example.com'));
show('All genres', $genre_dao->get_all());
show('Games per genre', $genre_dao->get_game_count_by_genre());
show('All platforms', $platform_dao->get_all());
show('Platform "PC"', $platform_dao->get_platform_by_name('PC'));
show('Catalog with genre names', $game_dao->get_all_with_genre());
show('Search catalog for "portal"', $game_dao->search_by_title('portal'));
show('Collection of user 1', $user_game_dao->get_collection_for_user(1));
show('Wishlist of user 1', $user_game_dao->get_wishlist_for_user(1));
show('Status counts of user 1', $user_game_dao->count_by_status(1));
show('Genre counts of user 1', $user_game_dao->count_by_genre(1));

/* ---------- 2. Create / update / delete round trip ---------- */
echo "Adding temporary user, genre, game and collection entry" . $nl;
$tmp_user = $user_dao->add_user([
    'username' => 'tmp_tester',
    'email' => 'tmp_tester@example.com',
    'password_hash' => password_hash('secret123', PASSWORD_DEFAULT)
]);
$tmp_genre = $genre_dao->add(['name' => 'Tmp Genre', 'description' => 'temporary']);
$tmp_game = $game_dao->add_game([
    'title' => 'Tmp Game',
    'description' => 'temporary',
    'release_date' => '2026-01-01',
    'genre_id' => $tmp_genre['genre_id'],
    'cover_url' => 'tmp'
]);
$tmp_entry = $user_game_dao->add_to_collection([
    'user_id' => $tmp_user['user_id'],
    'game_id' => $tmp_game['game_id'],
    'platform_id' => $platform_dao->get_platform_by_name('PC')['platform_id'],
    'status' => 'Want to play',
    'date_added' => date('Y-m-d')
]);
show('Temporary collection entry', $user_game_dao->get_by_id($tmp_entry['user_game_id']));

echo "Updating status and rating" . $nl;
$user_game_dao->set_status($tmp_entry['user_game_id'], 'Completed');
$user_game_dao->set_rating($tmp_entry['user_game_id'], 5);
show('After update', $user_game_dao->get_by_id($tmp_entry['user_game_id']));

echo "Updating username" . $nl;
$user_dao->edit_user(['username' => 'tmp_tester_renamed'], $tmp_user['user_id']);
show('Renamed user', $user_dao->get_by_id($tmp_user['user_id']));

echo "Cleaning up (deleting the user cascades to their collection)" . $nl;
$user_dao->delete_user($tmp_user['user_id']);
$game_dao->delete_game($tmp_game['game_id']);
$genre_dao->delete($tmp_genre['genre_id']);
show('Temporary user after delete (expect empty)', $user_dao->get_by_id($tmp_user['user_id']));
show('Temporary entry after delete (expect empty)', $user_game_dao->get_by_id($tmp_entry['user_game_id']));

echo "Done." . $nl;
if (!$is_cli) {
    echo '</pre>';
}
