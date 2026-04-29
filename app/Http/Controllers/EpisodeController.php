<?php

namespace App\Http\Controllers;

use App\Models\Anime;
use App\Models\Episode;
use Illuminate\Http\Request;

class EpisodeController extends Controller
{
    public function index(Anime $anime)
    {
        $episodes = $anime->episodes()->paginate(50);

        return view('admin.episodes.index', compact('anime', 'episodes'));
    }

    public function create(Anime $anime)
    {
        $series   = request()->integer('series', 1) ?: 1;
        $next     = ($anime->episodes()->where('series', $series)->max('number') ?? 0) + 1;
        $maxSeries = ($anime->episodes()->max('series') ?? 1);

        return view('admin.episodes.create', compact('anime', 'next', 'series', 'maxSeries'));
    }

    public function store(Request $request, Anime $anime)
    {
        $data = $request->validate([
            'series'      => 'required|integer|min:1',
            'number'      => 'required|integer|min:1',
            'title'       => 'nullable|string|max:255',
            'url'         => 'required|url|max:2048',
            'description' => 'nullable|string|max:1000',
            'duration'    => 'nullable|integer|min:1|max:300',
            'is_active'   => 'boolean',
        ]);

        $anime->episodes()->updateOrCreate(
            ['series' => $data['series'], 'number' => $data['number']],
            $data + ['is_active' => $request->boolean('is_active', true)],
        );

        return redirect()->route('episodes.index', $anime)
            ->with('success', 'Episode saved.');
    }

    public function edit(Anime $anime, Episode $episode)
    {
        return view('admin.episodes.edit', compact('anime', 'episode'));
    }

    public function update(Request $request, Anime $anime, Episode $episode)
    {
        $data = $request->validate([
            'series'      => 'required|integer|min:1',
            'number'      => 'required|integer|min:1',
            'title'       => 'nullable|string|max:255',
            'url'         => 'required|url|max:2048',
            'description' => 'nullable|string|max:1000',
            'duration'    => 'nullable|integer|min:1|max:300',
            'is_active'   => 'boolean',
        ]);

        $episode->update($data + ['is_active' => $request->boolean('is_active', true)]);

        return redirect()->route('episodes.index', $anime)
            ->with('success', 'Episode updated.');
    }

    public function destroy(Anime $anime, Episode $episode)
    {
        $episode->delete();

        return back()->with('success', 'Episode deleted.');
    }

    // ── Public watch page ─────────────────────────────────────────────────────

    public function watch(Anime $anime, Episode $episode)
    {
        abort_unless($episode->is_active && $episode->anime_id === $anime->id, 404);

        $base = $anime->episodes()->where('series', $episode->series)->where('is_active', true);

        $prev = (clone $base)->where('number', '<', $episode->number)->max('number');
        $next = (clone $base)->where('number', '>', $episode->number)->min('number');

        $prevEpisode = $prev ? (clone $base)->where('number', $prev)->first() : null;
        $nextEpisode = $next ? (clone $base)->where('number', $next)->first() : null;

        $allEpisodes = $anime->episodes()->where('is_active', true)->orderBy('series')->orderBy('number')->get();

        return view('watch', compact('anime', 'episode', 'prevEpisode', 'nextEpisode', 'allEpisodes'));
    }
}
