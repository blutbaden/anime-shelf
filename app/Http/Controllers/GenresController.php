<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Genre;
use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class GenresController extends Controller
{
    // ── Admin CRUD ───────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $genres = Genre::with('photo')
            ->withCount('animes')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.genres.index', compact('genres'));
    }

    public function create()
    {
        return view('admin.genres.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|max:255|unique:genres,name',
            'description' => 'nullable',
            'photo_id'    => 'nullable|file|mimes:jpeg,jpg,png,gif,webp|max:2048',
        ]);

        if ($file = $request->file('photo_id')) {
            $name  = Str::uuid() . '.' . ($file->guessExtension() ?? 'bin');
            $file->move(public_path('images/genres'), $name);
            $photo = Photo::create(['file' => $name]);
            $validated['photo_id'] = $photo->id;
        }

        $genre = Genre::create($validated);
        Cache::forget('genres_home');
        Cache::forget('genres_list_slim');
        AuditLog::record('genre.created', $genre, [], ['name' => $genre->name]);

        return back()->with('success', 'Genre created successfully.');
    }

    public function edit($id)
    {
        $genre = Genre::with('photo')->findOrFail($id);
        return view('admin.genres.edit', compact('genre'));
    }

    public function update(Request $request, $id)
    {
        $genre     = Genre::findOrFail($id);
        $validated = $request->validate([
            'name'        => 'required|max:255|unique:genres,name,' . $id,
            'description' => 'nullable',
            'photo_id'    => 'nullable|file|mimes:jpeg,jpg,png,gif,webp|max:2048',
        ]);

        if ($file = $request->file('photo_id')) {
            $name  = Str::uuid() . '.' . ($file->guessExtension() ?? 'bin');
            $file->move(public_path('images/genres'), $name);
            $photo = Photo::create(['file' => $name]);
            $validated['photo_id'] = $photo->id;
        }

        $old = $genre->only(['name']);
        $genre->update($validated);
        Cache::forget('genres_home');
        Cache::forget('genres_list_slim');
        AuditLog::record('genre.updated', $genre, $old, $genre->only(['name']));

        return back()->with('success', 'Genre updated successfully.');
    }

    public function destroy($id)
    {
        $genre = Genre::findOrFail($id);
        if ($genre->photo) {
            $img = public_path('/images/genres/' . $genre->photo->file);
            if (file_exists($img)) unlink($img);
            Photo::destroy([$genre->photo->id]);
        }
        AuditLog::record('genre.deleted', $genre, ['name' => $genre->name], []);
        $genre->delete();
        Cache::forget('genres_home');
        Cache::forget('genres_list_slim');

        return redirect('/admin/genres')->with('deleted_genre', 'Genre has been deleted.');
    }

    // ── Public ───────────────────────────────────────────────────────────────

    public function publicIndex()
    {
        $genres = Genre::with('photo')->withCount('animes')->orderBy('name')->get();
        return view('genres', compact('genres'));
    }
}
