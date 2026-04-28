@extends('layouts.app')

@section('title', __('Discover Anime'))

@section('content')

{{-- Hero --}}
<section class="relative bg-gradient-to-br from-gray-900 via-anime-950 to-gray-900 text-white overflow-hidden">
    <div class="absolute inset-0 opacity-20" style="background-image:url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%239C92AC\' fill-opacity=\'0.4\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-28">
        <div class="max-w-2xl">
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold leading-tight mb-4">
                {{ __('Your Anime') }}<br>
                <span class="text-anime-400">{{ __('Universe') }}</span>
            </h1>
            <p class="text-lg text-gray-300 mb-8">{{ __('Track, discover, and share your anime journey. Build your personal watch list and explore thousands of titles.') }}</p>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('animes') }}" class="px-6 py-3 bg-anime-600 hover:bg-anime-700 text-white font-semibold rounded-xl transition-colors">
                    {{ __('Browse Anime') }}
                </a>
                @guest
                    <a href="{{ route('register') }}" class="px-6 py-3 bg-white/10 hover:bg-white/20 text-white font-semibold rounded-xl border border-white/20 transition-colors">
                        {{ __('Get Started Free') }}
                    </a>
                @endguest
            </div>
        </div>
    </div>
</section>

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
@if($trending->count())
<section class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">🔥 {{ __('Trending Now') }}</h2>
            <a href="{{ route('animes') }}" class="text-anime-600 dark:text-anime-400 text-sm font-medium hover:underline">{{ __('View all') }} →</a>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
            @foreach($trending as $anime)
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
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
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
@if($airing->count())
<section class="py-12 bg-gray-50 dark:bg-gray-800/50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">📡 {{ __('Currently Airing') }}</h2>
            <a href="{{ route('animes', ['status'=>'airing']) }}" class="text-anime-600 dark:text-anime-400 text-sm font-medium hover:underline">{{ __('View all') }} →</a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach($airing->take(8) as $anime)
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
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">🎭 {{ __('Browse by Genre') }}</h2>
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
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">✨ {{ __('Recommended for You') }}</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                @foreach($recommendations as $anime)
                    <a href="{{ route('anime', $anime->id) }}" class="group">
                        <div class="relative aspect-[3/4] rounded-xl overflow-hidden bg-gray-200 dark:bg-gray-700 mb-2">
                            @if($anime->photo)
                                <img src="{{ asset('storage/'.$anime->photo->file) }}" alt="{{ $anime->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            @endif
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
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">🏢 {{ __('Top Studios') }}</h2>
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
