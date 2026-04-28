@extends('layouts.app')

@section('title', __('Discover Anime'))

@section('content')

{{-- Hero Spotlight Carousel --}}
@if($spotlight->count())
<section
    x-data="{
        current: 0,
        total: {{ $spotlight->count() }},
        timer: null,
        start() { this.timer = setInterval(() => this.next(), 7000); },
        stop()  { clearInterval(this.timer); },
        next()  { this.current = (this.current + 1) % this.total; },
        prev()  { this.current = (this.current - 1 + this.total) % this.total; },
        goto(i) { this.current = i; this.stop(); this.start(); }
    }"
    x-init="start()"
    class="relative h-[85vh] min-h-[520px] max-h-[780px] overflow-hidden bg-gray-950 select-none"
>
    @foreach($spotlight as $i => $anime)
    @php
        $imgUrl  = $anime->photo ? asset('storage/' . $anime->photo->file) : null;
        $firstEp = $anime->episodes_count > 0
            ? $anime->episodes()->where('is_active', true)->first()
            : null;
    @endphp

    <div
        x-show="current === {{ $i }}"
        x-transition:enter="transition-opacity duration-700"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity duration-500"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="absolute inset-0"
    >
        {{-- Blurred background --}}
        @if($imgUrl)
        <div class="absolute inset-0">
            <img src="{{ $imgUrl }}" alt="" class="w-full h-full object-cover scale-110 blur-md opacity-25">
        </div>
        @endif

        {{-- Gradient overlays --}}
        <div class="absolute inset-0 bg-gradient-to-r from-gray-950 via-gray-950/85 to-transparent"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-gray-950 via-transparent to-gray-950/50"></div>

        {{-- Right artwork --}}
        @if($imgUrl)
        <div class="absolute right-0 top-0 h-full w-1/2 lg:w-5/12 pointer-events-none hidden md:block">
            <img src="{{ $imgUrl }}" alt="{{ $anime->title }}" class="h-full w-full object-cover object-top">
            <div class="absolute inset-0 bg-gradient-to-r from-gray-950 via-gray-950/30 to-transparent"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-gray-950 via-transparent to-transparent"></div>
        </div>
        @endif

        {{-- Content --}}
        <div class="relative h-full flex items-center">
            <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12 w-full">
                <div class="max-w-xl lg:max-w-2xl">

                    <div class="inline-flex items-center gap-2 mb-4">
                        <span class="w-2 h-2 rounded-full bg-yellow-400 animate-pulse"></span>
                        <span class="text-yellow-400 text-xs font-bold uppercase tracking-widest">#{{ $i + 1 }} {{ __('Spotlight') }}</span>
                    </div>

                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white leading-tight mb-4 drop-shadow-lg line-clamp-2">
                        {{ $anime->title }}
                    </h1>

                    <div class="flex flex-wrap items-center gap-3 mb-5 text-sm">
                        <span class="px-2 py-0.5 bg-anime-600/90 text-white rounded font-semibold text-xs">{{ $anime->type }}</span>
                        @if($anime->episode_duration)
                            <span class="flex items-center gap-1 text-gray-300">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ $anime->episode_duration }}m
                            </span>
                        @endif
                        @if($anime->season_year)
                            <span class="text-gray-300">{{ $anime->season_year }}</span>
                        @endif
                        @if($anime->rating)
                            <span class="px-1.5 py-0.5 border border-gray-500 text-gray-300 rounded text-xs">{{ $anime->rating }}</span>
                        @endif
                        @if($anime->avg_rating)
                            <span class="flex items-center gap-1 text-yellow-400 font-semibold">
                                ★ {{ number_format($anime->avg_rating, 1) }}
                            </span>
                        @endif
                        @if($anime->seasons > 1)
                            <span class="text-gray-300 text-xs font-semibold">{{ $anime->seasons }} {{ __('Seasons') }}</span>
                        @endif
                        @if($anime->episodes_count > 0)
                            <span class="flex items-center gap-1 text-green-400 text-xs font-semibold">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                {{ $anime->episodes_count }} {{ __('eps') }}
                            </span>
                        @endif
                    </div>

                    @if($anime->synopsis)
                    <p class="text-gray-300 text-sm leading-relaxed mb-6 line-clamp-3">{{ $anime->synopsis }}</p>
                    @endif

                    <div class="flex flex-wrap gap-3">
                        @if($firstEp)
                            <a href="{{ route('episode.watch', [$anime, $firstEp]) }}"
                               class="inline-flex items-center gap-2 px-6 py-3 bg-yellow-400 hover:bg-yellow-300 text-gray-900 font-bold rounded-xl text-sm transition-colors shadow-lg shadow-yellow-500/20">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                {{ __('Watch Now') }}
                            </a>
                        @else
                            <a href="{{ route('anime', $anime) }}"
                               class="inline-flex items-center gap-2 px-6 py-3 bg-yellow-400 hover:bg-yellow-300 text-gray-900 font-bold rounded-xl text-sm transition-colors shadow-lg shadow-yellow-500/20">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                {{ __('Watch Now') }}
                            </a>
                        @endif
                        <a href="{{ route('anime', $anime) }}"
                           class="inline-flex items-center gap-2 px-6 py-3 bg-white/10 hover:bg-white/20 text-white font-semibold rounded-xl text-sm border border-white/20 transition-colors backdrop-blur-sm">
                            {{ __('Detail') }} ›
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
    @endforeach

    {{-- Arrows --}}
    <button @click="prev(); stop(); start()"
            class="absolute left-4 top-1/2 -translate-y-1/2 z-20 w-10 h-10 rounded-full bg-black/40 hover:bg-black/70 text-white flex items-center justify-center border border-white/10 transition-colors backdrop-blur-sm">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </button>
    <button @click="next(); stop(); start()"
            class="absolute right-4 top-1/2 -translate-y-1/2 z-20 w-10 h-10 rounded-full bg-black/40 hover:bg-black/70 text-white flex items-center justify-center border border-white/10 transition-colors backdrop-blur-sm">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    </button>

    {{-- Dot indicators --}}
    <div class="absolute bottom-6 left-1/2 -translate-x-1/2 z-20 flex gap-2">
        @foreach($spotlight as $i => $_)
        <button @click="goto({{ $i }})"
                class="transition-all duration-300 rounded-full"
                :class="current === {{ $i }} ? 'w-6 h-2 bg-yellow-400' : 'w-2 h-2 bg-white/40 hover:bg-white/70'">
        </button>
        @endforeach
    </div>

