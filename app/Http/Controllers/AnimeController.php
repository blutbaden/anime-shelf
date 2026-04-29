<?php

namespace App\Http\Controllers;

use App\Mail\NewAnimeMail;
use App\Models\ActivityLog;
use App\Models\Anime;
use App\Models\AuditLog;
use App\Models\Genre;
use App\Models\Photo;
use App\Models\Studio;
use App\Models\Subscriber;
use App\Models\Tag;
use App\Models\User;
use App\Notifications\NewAnimeNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class AnimeController extends Controller
{
    // ── Admin CRUD ───────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $search = $request->get('search');

        $animes = Anime::with(['photo', 'studio', 'genres'])
            ->when($search, function ($q) use ($search) {
                $term = '%' . $search . '%';
                $q->where(function ($q2) use ($term) {
                    $q2->where('title', 'ilike', $term)
                       ->orWhere('title_japanese', 'ilike', $term)
                       ->orWhereHas('studio', fn ($s) => $s->where('name', 'ilike', $term));
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('admin.animes.index', compact('animes', 'search'));
    }

    public function create()
    {
        $studios = Studio::select('id', 'name')->orderBy('name')->get();
        $genres  = Genre::select('id', 'name')->orderBy('name')->get();
        $tags    = Tag::select('id', 'name')->orderBy('name')->get();

        return view('admin.animes.create', compact('studios', 'genres', 'tags'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'            => 'required|max:255',
            'title_japanese'   => 'nullable|max:255',
            'type'             => 'required|in:TV,Movie,OVA,ONA,Special',
            'seasons'          => 'nullable|integer|min:1',
            'episodes'         => 'nullable|integer|min:0',
            'episode_duration' => 'nullable|integer|min:1',
            'status'           => 'required|in:airing,finished,upcoming',
            'season'           => 'nullable|in:Winter,Spring,Summer,Fall',
            'season_year'      => 'nullable|integer|min:1900|max:2099',
            'source'           => 'nullable|in:Manga,Light Novel,Visual Novel,Original,Game,Web Manga,Other',
            'synopsis'         => 'nullable',
            'trailer_url'      => 'nullable|url|max:500',
            'mal_id'           => 'nullable|integer|unique:animes,mal_id',
            'aired_from'       => 'nullable|date',
            'aired_to'         => 'nullable|date',
            'rating'           => 'nullable|in:G,PG,PG-13,R,R+',
            'language'         => 'nullable|max:50',
            'meta_title'       => 'nullable|max:255',
            'meta_description' => 'nullable|max:500',
            'studio_id'        => 'required|exists:studios,id',
            'photo_id'         => 'nullable|file|mimes:jpeg,jpg,png,gif,webp|max:2048',
            'genres'           => 'nullable|array',
            'genres.*'         => 'exists:genres,id',
            'tags'             => 'nullable|array',
            'tags.*'           => 'exists:tags,id',
        ]);

        if ($file = $request->file('photo_id')) {
            $name  = Str::uuid() . '.' . ($file->guessExtension() ?? 'bin');
            $file->move(public_path('images/anime'), $name);
            $photo = Photo::create(['file' => $name]);
            $validated['photo_id'] = $photo->id;
        }

        $genres = $validated['genres'] ?? [];
        $tags   = $validated['tags'] ?? [];
        unset($validated['genres'], $validated['tags']);

        $anime = Anime::create($validated);
        $anime->genres()->sync($genres);
        $anime->tags()->sync($tags);

        // Notify all active users (in-app)
        try {
            $users = User::where('is_active', true)->get();
            Notification::send($users, new NewAnimeNotification($anime));
        } catch (\Exception $e) {}

        // Newsletter to subscribers (queued)
        try {
            $anime->load(['photo', 'studio', 'genres']);
            Subscriber::chunk(100, function ($subscribers) use ($anime) {
                foreach ($subscribers as $subscriber) {
                    $unsubscribeUrl = URL::signedRoute('unsubscribe', ['email' => $subscriber->email]);
                    Mail::to($subscriber->email)->queue(new NewAnimeMail($anime, $unsubscribeUrl));
                }
            });
        } catch (\Exception $e) {}

        Cache::forget('trending_anime');
        AuditLog::record('anime.created', $anime, [], ['title' => $anime->title]);

        return back()->with('success', 'Anime created successfully.');
    }

    public function show($id)
    {
        $anime = Anime::with(['photo', 'studio', 'genres', 'tags', 'reviews', 'favoritedByUsers'])->findOrFail($id);
        $episodesBySeries = $anime->episodes()->orderBy('series')->orderBy('number')->get()->groupBy('series');
        return view('admin.animes.show', compact('anime', 'episodesBySeries'));
    }

    public function edit($id)
    {
        $anime   = Anime::with(['photo', 'genres', 'tags', 'studio'])->findOrFail($id);
        $studios = Studio::select('id', 'name')->orderBy('name')->get();
        $genres  = Genre::select('id', 'name')->orderBy('name')->get();
        $tags    = Tag::select('id', 'name')->orderBy('name')->get();

        return view('admin.animes.edit', compact('anime', 'studios', 'genres', 'tags'));
    }

    public function update(Request $request, $id)
    {
        $anime = Anime::findOrFail($id);

        $validated = $request->validate([
            'title'            => 'required|max:255',
            'title_japanese'   => 'nullable|max:255',
            'type'             => 'required|in:TV,Movie,OVA,ONA,Special',
            'seasons'          => 'nullable|integer|min:1',
            'episodes'         => 'nullable|integer|min:0',
            'episode_duration' => 'nullable|integer|min:1',
            'status'           => 'required|in:airing,finished,upcoming',
            'season'           => 'nullable|in:Winter,Spring,Summer,Fall',
            'season_year'      => 'nullable|integer|min:1900|max:2099',
            'source'           => 'nullable|in:Manga,Light Novel,Visual Novel,Original,Game,Web Manga,Other',
            'synopsis'         => 'nullable',
            'trailer_url'      => 'nullable|url|max:500',
            'mal_id'           => 'nullable|integer|unique:animes,mal_id,' . $id,
            'aired_from'       => 'nullable|date',
            'aired_to'         => 'nullable|date',
            'rating'           => 'nullable|in:G,PG,PG-13,R,R+',
            'language'         => 'nullable|max:50',
            'meta_title'       => 'nullable|max:255',
            'meta_description' => 'nullable|max:500',
            'studio_id'        => 'required|exists:studios,id',
            'photo_id'         => 'nullable|file|mimes:jpeg,jpg,png,gif,webp|max:2048',
            'genres'           => 'nullable|array',
            'genres.*'         => 'exists:genres,id',
            'tags'             => 'nullable|array',
            'tags.*'           => 'exists:tags,id',
        ]);

        if ($file = $request->file('photo_id')) {
            $name  = Str::uuid() . '.' . ($file->guessExtension() ?? 'bin');
            $file->move(public_path('images/anime'), $name);
            $photo = Photo::create(['file' => $name]);
            $validated['photo_id'] = $photo->id;
        }

        $genres = $validated['genres'] ?? [];
        $tags   = $validated['tags'] ?? [];
        unset($validated['genres'], $validated['tags']);

        $old = $anime->only(['title', 'studio_id', 'status']);
        $anime->update($validated);
        $anime->genres()->sync($genres);
        $anime->tags()->sync($tags);

        Cache::forget("anime_{$id}");
        AuditLog::record('anime.updated', $anime, $old, $anime->only(['title', 'studio_id']));

        return back()->with('success', 'Anime updated successfully.');
    }

    public function destroy($id)
    {
        $anime = Anime::findOrFail($id);
        AuditLog::record('anime.deleted', $anime, ['title' => $anime->title], []);

        if ($anime->photo) {
            $img = public_path('/images/anime/' . $anime->photo->file);
            if (file_exists($img)) {
                unlink($img);
            }
            Photo::destroy([$anime->photo->id]);
        }

        $anime->delete();

        return redirect('/admin/animes')->with('deleted_anime', 'Anime has been deleted.');
    }

    // ── Public catalog ───────────────────────────────────────────────────────

    public function animes(Request $request)
    {
        $sort     = $request->get('sort', 'popular');
        $search   = $request->get('search');
        $studioId = $request->get('studio_id');
        $genreId  = $request->get('genre_id');
        $type     = $request->get('type');
        $status   = $request->get('status');
        $season   = $request->get('season');
        $year     = $request->get('year');

        $query = Anime::with(['photo', 'studio', 'genres'])
            ->withAvg(['reviews as avg_rating' => fn ($q) => $q->where('is_active', true)], 'rate')
            ->withCount(['reviews as reviews_count' => fn ($q) => $q->where('is_active', true)]);

        if ($search) {
            $term = '%' . $search . '%';
            $query->where(function ($q) use ($term) {
                $q->where('title', 'ilike', $term)
                  ->orWhere('title_japanese', 'ilike', $term)
                  ->orWhere('synopsis', 'ilike', $term)
                  ->orWhereHas('studio', fn ($s) => $s->where('name', 'ilike', $term))
                  ->orWhereHas('genres', fn ($g) => $g->where('name', 'ilike', $term));
            });
        }

        if ($studioId) $query->where('studio_id', $studioId);
        if ($genreId)  $query->whereHas('genres', fn ($q) => $q->where('genres.id', $genreId));
        if ($type)     $query->where('type', $type);
        if ($status)   $query->where('status', $status);
        if ($season)   $query->where('season', $season);
        if ($year)     $query->where('season_year', $year);

        $animes  = $query->sortBy($sort)->paginate(12)->withQueryString();

        $genres  = Cache::remember('genres_list_slim', 3600,
            fn () => Genre::select('id', 'name')->orderBy('name')->get()
        );
        $studios = Cache::remember('studios_list_slim', 3600,
            fn () => Studio::select('id', 'name')->withCount('animes')->orderByDesc('animes_count')->limit(200)->get()
        );

        return view('animes', compact('animes', 'genres', 'studios', 'sort', 'search'));
    }

    public function anime(Request $request, $id)
    {
        $anime = Cache::remember("anime_{$id}", 300, fn () =>
            Anime::with(['photo', 'studio', 'genres', 'tags'])
                ->withAvg(['reviews as avg_rating' => fn ($q) => $q->where('is_active', true)], 'rate')
                ->when(is_numeric($id), fn ($q) => $q->where('id', $id), fn ($q) => $q->where('slug', $id))
                ->firstOrFail()
        );

        $sort   = $request->get('sort', 'newest');
        $userId = auth()->id();

        $reviews = $anime->reviews()
            ->where('is_active', true)
            ->whereNull('parent_id')
            ->with([
                'user.photo',
                'replies'  => fn ($q) => $q->with('user.photo')->limit(10),
                'users'    => fn ($q) => $userId ? $q->where('users.id', $userId) : $q->whereRaw('1=0'),
            ])
            ->when($sort === 'helpful', fn ($q) => $q->orderBy('upvote', 'desc'))
            ->when($sort === 'highest', fn ($q) => $q->orderBy('rate', 'desc'))
            ->when($sort === 'lowest',  fn ($q) => $q->orderBy('rate'))
            ->when($sort === 'newest',  fn ($q) => $q->orderBy('created_at', 'desc'))
            ->paginate(10);

        // Rating distribution 1–10
        $ratingDist  = $anime->reviews()->where('is_active', true)->whereNull('parent_id')
            ->selectRaw('rate, count(*) as total')->groupBy('rate')
            ->pluck('total', 'rate')->toArray();
        $ratingTotal = array_sum($ratingDist) ?: 1;

        $animeId    = $anime->id;
        $isFavorite = auth()->check() && auth()->user()->favoriteAnime()->where('anime_id', $animeId)->exists();
        $shelfEntry = auth()->check()
            ? auth()->user()->watchList()->where('anime_id', $animeId)->first()
            : null;
        $inWatchList   = (bool) $shelfEntry;
        $watchStatus   = $shelfEntry?->pivot->status;
        $hasCompleted  = auth()->check() && auth()->user()->watchHistory()->where('anime_id', $animeId)->exists();
        $userReview    = auth()->check()
            ? $anime->reviews()->where('user_id', auth()->id())->whereNull('parent_id')->first()
            : null;

        $related = Cache::remember("related_{$animeId}", 600, function () use ($anime) {
            $genreIds = $anime->genres->pluck('id');
            return Anime::with(['photo', 'studio'])
                ->whereHas('genres', fn ($q) => $q->whereIn('genres.id', $genreIds))
                ->where('id', '!=', $anime->id)
                ->inRandomOrder()
                ->limit(4)
                ->get();
        });

        // Track recently viewed
        if (auth()->check()) {
            DB::table('recently_viewed')->upsert(
                ['user_id' => auth()->id(), 'anime_id' => $animeId, 'viewed_at' => now(), 'created_at' => now(), 'updated_at' => now()],
                ['user_id', 'anime_id'],
                ['viewed_at', 'updated_at']
            );
        }

        $anime->increment('views');

        $episodesBySeries = $anime->episodes()
            ->where('is_active', true)
            ->orderBy('series')
            ->orderBy('number')
            ->get()
            ->groupBy('series');

        return view('anime', compact(
            'anime', 'reviews', 'isFavorite', 'inWatchList',
            'watchStatus', 'shelfEntry', 'hasCompleted', 'userReview',
            'related', 'sort', 'ratingDist', 'ratingTotal', 'episodesBySeries'
        ));
    }

    public function autocomplete(Request $request)
    {
        $q = trim($request->get('q', ''));
        if (strlen($q) < 3) {
            return response()->json([]);
        }

        $term = '%' . $q . '%';

        $animes = Anime::with(['photo', 'studio'])
            ->where(fn ($qry) => $qry
                ->where('title', 'ilike', $term)
                ->orWhere('title_japanese', 'ilike', $term)
                ->orWhereHas('studio', fn ($s) => $s->where('name', 'ilike', $term))
            )
            ->orderBy('views', 'desc')
            ->limit(5)
            ->get()
            ->map(fn ($a) => [
                'id'     => $a->id,
                'title'  => $a->title,
                'studio' => $a->studio?->name,
                'type'   => $a->type,
                'image'  => $a->photo?->file ?? 'default.png',
                'url'    => route('anime', $a->slug ?? $a->id),
            ]);

        return response()->json($animes);
    }

    // ── Admin bulk actions ───────────────────────────────────────────────────

    public function bulkDestroy(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'exists:animes,id']);

        Anime::whereIn('id', $request->ids)->each(function ($anime) {
            if ($anime->photo) {
                $img = public_path('/images/anime/' . $anime->photo->file);
                if (file_exists($img)) {
                    unlink($img);
                }
                Photo::destroy([$anime->photo->id]);
            }
            $anime->delete();
        });

        return back()->with('success', count($request->ids) . ' anime deleted.');
    }

    public function importCsv(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:csv,txt|max:2048']);

        $file   = $request->file('file');
        $rows   = array_map('str_getcsv', file($file->getPathname()));
        $header = array_shift($rows);
        $header = array_map('trim', $header);

        $imported = 0;

        foreach ($rows as $row) {
            $data = array_combine($header, $row);
            if (empty($data['title'])) continue;

            // Sanitize against CSV formula injection
            $data = array_map(function ($val) {
                if (is_string($val) && strlen($val) > 0 && in_array($val[0], ['=', '+', '-', '@', '|', '%'], true)) {
                    return "'" . $val;
                }
                return $val;
            }, $data);

            $studio = Studio::firstOrCreate(['name' => trim($data['studio'] ?? 'Unknown')]);

            Anime::firstOrCreate(
                ['title' => trim($data['title'])],
                [
                    'studio_id'   => $studio->id,
                    'type'        => in_array($data['type'] ?? '', ['TV', 'Movie', 'OVA', 'ONA', 'Special']) ? $data['type'] : 'TV',
                    'episodes'    => is_numeric($data['episodes'] ?? '') ? (int) $data['episodes'] : 0,
                    'status'      => in_array($data['status'] ?? '', ['airing', 'finished', 'upcoming']) ? $data['status'] : 'finished',
                    'synopsis'    => $data['synopsis'] ?? null,
                    'season_year' => is_numeric($data['year'] ?? '') ? (int) $data['year'] : null,
                ]
            );

            $imported++;
        }

        return back()->with('success', "{$imported} anime imported from CSV.");
    }
}
