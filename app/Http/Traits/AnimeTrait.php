<?php

namespace App\Http\Traits;

use App\Models\Anime;

trait AnimeTrait
{
    public static function getTopViewedAnime(): \Illuminate\Database\Eloquent\Collection
    {
        return Anime::orderBy('views', 'desc')->take(6)->get();
    }

    public static function getTopRecentAnime(): \Illuminate\Database\Eloquent\Collection
    {
        return Anime::orderBy('created_at', 'desc')->take(6)->get();
    }

    public static function getAiringAnime(): \Illuminate\Database\Eloquent\Collection
    {
        return Anime::where('status', 'airing')->orderBy('views', 'desc')->take(6)->get();
    }

    public static function getUpcomingAnime(): \Illuminate\Database\Eloquent\Collection
    {
        return Anime::where('status', 'upcoming')->orderBy('season_year', 'asc')->take(6)->get();
    }
}