</section>
@else
<section class="relative bg-gradient-to-br from-gray-900 via-anime-950 to-gray-900 text-white overflow-hidden">
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-28">
        <div class="max-w-2xl">
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold leading-tight mb-4">
                {{ __('Your Anime') }}<br><span class="text-anime-400">{{ __('Universe') }}</span>
            </h1>
            <p class="text-lg text-gray-300 mb-8">{{ __('Track, discover, and share your anime journey.') }}</p>
            <a href="{{ route('animes') }}" class="px-6 py-3 bg-anime-600 hover:bg-anime-700 text-white font-semibold rounded-xl transition-colors">{{ __('Browse Anime') }}</a>
        </div>
    </div>
</section>
@endif

{{-- Daily Quote --}}
@if($quote)
<section class="bg-anime-600 dark:bg-anime-800 text-white py-8">
    <div class="max-w-4xl mx-auto px-4 text-center">
        <p class="text-lg italic font-medium">"{{ $quote->body }}"</p>
        <p class="mt-2 text-anime-200 text-sm">— {{ $quote->character_name }}, <span class="font-semibold">{{ $quote->anime->title }}</span></p>
    </div>
</section>
@endif

{{-- Trending --}}
@if($trendingAnime->count())
<section class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Trending Now') }}</h2>
            <a href="{{ route('animes') }}" class="text-anime-600 dark:text-anime-400 text-sm font-medium hover:underline">{{ __('View all') }} →</a>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
            @foreach($trendingAnime as $anime)
                <a href="{{ route('anime', $anime->id) }}" class="group">
                    <div class="relative aspect-[3/4] rounded-xl overflow-hidden bg-gray-200 dark:bg-gray-700 mb-2">
                        @if($anime->photo)
                            <img src="{{ asset('storage/'.$anime->photo->file) }}" alt="{{ $anime->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.069A1 1 0 0121 8.868V15.131a1 1 0 01-1.447.9L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            </div>
                        @endif
                        @if($anime->average_rating)
                            <div class="absolute top-2 left-2 bg-black/70 text-yellow-400 text-xs font-bold px-1.5 py-0.5 rounded">
                                ★ {{ number_format($anime->average_rating, 1) }}
                            </div>
                        @endif
                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 to-transparent px-2 pt-6 pb-2 flex items-end justify-end gap-1">
                            @if($anime->seasons > 1)
                                <span class="text-white text-xs font-semibold bg-white/20 backdrop-blur-sm px-1.5 py-0.5 rounded">{{ $anime->seasons }}S</span>
                            @endif
                            @if($anime->episodes)
                                <span class="text-white text-xs font-semibold bg-white/20 backdrop-blur-sm px-1.5 py-0.5 rounded">{{ $anime->episodes }} {{ __('eps') }}</span>
                            @endif
                        </div>
                    </div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $anime->title }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $anime->type }} · {{ $anime->season_year }}</p>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Currently Airing --}}
