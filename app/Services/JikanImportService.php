<?php

namespace App\Services;

use App\Models\Anime;
use App\Models\Genre;
use App\Models\Photo;
use App\Models\Studio;
use App\Models\Tag;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class JikanImportService
{
    private Client $httpClient;
    private JikanService $jikan;

    public function __construct(JikanService $jikan)
    {
        $this->httpClient = new Client(['timeout' => 20]);
        $this->jikan      = $jikan;
    }

    /**
     * Import a single anime from Jikan API data array.
     */
    public function importAnime(array $data): ?Anime
    {
        if (empty($data['mal_id'])) {
            return null;
        }

        // Dedup by mal_id
        $existing = Anime::where('mal_id', $data['mal_id'])->first();

        // Map studio
        $studio = $this->resolveStudio($data['studios'] ?? []);

        $animeData = [
            'title'            => $data['title'] ?? 'Unknown',
            'title_japanese'   => $data['title_japanese'] ?? null,
            'type'             => $this->mapType($data['type'] ?? ''),
            'episodes'         => $data['episodes'] ?? 0,
            'episode_duration' => $this->parseDuration($data['duration'] ?? ''),
            'status'           => $this->mapStatus($data['status'] ?? ''),
            'season'           => $data['season'] ? ucfirst($data['season']) : null,
            'season_year'      => $data['year'] ?? null,
            'source'           => $this->mapSource($data['source'] ?? ''),
            'synopsis'         => $data['synopsis'] ?? null,
            'trailer_url'      => $data['trailer']['embed_url'] ?? null,
            'mal_id'           => $data['mal_id'],
            'aired_from'       => $data['aired']['from'] ? substr($data['aired']['from'], 0, 10) : null,
            'aired_to'         => $data['aired']['to']   ? substr($data['aired']['to'],   0, 10) : null,
            'rating'           => $this->mapRating($data['rating'] ?? ''),
            'studio_id'        => $studio->id,
        ];

        if ($existing) {
            $existing->update($animeData);
            $anime = $existing;
        } else {
            $anime = Anime::create($animeData);
        }

        // Auto-detect seasons from Jikan relations (sequels = additional seasons)
        if ($anime->seasons === null) {
            $seasons = $this->jikan->fetchSeasonsCount($data['mal_id']);
            if ($seasons !== null) {
                $anime->update(['seasons' => $seasons]);
            }
        }

        // Cover image
        if (! $existing && ! empty($data['images']['jpg']['large_image_url'])) {
            $photo = $this->downloadCoverImage($data['images']['jpg']['large_image_url']);
            if ($photo) {
                $anime->update(['photo_id' => $photo->id]);
            }
        }

        // Genres
        $genreIds = [];
        foreach (array_merge($data['genres'] ?? [], $data['explicit_genres'] ?? []) as $g) {
            $genre      = Genre::firstOrCreate(['name' => $g['name']], ['name' => $g['name']]);
            $genreIds[] = $genre->id;
        }
        $anime->genres()->sync($genreIds);

        // Tags (demographics + themes)
        $tagIds = [];
        foreach (array_merge($data['demographics'] ?? [], $data['themes'] ?? []) as $t) {
            $tag      = Tag::firstOrCreate(['name' => $t['name']], ['name' => $t['name']]);
            $tagIds[] = $tag->id;
        }
        $anime->tags()->sync($tagIds);

        return $anime;
    }

    public function importBulk(array $results): int
    {
        $count = 0;
        foreach ($results as $item) {
            try {
                if ($this->importAnime($item)) {
                    $count++;
                }
            } catch (\Exception $e) {
                Log::warning('Jikan import failed for mal_id=' . ($item['mal_id'] ?? '?') . ': ' . $e->getMessage());
            }
        }
        return $count;
    }

    // ── Field Mappers ────────────────────────────────────────────────────────

    private function resolveStudio(array $studios): Studio
    {
        $name = $studios[0]['name'] ?? 'Unknown';
        return Studio::firstOrCreate(['name' => $name]);
    }

    private function mapType(string $type): string
    {
        return match (strtoupper($type)) {
            'TV'      => 'TV',
            'MOVIE'   => 'Movie',
            'OVA'     => 'OVA',
            'ONA'     => 'ONA',
            'SPECIAL' => 'Special',
            default   => 'TV',
        };
    }

    private function mapStatus(string $status): string
    {
        return match (true) {
            str_contains(strtolower($status), 'airing')    => 'airing',
            str_contains(strtolower($status), 'finished')  => 'finished',
            str_contains(strtolower($status), 'not yet')   => 'upcoming',
            default                                         => 'finished',
        };
    }

    private function mapSource(string $source): ?string
    {
        $valid = ['Manga', 'Light Novel', 'Visual Novel', 'Original', 'Game', 'Web Manga', 'Other'];
        return in_array($source, $valid) ? $source : 'Other';
    }

    private function mapRating(string $rating): ?string
    {
        return match (true) {
            str_starts_with($rating, 'G ')   => 'G',
            str_starts_with($rating, 'PG-13')=> 'PG-13',
            str_starts_with($rating, 'PG ')  => 'PG',
            str_starts_with($rating, 'R+ ')  => 'R+',
            str_starts_with($rating, 'R ')   => 'R',
            default                           => null,
        };
    }

    private function parseDuration(string $duration): ?int
    {
        // "24 min per ep" or "1 hr 30 min"
        if (preg_match('/(\d+)\s*hr.*?(\d+)\s*min/', $duration, $m)) {
            return (int) $m[1] * 60 + (int) $m[2];
        }
        if (preg_match('/(\d+)\s*hr/', $duration, $m)) {
            return (int) $m[1] * 60;
        }
        if (preg_match('/(\d+)\s*min/', $duration, $m)) {
            return (int) $m[1];
        }
        return null;
    }

    private function downloadCoverImage(string $url): ?Photo
    {
        try {
            $response = $this->httpClient->get($url);
            if ($response->getStatusCode() !== 200) {
                return null;
            }

            $ext      = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
            $filename = Str::uuid() . '.' . $ext;
            $destDir  = storage_path('app/public');

            if (! is_dir($destDir)) {
                mkdir($destDir, 0755, true);
            }

            file_put_contents($destDir . '/' . $filename, $response->getBody()->getContents());

            return Photo::create(['file' => $filename]);
        } catch (\Exception $e) {
            Log::warning('Failed to download Jikan cover: ' . $e->getMessage());
            return null;
        }
    }
}
