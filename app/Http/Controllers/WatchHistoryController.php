<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WatchHistoryController extends Controller
{
    public function index()
    {
        $animes = auth()->user()->watchHistory()
            ->with(['photo', 'studio'])
            ->orderByPivot('completed_at', 'desc')
            ->paginate(12);

        return view('watch-history', compact('animes'));
    }

    public function destroy($animeId)
    {
        auth()->user()->watchHistory()->detach($animeId);

        return back()->with('success', __('Removed from watch history.'));
    }
}