@if($airingAnime->count())
<section class="py-12 bg-gray-50 dark:bg-gray-800/50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Currently Airing') }}</h2>
            <a href="{{ route('animes', ['status'=>'airing']) }}" class="text-anime-600 dark:text-anime-400 text-sm font-medium hover:underline">{{ __('View all') }} →</a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach($airingAnime->take(8) as $anime)
                <a href="{{ route('anime', $anime->id) }}" class="group flex gap-3 bg-white dark:bg-gray-800 rounded-xl p-3 border border-gray-100 dark:border-gray-700 hover:border-anime-300 dark:hover:border-anime-700 transition-colors shadow-sm">
                    <div class="w-16 h-20 rounded-lg overflow-hidden shrink-0 bg-gray-200 dark:bg-gray-700">
                        @if($anime->photo)
                            <img src="{{ asset('storage/'.$anime->photo->file) }}" alt="{{ $anime->title }}" class="w-full h-full object-cover">
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-sm text-gray-900 dark:text-white truncate group-hover:text-anime-600 dark:group-hover:text-anime-400">{{ $anime->title }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $anime->studio?->name }}</p>
                        <span class="inline-block mt-2 text-xs bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 px-2 py-0.5 rounded-full">● {{ __('Airing') }}</span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Genres --}}
@if($genres->count())
<section class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">{{ __('Browse by Genre') }}</h2>
        <div class="flex flex-wrap gap-3">
            @foreach($genres as $genre)
                <a href="{{ route('animes', ['genre'=>$genre->id]) }}"
                   class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-full text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-anime-50 hover:border-anime-300 dark:hover:bg-anime-900/20 dark:hover:border-anime-700 transition-colors">
                    {{ $genre->name }}
                    <span class="ml-1 text-xs text-gray-400">({{ $genre->animes_count }})</span>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Recommendations (auth only) --}}
@auth
    @if(isset($recommendations) && $recommendations->count())
    <section class="py-12 bg-gray-50 dark:bg-gray-800/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">{{ __('Recommended for You') }}</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                @foreach($recommendations as $anime)
                    <a href="{{ route('anime', $anime->id) }}" class="group">
                        <div class="relative aspect-[3/4] rounded-xl overflow-hidden bg-gray-200 dark:bg-gray-700 mb-2">
                            @if($anime->photo)
                                <img src="{{ asset('storage/'.$anime->photo->file) }}" alt="{{ $anime->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            @endif
                            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 to-transparent px-2 pt-6 pb-2 flex items-end justify-end gap-1">
                                @if($anime->seasons > 1)
                                    <span class="text-white text-xs font-semibold bg-white/20 backdrop-blur-sm px-1.5 py-0.5 rounded">{{ $anime->seasons }}S</span>
                                @endif
                                @if($anime->episodes)
                                    <span class="text-white text-xs font-semibold bg-white/20 backdrop-blur-sm px-1.5 py-0.5 rounded">{{ $anime->episodes }} {{ __('eps') }}</span>
                                @endif
                            </div>
                        </div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $anime->title }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $anime->type }}</p>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif
@endauth

{{-- Studios --}}
@if($studios->count())
<section class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Top Studios') }}</h2>
            <a href="{{ route('studios.public') }}" class="text-anime-600 dark:text-anime-400 text-sm font-medium hover:underline">{{ __('View all') }} →</a>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
            @foreach($studios->take(6) as $studio)
                <a href="{{ route('studio.public', $studio->id) }}" class="group text-center">
                    <div class="w-20 h-20 mx-auto rounded-2xl overflow-hidden bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 group-hover:border-anime-400 transition-colors mb-2 flex items-center justify-center">
                        @if($studio->photo)
                            <img src="{{ asset('storage/'.$studio->photo->file) }}" alt="{{ $studio->name }}" class="w-full h-full object-cover">
                        @else
                            <span class="text-2xl font-bold text-gray-400">{{ strtoupper(substr($studio->name, 0, 1)) }}</span>
                        @endif
                    </div>
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300 truncate">{{ $studio->name }}</p>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

@endsection
