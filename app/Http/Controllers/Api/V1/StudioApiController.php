<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Studio;

class StudioApiController extends Controller
{
    public function index()
    {
        return Studio::with('photo')->withCount('animes')->orderByDesc('animes_count')->paginate(20);
    }

    public function show($id)
    {
        return Studio::with(['photo', 'animes.photo'])->findOrFail($id);
    }
}
