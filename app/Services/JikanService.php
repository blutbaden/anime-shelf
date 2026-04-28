<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Cache;

class JikanService
{
    private Client $client;
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.jikan.base_url', 'https://api.jikan.moe/v4'), '/') . '/';
        $this->client  = new Client([
            'base_uri' => $this->baseUrl,
            'timeout'  => 15,
            'headers'  => [
                'Accept'     => 'application/json',
                'User-Agent' => 'AnimeSelf/1.0',
            ],
        ]);
    }

    public function search(string $query, int $page = 1): array
    {
        $cacheKey = 'jikan_search_' . md5($query . $page);

        return Cache::remember($cacheKey, 86400, function () use ($query, $page) {
            $this->rateLimit();
            try {
                $response = $this->client->get('anime', [
                    'query' => ['q' => $query, 'page' => $page, 'limit' => 20],
                ]);
                return json_decode($response->getBody()->getContents(), true);
            } catch (RequestException $e) {
                return ['data' => []];
            }
        });
    }

    public function getAnime(int $malId): array
    {
        $cacheKey = 'jikan_anime_' . $malId;

        return Cache::remember($cacheKey, 86400, function () use ($malId) {
            $this->rateLimit();
            try {
                $response = $this->client->get("anime/{$malId}/full");
                return json_decode($response->getBody()->getContents(), true);
            } catch (RequestException $e) {
                return ['data' => null];
            }
        });
    }

    public function getTopAnime(int $page = 1, string $filter = 'bypopularity'): array
    {
        $cacheKey = 'jikan_top_' . $filter . '_' . $page;

        return Cache::remember($cacheKey, 86400, function () use ($page, $filter) {
            $this->rateLimit();
            try {
                $response = $this->client->get('top/anime', [
                    'query' => ['page' => $page, 'filter' => $filter, 'limit' => 25],
                ]);
                return json_decode($response->getBody()->getContents(), true);
            } catch (RequestException $e) {
                return ['data' => []];
            }
        });
    }

    public function getRelations(int $malId): array
    {
        $cacheKey = 'jikan_relations_' . $malId;

        return Cache::remember($cacheKey, 86400, function () use ($malId) {
            $this->rateLimit();
            try {
                $response = $this->client->get("anime/{$malId}/relations");
                return json_decode($response->getBody()->getContents(), true);
            } catch (RequestException $e) {
                return ['data' => []];
            }
        });
    }

    /**
     * Count seasons by looking at how many "Sequel" anime relations exist.
     * Each MAL sequel entry of type anime represents a new season.
     * Returns 1 (the anime itself) + number of sequel anime entries.
     */
    public function fetchSeasonsCount(int $malId): ?int
    {
        $relations = $this->getRelations($malId);

        if (empty($relations['data'])) {
            return null;
        }

        $sequelCount = 0;
        foreach ($relations['data'] as $relation) {
            if (strtolower($relation['relation'] ?? '') === 'sequel') {
                foreach ($relation['entry'] ?? [] as $entry) {
                    if (strtolower($entry['type'] ?? '') === 'anime') {
                        $sequelCount++;
                    }
                }
            }
        }

        // If no sequels found, we can't determine seasons automatically
        if ($sequelCount === 0) {
            return null;
        }

        return 1 + $sequelCount;
    }

    public function getSeasonAnime(int $year, string $season): array
    {
        $cacheKey = 'jikan_season_' . $year . '_' . $season;

        return Cache::remember($cacheKey, 86400, function () use ($year, $season) {
            $this->rateLimit();
            try {
                $response = $this->client->get("seasons/{$year}/{$season}");
                return json_decode($response->getBody()->getContents(), true);
            } catch (RequestException $e) {
                return ['data' => []];
            }
        });
    }

    /** Jikan allows 3 requests/second — sleep 350ms between calls */
    private function rateLimit(): void
    {
        usleep(350_000);
    }
}
