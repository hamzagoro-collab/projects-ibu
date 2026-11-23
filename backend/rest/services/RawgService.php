<?php

require_once __DIR__ . '/../../utils/exceptions.php';
require_once __DIR__ . '/../../utils/Validator.php';
require_once __DIR__ . '/../../config/config.php';

/**
 * Client of the external RAWG Video Games Database (https://rawg.io/apidocs).
 *
 * - The API key comes from config.php and never reaches the browser.
 * - cURL has a connect timeout and a total timeout (config.php), so a slow
 *   RAWG cannot block our API.
 * - Errors become: 400 bad input, 404 unknown game, 503 RAWG not available
 *   (timeout, no connection, rejected key, rate limit, bad answer).
 *   The technical reason is written to the server log, not sent to the client.
 */
class RawgService
{
    /** Search RAWG by title. */
    public function search($text, $page = 1, $page_size = 10): array
    {
        $text = trim((string) $text);
        Validator::length($text, 'search', 2, 100);
        $page = Validator::integer($page, 'page', 1, 1000);
        $page_size = Validator::integer($page_size, 'page_size', 1, 40);

        $data = $this->request('/games', ['search' => $text, 'page' => $page, 'page_size' => $page_size]);

        return [
            'search' => $text,
            'page' => $page,
            'total' => (int) ($data['count'] ?? 0),
            'has_next' => !empty($data['next']),
            'results' => array_map([$this, 'normalize'], $data['results'] ?? [])
        ];
    }

    /** Details of one RAWG game. */
    public function get_game($external_id): array
    {
        $id = Validator::integer($external_id, 'id', 1);
        $data = $this->request('/games/' . $id, []);

        $game = $this->normalize($data);
        $game['description'] = $data['description_raw'] ?? null;
        $game['website'] = $data['website'] ?? null;
        return $game;
    }

    /** Only the fields the frontend needs, named like GameVault's own. */
    private function normalize(array $game): array
    {
        $platforms = [];
        foreach ($game['platforms'] ?? [] as $entry) {
            if (!empty($entry['platform']['name'])) {
                $platforms[] = $entry['platform']['name'];
            }
        }
        return [
            'external_id' => $game['id'] ?? null,
            'title' => $game['name'] ?? null,
            'release_date' => $game['released'] ?? null,
            'cover_url' => $game['background_image'] ?? null,
            'rating' => $game['rating'] ?? null,
            'metacritic' => $game['metacritic'] ?? null,
            'genres' => array_values(array_filter(array_column($game['genres'] ?? [], 'name'))),
            'platforms' => $platforms
        ];
    }

    /** GET request to RAWG; returns the decoded JSON. */
    private function request(string $path, array $params): array
    {
        $key = Config::RAWG_API_KEY();
        if ($key === '' || $key === 'YOUR_RAWG_API_KEY') {
            // our own configuration problem: a plain 500, details only in the log
            throw new RuntimeException('RAWG_API_KEY is not set in backend/config/config.php.');
        }
        $params['key'] = $key;
        $url = rtrim(Config::RAWG_BASE_URL(), '/') . $path . '?' . http_build_query($params);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => Config::RAWG_CONNECT_TIMEOUT(),
            CURLOPT_TIMEOUT => Config::RAWG_TIMEOUT(),
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_HTTPHEADER => ['Accept: application/json']
        ]);
        $body = curl_exec($ch);
        $errno = curl_errno($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        curl_close($ch);

        if ($errno === CURLE_OPERATION_TIMEDOUT) {
            error_log("RAWG timeout after " . Config::RAWG_TIMEOUT() . " s: $path");
            throw new ServiceUnavailableException('The RAWG games service did not answer in time. Please try again.');
        }
        if ($errno !== 0 || $body === false) {
            error_log("RAWG connection error (cURL $errno): $path");
            throw new ServiceUnavailableException('The RAWG games service is not available.');
        }
        if ($status === 404) {
            throw new NotFoundException('Game not found in RAWG.');
        }
        if ($status < 200 || $status >= 300) {
            // 401/403 = our key was rejected, 429 = rate limit, 5xx = RAWG itself is broken
            error_log("RAWG answered HTTP $status: $path");
            throw new ServiceUnavailableException('The RAWG games service is not available.');
        }

        $data = json_decode($body, true);
        if (!is_array($data)) {
            error_log("RAWG sent invalid JSON: $path");
            throw new ServiceUnavailableException('The RAWG games service sent an invalid answer.');
        }
        return $data;
    }
}
