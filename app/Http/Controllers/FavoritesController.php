<?php

namespace App\Http\Controllers;

use App\Models\Anime;
use Illuminate\Http\Request;

class FavoritesController extends Controller
{
    public function index()
    {
        $animes = auth()->user()->favoriteAnime()->with(['photo', 'studio'])->paginate(12);
        return view('favorites', compact('animes'));
    }

    public function store(Request $request)
    {
        $request->validate(['anime_id' => 'required|exists:animes,id']);

        $user    = auth()->user();
        $animeId = $request->anime_id;

        if ($user->favoriteAnime()->where('anime_id', $animeId)->exists()) {
            $user->favoriteAnime()->detach($animeId);
            $action = 'removed';
        } else {
            $user->favoriteAnime()->attach($animeId);
            $action = 'added';
        }

        if ($request->expectsJson()) {
            return response()->json(['action' => $action]);
        }

        return back()->with('success', $action === 'added'
            ? __('Added to favorites.')
            : __('Removed from favorites.')
        );
    }
}
