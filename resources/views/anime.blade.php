@extends('layouts.app')

@section('title', $anime->title . ' — ' . config('app.name'))
@section('meta_description', $anime->meta_description ?: Str::limit($anime->synopsis, 160))
@section('og_title', $anime->title . ' — ' . config('app.name'))
@section('og_type', 'video.tv_show')
@section('canonical', route('anime', $anime->slug ?? $anime->id))
@if($anime->photo)
@section('og_image', asset('storage/' . $anime->photo->file))
@endif

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-6">
        <a href="{{ route('home') }}" class="hover:text-anime-600 dark:hover:text-anime-400">{{ __('Home') }}</a>
        <span>/</span>
        <a href="{{ route('animes') }}" class="hover:text-anime-600 dark:hover:text-anime-400">{{ __('Anime') }}</a>
        <span>/</span>
        <span class="text-gray-700 dark:text-gray-200 truncate max-w-xs">{{ $anime->title }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

        {{-- Left sidebar --}}
        <div class="lg:col-span-1">
            {{-- Poster --}}
            <div class="aspect-[3/4] rounded-2xl overflow-hidden bg-gray-200 dark:bg-gray-700 mb-4 shadow-lg">
                @if($anime->photo)
                    <img src="{{ asset('storage/'.$anime->photo->file) }}" alt="{{ $anime->title }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                        <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.069A1 1 0 0121 8.868V15.131a1 1 0 01-1.447.9L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    </div>
                @endif
            </div>

            {{-- Watch Episodes button --}}
            @if($anime->episodes()->where('is_active', true)->exists())
            @php $firstEp = $anime->episodes()->where('is_active', true)->first(); @endphp
            <a href="{{ route('episode.watch', [$anime, $firstEp]) }}"
               class="flex items-center justify-center gap-2 w-full py-2.5 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg text-sm transition-colors mb-3">
                ▶ {{ __('Watch Now') }} · {{ $anime->episodes()->where('is_active', true)->count() }} {{ __('Episodes') }}
            </a>
            @endif

            {{-- Action buttons --}}
            @auth
            <div class="space-y-2 mb-4">
                <form action="{{ route('watch-list.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="anime_id" value="{{ $anime->id }}">
                    <select name="status" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-anime-500 mb-2">
                        <option value="">{{ __('Add to list...') }}</option>
                        <option value="plan_to_watch" {{ $watchStatus === 'plan_to_watch' ? 'selected' : '' }}>{{ __('Plan to Watch') }}</option>
                        <option value="watching" {{ $watchStatus === 'watching' ? 'selected' : '' }}>{{ __('Watching') }}</option>
                        <option value="completed" {{ $watchStatus === 'completed' ? 'selected' : '' }}>{{ __('Completed') }}</option>
                        <option value="on_hold" {{ $watchStatus === 'on_hold' ? 'selected' : '' }}>{{ __('On Hold') }}</option>
                        <option value="dropped" {{ $watchStatus === 'dropped' ? 'selected' : '' }}>{{ __('Dropped') }}</option>
                    </select>
                    <button type="submit" class="w-full py-2.5 bg-anime-600 hover:bg-anime-700 text-white font-semibold rounded-lg text-sm transition-colors">
                        {{ $watchStatus ? __('Update Status') : __('Add to Watch List') }}
                    </button>
                </form>

                <form action="{{ route('favorites.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="anime_id" value="{{ $anime->id }}">
                    <button type="submit" class="w-full py-2.5 border-2 {{ $isFavorite ? 'border-red-500 text-red-500 bg-red-50 dark:bg-red-900/20' : 'border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300' }} font-semibold rounded-lg text-sm transition-colors hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        {{ $isFavorite ? '❤ '.__('Favorited') : '♡ '.__('Add to Favorites') }}
                    </button>
                </form>
            </div>
            @endauth

            {{-- Info card --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500 dark:text-gray-400">{{ __('Type') }}</span>
                    <span class="font-medium text-gray-900 dark:text-white">{{ $anime->type }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 dark:text-gray-400">{{ __('Episodes') }}</span>
                    <span class="font-medium text-gray-900 dark:text-white">{{ $anime->episodes ?? '?' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 dark:text-gray-400">{{ __('Seasons') }}</span>
                    <span class="font-medium text-gray-900 dark:text-white">{{ $anime->seasons ?? '—' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 dark:text-gray-400">{{ __('Duration') }}</span>
                    <span class="font-medium text-gray-900 dark:text-white">{{ $anime->episode_duration ? $anime->episode_duration.' min' : '—' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 dark:text-gray-400">{{ __('Status') }}</span>
                    <span class="font-medium capitalize text-gray-900 dark:text-white">{{ $anime->status }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 dark:text-gray-400">{{ __('Season') }}</span>
                    <span class="font-medium text-gray-900 dark:text-white">{{ $anime->season }} {{ $anime->season_year }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 dark:text-gray-400">{{ __('Rating') }}</span>
                    <span class="font-medium text-gray-900 dark:text-white">{{ $anime->rating ?? '—' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 dark:text-gray-400">{{ __('Source') }}</span>
                    <span class="font-medium text-gray-900 dark:text-white">{{ $anime->source ?? '—' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 dark:text-gray-400">{{ __('Views') }}</span>
                    <span class="font-medium text-gray-900 dark:text-white">{{ number_format($anime->views) }}</span>
                </div>
                @if($anime->aired_from)
                <div class="flex justify-between">
                    <span class="text-gray-500 dark:text-gray-400">{{ __('Aired') }}</span>
                    <span class="font-medium text-gray-900 dark:text-white text-right">
                        {{ $anime->aired_from->format('M Y') }}
                        @if($anime->aired_to) – {{ $anime->aired_to->format('M Y') }} @endif
                    </span>
                </div>
                @endif
            </div>
        </div>

        {{-- Main content --}}
        <div class="lg:col-span-3 space-y-6">

            {{-- Title block --}}
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white mb-1">{{ $anime->title }}</h1>
                @if($anime->title_japanese)
                    <p class="text-gray-500 dark:text-gray-400 text-sm">{{ $anime->title_japanese }}</p>
                @endif

                <div class="flex flex-wrap items-center gap-4 mt-3">
                    @if($anime->average_rating)
                        <div class="flex items-center gap-1 text-yellow-500">
                            @for($i=1;$i<=10;$i++)
                                <svg class="w-4 h-4 {{ $i <= round($anime->average_rating) ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @endfor
                            <span class="text-gray-700 dark:text-gray-300 font-bold ml-1">{{ number_format($anime->average_rating, 1) }}/10</span>
                            <span class="text-gray-400 text-xs">({{ $anime->reviews->count() }} {{ __('reviews') }})</span>
                        </div>
                    @endif
                    @if($anime->studio)
                        <a href="{{ route('studio.public', $anime->studio->id) }}" class="text-sm text-anime-600 dark:text-anime-400 hover:underline font-medium">{{ $anime->studio->name }}</a>
                    @endif
                </div>

                {{-- Genres --}}
                <div class="flex flex-wrap gap-2 mt-4">
                    @foreach($anime->genres as $genre)
                        <a href="{{ route('animes', ['genre'=>$genre->id]) }}" class="px-3 py-1 bg-anime-100 dark:bg-anime-900/30 text-anime-700 dark:text-anime-300 rounded-full text-xs font-medium hover:bg-anime-200 dark:hover:bg-anime-800/40 transition-colors">{{ $genre->name }}</a>
                    @endforeach
                    @foreach($anime->tags as $tag)
                        <span class="px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-full text-xs">{{ $tag->name }}</span>
                    @endforeach
                </div>
            </div>

            {{-- Synopsis --}}
            @if($anime->synopsis)
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                <h2 class="font-bold text-gray-900 dark:text-white mb-3">{{ __('Synopsis') }}</h2>
                <p class="text-gray-700 dark:text-gray-300 text-sm leading-relaxed">{{ $anime->synopsis }}</p>
            </div>
            @endif

            {{-- Trailer --}}
            @if($anime->trailer_url)
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                <h2 class="font-bold text-gray-900 dark:text-white mb-3">{{ __('Trailer') }}</h2>
                @include('components.trailer-player', ['url' => $anime->trailer_url])
            </div>
            @endif

            {{-- Episode progress (for watching users) --}}
            @auth
            @if($watchStatus === 'watching' && $anime->episodes)
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5"
                 x-data="episodeTracker({{ $currentEpisode ?? 0 }}, {{ $anime->episodes }}, {{ $anime->id }})">
                <h2 class="font-bold text-gray-900 dark:text-white mb-3">{{ __('Episode Progress') }}</h2>
                <div class="flex items-center gap-4">
                    <button @click="decrement()" class="w-8 h-8 rounded-full border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center justify-center text-lg font-bold transition-colors">−</button>
                    <div class="flex-1">
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600 dark:text-gray-400">{{ __('Episode') }} <strong x-text="current" class="text-gray-900 dark:text-white"></strong> / {{ $anime->episodes }}</span>
                            <span class="text-gray-500 dark:text-gray-400" x-text="Math.round(current/{{ $anime->episodes }}*100)+'%'"></span>
                        </div>
                        <div class="h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                            <div class="h-full bg-anime-500 rounded-full transition-all duration-300" :style="`width:${Math.round(current/{{ $anime->episodes }}*100)}%`"></div>
                        </div>
                    </div>
                    <button @click="increment()" class="w-8 h-8 rounded-full border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center justify-center text-lg font-bold transition-colors">+</button>
                </div>
                <p x-show="saved" x-transition class="text-xs text-green-500 mt-2">✓ {{ __('Saved') }}</p>
            </div>
            @endif
            @endauth

            {{-- Quotes --}}
            @if($anime->quotes->count())
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                <h2 class="font-bold text-gray-900 dark:text-white mb-4">{{ __('Notable Quotes') }}</h2>
                <div class="space-y-4">
                    @foreach($anime->quotes->take(3) as $quote)
                        <blockquote class="border-l-4 border-anime-400 pl-4">
                            <p class="text-gray-700 dark:text-gray-300 italic text-sm">"{{ $quote->body }}"</p>
                            <footer class="text-xs text-gray-500 dark:text-gray-400 mt-1">— {{ $quote->character_name }}</footer>
                        </blockquote>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Reviews --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                <h2 class="font-bold text-gray-900 dark:text-white mb-4">{{ __('Reviews') }} ({{ $anime->reviews->where('parent_id', null)->where('is_active', true)->count() }})</h2>

                @auth
                <form action="{{ route('review') }}" method="POST" class="mb-6 bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4" x-data="{ rating: 0 }">
                    @csrf
                    <input type="hidden" name="anime_id" value="{{ $anime->id }}">

                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Your Rating') }}</label>
                        <div class="flex gap-1">
                            @for($i=1;$i<=10;$i++)
                                <button type="button" @click="rating={{ $i }}" class="w-7 h-7 rounded text-sm font-bold transition-colors"
                                        :class="rating>={{ $i }} ? 'bg-yellow-400 text-white' : 'bg-gray-200 dark:bg-gray-600 text-gray-600 dark:text-gray-400'">{{ $i }}</button>
                            @endfor
                        </div>
                        <input type="hidden" name="rate" :value="rating">
                    </div>

                    <textarea name="body" rows="3" placeholder="{{ __('Share your thoughts...') }}" required
                              class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-anime-500 resize-none"></textarea>
                    <button type="submit" class="mt-2 px-5 py-2 bg-anime-600 hover:bg-anime-700 text-white font-semibold rounded-lg text-sm transition-colors">{{ __('Submit Review') }}</button>
                </form>
                @endauth

                <div class="space-y-5">
                    @forelse($anime->reviews->where('parent_id', null)->where('is_active', true)->take(10) as $review)
                        <div class="flex gap-3">
                            <div class="w-9 h-9 rounded-full bg-anime-100 dark:bg-anime-900/30 flex items-center justify-center text-anime-700 dark:text-anime-300 font-bold text-sm shrink-0">
                                {{ strtoupper(substr($review->user->name ?? '?', 0, 1)) }}
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="font-medium text-sm text-gray-900 dark:text-white">{{ $review->user->name ?? __('Unknown') }}</span>
                                    @if($review->is_verified_watcher)
                                        <span class="text-xs bg-anime-100 dark:bg-anime-900/30 text-anime-700 dark:text-anime-300 px-1.5 py-0.5 rounded-full">✓ {{ __('Verified Watcher') }}</span>
                                    @endif
                                    <span class="text-xs text-gray-400">{{ $review->created_at->diffForHumans() }}</span>
                                    <span class="ml-auto text-xs font-bold text-yellow-500">{{ $review->rate }}/10</span>
                                </div>
                                <p class="text-sm text-gray-700 dark:text-gray-300">{{ $review->body }}</p>

                                <div class="flex items-center gap-4 mt-2">
                                    @auth
                                    <form action="{{ route('vote-review', $review->id) }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="is_upvote" value="1">
                                        <button type="submit" class="text-xs text-gray-500 hover:text-green-600 dark:hover:text-green-400">👍 {{ $review->upvote }}</button>
                                    </form>
                                    <form action="{{ route('vote-review', $review->id) }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="is_upvote" value="0">
                                        <button type="submit" class="text-xs text-gray-500 hover:text-red-600 dark:hover:text-red-400">👎 {{ $review->downvote }}</button>
                                    </form>
                                    @endauth
                                </div>

                                {{-- Replies --}}
                                @foreach($review->replies->where('is_active', true) as $reply)
                                    <div class="mt-3 ml-4 pl-3 border-l-2 border-gray-200 dark:border-gray-600 flex gap-2">
                                        <div class="w-7 h-7 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-600 dark:text-gray-300 font-bold text-xs shrink-0">
                                            {{ strtoupper(substr($reply->user->name ?? '?', 0, 1)) }}
                                        </div>
                                        <div>
                                            <span class="font-medium text-xs text-gray-900 dark:text-white">{{ $reply->user->name ?? __('Unknown') }}</span>
                                            <span class="text-xs text-gray-400 ml-2">{{ $reply->created_at->diffForHumans() }}</span>
                                            <p class="text-xs text-gray-700 dark:text-gray-300 mt-0.5">{{ $reply->body }}</p>
                                        </div>
                                    </div>
                                @endforeach

                                @auth
                                <div x-data="{ open: false }" class="mt-2">
                                    <button @click="open=!open" class="text-xs text-anime-600 dark:text-anime-400 hover:underline">{{ __('Reply') }}</button>
                                    <form x-show="open" action="{{ route('review.reply') }}" method="POST" class="mt-2">
                                        @csrf
                                        <input type="hidden" name="anime_id" value="{{ $anime->id }}">
                                        <input type="hidden" name="parent_id" value="{{ $review->id }}">
                                        <textarea name="body" rows="2" placeholder="{{ __('Write a reply...') }}" required class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-anime-500 resize-none"></textarea>
                                        <button type="submit" class="mt-1 px-3 py-1 bg-anime-600 text-white text-xs rounded-lg">{{ __('Submit') }}</button>
                                    </form>
                                </div>
                                @endauth
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 dark:text-gray-400 text-sm text-center py-4">{{ __('No reviews yet. Be the first!') }}</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function episodeTracker(initial, total, animeId) {
    return {
        current: initial,
        total: total,
        saved: false,
        timer: null,
        increment() { if (this.current < this.total) { this.current++; this.autoSave(); } },
        decrement() { if (this.current > 0) { this.current--; this.autoSave(); } },
        autoSave() {
            clearTimeout(this.timer);
            this.timer = setTimeout(() => this.save(), 800);
        },
        save() {
            fetch('{{ route('watch-list.progress') }}', {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ anime_id: animeId, current_episode: this.current })
            }).then(r => r.ok && (this.saved = true, setTimeout(() => this.saved = false, 2000)));
        }
    }
}
</script>
@endpush
