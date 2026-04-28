<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Genre;

class GenreApiController extends Controller
{
    public function index()
    {
        return Genre::withCount('animes')->orderBy('name')->get();
    }

    public function show($id)
    {
        return Genre::with('animes.photo')->findOrFail($id);
    }
}
