<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Anime;

class ReviewApiController extends Controller
{
    public function index($animeId)
    {
        $anime = Anime::findOrFail($animeId);

        return $anime->reviews()
            ->where('is_active', true)
            ->whereNull('parent_id')
            ->with('user:id,name')
            ->latest()
            ->paginate(20);
    }
}
