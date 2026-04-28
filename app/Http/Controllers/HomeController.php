<?php

namespace App\Http\Controllers;

use App\Models\Anime;
use App\Models\Genre;
use App\Models\Quote;
use App\Models\Studio;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        $trendingAnime = Cache::remember('trending_anime', 600, fn () =>
            Anime::with(['photo', 'studio', 'genres'])
                ->withAvg(['reviews as avg_rating' => fn ($q) => $q->where('is_active', true)], 'rate')
                ->orderBy('views', 'desc')
                ->limit(6)
                ->get()
        );

        $genres = Cache::remember('genres_home', 3600, fn () =>
            Genre::withCount('animes')->with('photo')->orderBy('name')->limit(12)->get()
        );

        $studios = Cache::remember('studios_home', 3600, fn () =>
            Studio::withCount('animes')
                ->withSum('animes', 'views')
                ->with('photo')
                ->orderByDesc('animes_count')
                ->limit(6)
                ->get()
        );

        $quote = Cache::remember('daily_quote', 3600, fn () =>
            Quote::with('anime')->inRandomOrder()->first()
        );

        $airingAnime = Cache::remember('airing_anime', 600, fn () =>
            Anime::with(['photo', 'studio'])
                ->where('status', 'airing')
                ->orderBy('views', 'desc')
                ->limit(6)
                ->get()
        );

        $recentlyViewed  = collect();
        $recommendations = collect();

        if (auth()->check()) {
            $user = auth()->user();

            $recentlyViewed = $user->recentlyViewed()
                ->with(['photo', 'studio'])
                ->limit(6)
                ->get();

            $recommendations = Cache::remember('recommendations_' . $user->id, 900, function () use ($user) {
                $watchedIds  = $user->watchHistory()->pluck('animes.id');
                $shelvédIds  = $user->watchList()->pluck('animes.id');
                $excludeIds  = $watchedIds->merge($shelvédIds)->unique();

                $topGenreIds = DB::table('anime_genre')
                    ->whereIn('anime_id', $watchedIds)
                    ->select('genre_id', DB::raw('count(*) as cnt'))
                    ->groupBy('genre_id')
                    ->orderByDesc('cnt')
                    ->limit(3)
                    ->pluck('genre_id');

                if ($topGenreIds->isEmpty()) {
                    return collect();
                }

                return Anime::with(['photo', 'studio', 'genres'])
                    ->whereNotIn('id', $excludeIds)
                    ->whereHas('genres', fn ($q) => $q->whereIn('genres.id', $topGenreIds))
                    ->withAvg(['reviews as avg_rating' => fn ($q) => $q->where('is_active', true)], 'rate')
                    ->orderBy('views', 'desc')
                    ->limit(6)
                    ->get();
            });
        }

        $stats = Cache::remember('home_stats', 3600, fn () => [
            'anime'   => Anime::count(),
            'studios' => Studio::count(),
            'genres'  => Genre::count(),
        ]);

        return view('home', compact(
            'trendingAnime', 'genres', 'studios', 'quote',
            'airingAnime', 'recentlyViewed', 'recommendations', 'stats'
        ));
    }

    public function sitemap()
    {
        $animes  = Anime::select('slug', 'updated_at')->orderBy('updated_at', 'desc')->get();
        $studios = Studio::select('slug', 'updated_at')->orderBy('updated_at', 'desc')->get();

        $content = view('sitemap', compact('animes', 'studios'))->render();

        return response($content, 200)->header('Content-Type', 'application/xml');
    }
}
