<?php

namespace App\Http\Controllers;

use App\Models\Anime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WatchListController extends Controller
{
    public function index(Request $request)
    {
        $validStatuses = ['plan_to_watch', 'watching', 'completed', 'on_hold', 'dropped'];
        $status = $request->query('status');

        $query = auth()->user()->watchList()->with(['photo', 'studio', 'genres']);

        if ($status && in_array($status, $validStatuses)) {
            $query->wherePivot('status', $status);
        }

        $animes = $query->orderByPivot('updated_at', 'desc')->paginate(12)->withQueryString();
        $counts = $this->statusCounts();

        return view('watch-list', compact('animes', 'status', 'counts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'anime_id' => 'required|exists:animes,id',
            'status'   => 'nullable|in:plan_to_watch,watching,completed,on_hold,dropped',
        ]);

        $user    = auth()->user();
        $animeId = $request->anime_id;
        $status  = $request->status ?? 'plan_to_watch';
        $pivot   = $user->watchList()->where('anime_id', $animeId)->first();
        $current = $pivot?->pivot->status;

        if ($current === $status) {
            // Clicking same status → remove from list
            $user->watchList()->detach($animeId);
            $action = 'removed';
        } else {
            $user->watchList()->syncWithoutDetaching([
                $animeId => ['status' => $status],
            ]);
            $action = $status;

            // Auto-record in watch history when completed
            if ($status === 'completed') {
                DB::table('watch_history')->upsert(
                    ['user_id' => $user->id, 'anime_id' => $animeId, 'completed_at' => now(),
                     'created_at' => now(), 'updated_at' => now()],
                    ['user_id', 'anime_id'],
                    ['completed_at', 'updated_at']
                );
            }
        }

        if ($request->expectsJson()) {
            return response()->json(['action' => $action, 'status' => $status]);
        }

        if ($action === 'removed') {
            return back();
        }

        $labels = [
            'plan_to_watch' => __('Added to Plan to Watch.'),
            'watching'      => __('Marked as Watching.'),
            'completed'     => __('Marked as Completed.'),
            'on_hold'       => __('Marked as On Hold.'),
            'dropped'       => __('Marked as Dropped.'),
        ];

        return back()->with('success', $labels[$action] ?? __('Updated.'));
    }

    public function updateProgress(Request $request)
    {
        $request->validate([
            'anime_id'        => 'required|exists:animes,id',
            'current_episode' => 'required|integer|min:0',
            'total_episodes'  => 'nullable|integer|min:0',
        ]);

        $user    = auth()->user();
        $animeId = $request->anime_id;

        $existing = $user->watchList()->where('anime_id', $animeId)->first();

        if ($existing) {
            $currentStatus = $existing->pivot->status;
            $newStatus     = $currentStatus === 'completed' ? 'completed' : 'watching';

            $user->watchList()->updateExistingPivot($animeId, [
                'current_episode' => $request->current_episode,
                'status'          => $newStatus,
            ]);

            // Auto-complete if episode count reached
            $total = $request->total_episodes ?? 0;
            if ($total > 0 && $request->current_episode >= $total && $newStatus !== 'completed') {
                $user->watchList()->updateExistingPivot($animeId, ['status' => 'completed']);
                DB::table('watch_history')->upsert(
                    ['user_id' => $user->id, 'anime_id' => $animeId, 'completed_at' => now(),
                     'created_at' => now(), 'updated_at' => now()],
                    ['user_id', 'anime_id'],
                    ['completed_at', 'updated_at']
                );
            }
        } else {
            $user->watchList()->attach($animeId, [
                'status'          => 'watching',
                'current_episode' => $request->current_episode,
            ]);
        }

        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'current_episode' => $request->current_episode]);
        }

        return response()->noContent();
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function statusCounts(): array
    {
        $rows = auth()->user()->watchList()
            ->selectRaw('watch_list.status, count(*) as total')
            ->groupBy('watch_list.status')
            ->pluck('total', 'watch_list.status')
            ->toArray();

        return [
            'all'           => array_sum($rows),
            'plan_to_watch' => $rows['plan_to_watch'] ?? 0,
            'watching'      => $rows['watching']      ?? 0,
            'completed'     => $rows['completed']     ?? 0,
            'on_hold'       => $rows['on_hold']       ?? 0,
            'dropped'       => $rows['dropped']       ?? 0,
        ];
    }
}
