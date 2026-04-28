<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Anime;
use App\Models\Genre;
use Illuminate\Http\Request;

class AnimeApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Anime::with(['photo', 'studio', 'genres'])
            ->withAvg(['reviews as avg_rating' => fn ($q) => $q->where('is_active', true)], 'rate');

        if ($search = $request->get('search')) {
            $term = '%' . $search . '%';
            $query->where(fn ($q) => $q->where('title', 'ilike', $term)
                ->orWhere('title_japanese', 'ilike', $term));
        }

        if ($genre = $request->get('genre')) {
            $query->whereHas('genres', fn ($q) => $q->where('genres.id', $genre));
        }

        if ($type   = $request->get('type'))   $query->where('type', $type);
        if ($status = $request->get('status')) $query->where('status', $status);
        if ($year   = $request->get('year'))   $query->where('season_year', $year);

        $sort = $request->get('sort', 'popular');
        $query->sortBy($sort);

        return $query->paginate((int) $request->get('per_page', 20));
    }

    public function show($slug)
    {
        $anime = Anime::with(['photo', 'studio', 'genres', 'tags'])
            ->withAvg(['reviews as avg_rating' => fn ($q) => $q->where('is_active', true)], 'rate')
            ->when(is_numeric($slug), fn ($q) => $q->where('id', $slug), fn ($q) => $q->where('slug', $slug))
            ->firstOrFail();

        $anime->increment('views');

        return $anime;
    }

    public function related($id)
    {
        $anime    = Anime::with('genres')->findOrFail($id);
        $genreIds = $anime->genres->pluck('id');

        return Anime::with(['photo', 'studio'])
            ->whereHas('genres', fn ($q) => $q->whereIn('genres.id', $genreIds))
            ->where('id', '!=', $id)
            ->inRandomOrder()
            ->limit(6)
            ->get();
    }
}
