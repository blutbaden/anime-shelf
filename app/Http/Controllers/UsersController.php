<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Photo;
use App\Models\Role;
use App\Models\User;
use App\Models\WatchGoal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersController extends Controller
{
    // ── Admin CRUD ───────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $search = $request->get('search');
        $users  = User::with(['role', 'photo'])
            ->when($search, fn ($q) => $q->where('name', 'ilike', "%{$search}%")
                ->orWhere('email', 'ilike', "%{$search}%"))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'search'));
    }

    public function create()
    {
        $roles = Role::pluck('name', 'id')->all();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|max:30',
            'email'     => 'required|email|unique:users',
            'password'  => ['required', 'min:8', 'regex:/[A-Z]/', 'regex:/[0-9]/'],
            'role_id'   => 'required|exists:roles,id',
            'is_active' => 'required|boolean',
            'photo_id'  => 'nullable|file|mimes:jpeg,jpg,png,gif,webp|max:2048',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        if ($file = $request->file('photo_id')) {
            $name  = Str::uuid() . '.' . ($file->guessExtension() ?? 'bin');
            $file->move(public_path('images/users'), $name);
            $photo = Photo::create(['file' => $name]);
            $validated['photo_id'] = $photo->id;
        }

        User::create($validated);
        AuditLog::record('user.created', null, [], ['email' => $validated['email']]);

        return back()->with('success', 'User created successfully.');
    }

    public function show($id) {}

    public function edit($id)
    {
        $user  = User::findOrFail($id);
        $roles = Role::pluck('name', 'id')->all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name'         => 'required|max:255',
            'email'        => 'required|email|unique:users,email,' . $id,
            'role_id'      => 'required|exists:roles,id',
            'is_active'    => 'required|boolean',
            'photo_id'     => 'nullable|file|mimes:jpeg,jpg,png,gif,webp|max:2048',
            'new_password' => ['nullable', 'min:8', 'confirmed', 'regex:/[A-Z]/', 'regex:/[0-9]/'],
        ]);

        if ($file = $request->file('photo_id')) {
            $name  = Str::uuid() . '.' . ($file->guessExtension() ?? 'bin');
            $file->move(public_path('images/users'), $name);
            $photo = Photo::create(['file' => $name]);
            $validated['photo_id'] = $photo->id;
        }

        if (! empty($validated['new_password'])) {
            $validated['password'] = Hash::make($validated['new_password']);
        }
        unset($validated['new_password'], $validated['new_password_confirmation']);

        $old = $user->only(['name', 'email', 'role_id', 'is_active']);
        $user->update($validated);
        AuditLog::record('user.updated', $user, $old, $validated);

        return back()->with('success', 'User updated successfully.');
    }

    public function toggleStatus($id)
    {
        $user            = User::findOrFail($id);
        $old             = $user->is_active;
        $user->is_active = ! $user->is_active;
        $user->save();

        AuditLog::record('user.status_toggled', $user, ['is_active' => $old], ['is_active' => $user->is_active]);

        return back()->with('success', "'{$user->name}' is now " . ($user->is_active ? 'active' : 'inactive') . '.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->photo) {
            $img = public_path('/images/users/' . $user->photo->file);
            if (file_exists($img)) {
                unlink($img);
            }
            Photo::destroy([$user->photo->id]);
        }

        AuditLog::record('user.deleted', $user, ['email' => $user->email], []);
        $user->delete();

        return redirect('/admin/users')->with('deleted_user', 'User has been deleted.');
    }

    // ── Private user profile ─────────────────────────────────────────────────

    public function userProfile()
    {
        $user = User::with(['photo', 'reviews.anime.photo'])->findOrFail(auth()->id());

        $recentlyViewed = $user->recentlyViewed()->with(['photo', 'studio'])->limit(6)->get();
        $watchList      = $user->watchList()->with(['photo', 'studio'])->paginate(6);
        $watchHistory   = $user->watchHistory()->with(['photo', 'studio'])
                               ->orderByPivot('completed_at', 'desc')->paginate(6);
        $activityLogs   = $user->activityLogs()->latest()->limit(10)->get();

        return view('profile', compact('user', 'recentlyViewed', 'watchList', 'watchHistory', 'activityLogs'));
    }

    public function stats()
    {
        $user = auth()->user();

        $animeCompleted = $user->watchHistory()->with('genres')->get();
        $totalCompleted = $animeCompleted->count();
        $totalEpisodes  = $animeCompleted->sum('episodes');
        $thisYear       = $animeCompleted->filter(
            fn ($a) => $a->pivot->completed_at && \Carbon\Carbon::parse($a->pivot->completed_at)->year === now()->year
        )->count();

        // Favorite genre
        $genreCounts = [];
        foreach ($animeCompleted as $anime) {
            foreach ($anime->genres as $genre) {
                $genreCounts[$genre->name] = ($genreCounts[$genre->name] ?? 0) + 1;
            }
        }
        arsort($genreCounts);
        $favoriteGenre = array_key_first($genreCounts);

        // Shelf counts
        $shelfCounts = DB::table('watch_list')
            ->selectRaw('status, count(*) as total')
            ->where('user_id', $user->id)
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $reviewCount = $user->reviews()->count();

        // Anime completed per month (last 12 months)
        $monthlyCompleted = DB::table('watch_history')
            ->selectRaw("TO_CHAR(completed_at, 'Mon') as month, TO_CHAR(completed_at, 'YYYY-MM') as ym, count(*) as total")
            ->where('user_id', $user->id)
            ->where('completed_at', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('ym', 'month')
            ->orderBy('ym')
            ->get();

        $currentYear = now()->year;
        $goal        = $user->watchGoalForYear($currentYear);
        $goalTarget  = $goal?->goal ?? 0;
        $goalPct     = $goalTarget > 0 ? min(100, round($thisYear / $goalTarget * 100)) : 0;

        return view('stats', compact(
            'totalCompleted', 'totalEpisodes', 'thisYear', 'favoriteGenre',
            'shelfCounts', 'reviewCount', 'monthlyCompleted', 'genreCounts',
            'goalTarget', 'goalPct', 'currentYear'
        ));
    }

    public function setGoal(Request $request)
    {
        $request->validate(['goal' => 'required|integer|min:1|max:365']);

        WatchGoal::updateOrCreate(
            ['user_id' => auth()->id(), 'year' => now()->year],
            ['goal' => $request->goal]
        );

        return back()->with('success', __('Watch goal updated.'));
    }

    public function publicProfile($id)
    {
        $user = User::with(['photo'])->where('is_active', true)->findOrFail($id);

        $reviews = $user->reviews()
            ->where('is_active', true)
            ->with('anime.photo')
            ->latest()
            ->paginate(6);

        $animeCompleted = $user->watchHistory()->count();
        $reviewCount    = $user->reviews()->where('is_active', true)->count();

        $shelfCounts = DB::table('watch_list')
            ->selectRaw('status, count(*) as total')
            ->where('user_id', $user->id)
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $favoriteGenre = DB::table('watch_history')
            ->join('animes', 'watch_history.anime_id', '=', 'animes.id')
            ->join('anime_genre', 'animes.id', '=', 'anime_genre.anime_id')
            ->join('genres', 'anime_genre.genre_id', '=', 'genres.id')
            ->where('watch_history.user_id', $user->id)
            ->selectRaw('genres.name, count(*) as total')
            ->groupBy('genres.name')
            ->orderByDesc('total')
            ->value('genres.name');

        $currentlyWatching = $user->currentlyWatching()->with('photo')->limit(4)->get();

        return view('user-profile', compact(
            'user', 'reviews', 'animeCompleted', 'reviewCount',
            'shelfCounts', 'favoriteGenre', 'currentlyWatching'
        ));
    }

    public function updateUser(Request $request)
    {
        $user      = User::findOrFail(auth()->id());
        $validated = $request->validate([
            'name'     => 'required|max:30',
            'photo_id' => 'nullable|file|mimes:jpeg,jpg,png|max:1024',
        ]);

        if ($file = $request->file('photo_id')) {
            $name  = Str::uuid() . '.' . ($file->guessExtension() ?? 'bin');
            $file->move(public_path('images/users'), $name);
            $photo = Photo::create(['file' => $name]);
            $validated['photo_id'] = $photo->id;
        }

        $user->update($validated);
        return back()->with('success', __('Profile updated successfully.'));
    }

    public function changePassword(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'current_password' => 'required',
            'new_password'     => ['required', 'min:8', 'confirmed', 'regex:/[A-Z]/', 'regex:/[0-9]/'],
        ]);

        if (! Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password does not match.']);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('success', __('Password updated successfully.'));
    }

    public function deleteAccount(Request $request)
    {
        $request->validate(['password' => 'required']);
        $user = Auth::user();

        if (! Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Incorrect password.']);
        }

        if ($user->photo) {
            $img = public_path('/images/users/' . $user->photo->file);
            if (file_exists($img)) {
                unlink($img);
            }
            Photo::destroy([$user->photo->id]);
        }

        Auth::logout();
        $user->delete();

        return redirect('/')->with('success', __('Your account has been deleted.'));
    }
}
