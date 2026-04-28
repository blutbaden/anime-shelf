<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Photo;
use App\Models\Studio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class StudiosController extends Controller
{
    // ── Admin CRUD ───────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $search  = $request->get('search');
        $studios = Studio::with('photo')
            ->withCount('animes')
            ->when($search, fn ($q) => $q->where('name', 'ilike', "%{$search}%"))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.studios.index', compact('studios', 'search'));
    }

    public function create()
    {
        return view('admin.studios.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => 'required|max:255',
            'description'  => 'nullable',
            'founded_year' => 'nullable|integer|min:1900|max:2099',
            'website'      => 'nullable|url|max:255',
            'headquarters' => 'nullable|max:255',
            'photo_id'     => 'nullable|file|mimes:jpeg,jpg,png,gif,webp|max:2048',
        ]);

        if ($file = $request->file('photo_id')) {
            $name  = Str::uuid() . '.' . ($file->guessExtension() ?? 'bin');
            $file->move(public_path('images/studios'), $name);
            $photo = Photo::create(['file' => $name]);
            $validated['photo_id'] = $photo->id;
        }

        $studio = Studio::create($validated);
        Cache::forget('studios_home');
        Cache::forget('studios_list_slim');
        AuditLog::record('studio.created', $studio, [], ['name' => $studio->name]);

        return back()->with('success', 'Studio created successfully.');
    }

    public function edit($id)
    {
        $studio = Studio::with('photo')->findOrFail($id);
        return view('admin.studios.edit', compact('studio'));
    }

    public function update(Request $request, $id)
    {
        $studio    = Studio::findOrFail($id);
        $validated = $request->validate([
            'name'         => 'required|max:255',
            'description'  => 'nullable',
            'founded_year' => 'nullable|integer|min:1900|max:2099',
            'website'      => 'nullable|url|max:255',
            'headquarters' => 'nullable|max:255',
            'photo_id'     => 'nullable|file|mimes:jpeg,jpg,png,gif,webp|max:2048',
        ]);

        if ($file = $request->file('photo_id')) {
            $name  = Str::uuid() . '.' . ($file->guessExtension() ?? 'bin');
            $file->move(public_path('images/studios'), $name);
            $photo = Photo::create(['file' => $name]);
            $validated['photo_id'] = $photo->id;
        }

        $old = $studio->only(['name']);
        $studio->update($validated);
        Cache::forget('studios_home');
        Cache::forget('studios_list_slim');
        AuditLog::record('studio.updated', $studio, $old, $studio->only(['name']));

        return back()->with('success', 'Studio updated successfully.');
    }

    public function destroy($id)
    {
        $studio = Studio::findOrFail($id);

        if ($studio->photo) {
            $img = public_path('/images/studios/' . $studio->photo->file);
            if (file_exists($img)) unlink($img);
            Photo::destroy([$studio->photo->id]);
        }

        AuditLog::record('studio.deleted', $studio, ['name' => $studio->name], []);
        $studio->delete();
        Cache::forget('studios_home');
        Cache::forget('studios_list_slim');

        return redirect('/admin/studios')->with('deleted_studio', 'Studio has been deleted.');
    }

    // ── Public ───────────────────────────────────────────────────────────────

    public function studios(Request $request)
    {
        $search  = $request->get('search');
        $studios = Studio::with('photo')
            ->withCount('animes')
            ->when($search, fn ($q) => $q->where('name', 'ilike', "%{$search}%"))
            ->orderByDesc('animes_count')
            ->paginate(12)
            ->withQueryString();

        return view('studios', compact('studios', 'search'));
    }

    public function studio($id)
    {
        $studio = Studio::with(['photo', 'animes.photo'])->findOrFail($id);
        $animes = $studio->animes()->with('photo')->paginate(12);
        return view('studio', compact('studio', 'animes'));
    }

    public function autocomplete(Request $request)
    {
        $q = trim($request->get('q', ''));
        if (strlen($q) < 2) return response()->json([]);

        $studios = Studio::where('name', 'ilike', '%' . $q . '%')
            ->select('id', 'name')
            ->limit(10)
            ->get();

        return response()->json($studios);
    }
}
