@extends('layouts.admin')

@section('page-title', $anime->title)

@section('content')
<div class="max-w-4xl">
    <div class="flex items-center justify-between mb-5">
        <a href="{{ route('animes.index') }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">← {{ __('Back') }}</a>
        <div class="flex gap-2">
            <a href="{{ route('episodes.create', $anime->id) }}" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-semibold">+ {{ __('Add Episode') }}</a>
            <a href="{{ route('animes.edit', $anime->id) }}" class="px-4 py-2 bg-anime-600 hover:bg-anime-700 text-white rounded-lg text-sm font-semibold">{{ __('Edit') }}</a>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex gap-6">
            @if($anime->photo)
                <img src="{{ asset('storage/'.$anime->photo->file) }}" alt="{{ $anime->title }}" class="w-32 h-44 object-cover rounded-xl shrink-0">
            @endif
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $anime->title }}</h1>
                @if($anime->title_japanese)<p class="text-gray-500 dark:text-gray-400 text-sm">{{ $anime->title_japanese }}</p>@endif
                <div class="flex flex-wrap gap-2 mt-3">
                    <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded text-xs">{{ $anime->type }}</span>
                    <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded text-xs capitalize">{{ $anime->status }}</span>
                    @if($anime->season)<span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded text-xs">{{ $anime->season }} {{ $anime->season_year }}</span>@endif
                    @if($anime->episodes)<span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded text-xs">{{ $anime->episodes }} eps</span>@endif
                </div>
                <p class="mt-4 text-sm text-gray-700 dark:text-gray-300">{{ $anime->synopsis }}</p>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-2 sm:grid-cols-4 gap-4 text-center">
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4">
                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $anime->reviews->count() }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400">{{ __('Reviews') }}</div>
            </div>
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4">
                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($anime->views) }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400">{{ __('Views') }}</div>
            </div>
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4">
                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $anime->favoritedByUsers->count() }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400">{{ __('Favorites') }}</div>
            </div>
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4">
                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $anime->average_rating ? number_format($anime->average_rating, 1) : '—' }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400">{{ __('Avg Rating') }}</div>
            </div>
        </div>
    </div>

    {{-- Episodes --}}
    <div class="mt-6 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-700">
            <h2 class="font-semibold text-gray-900 dark:text-white">
                {{ __('Episodes') }}
                <span class="ml-2 text-sm font-normal text-gray-400">({{ $episodesBySeries->flatten()->count() }} {{ __('total') }})</span>
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('episodes.index', $anime->id) }}" class="text-xs text-gray-500 dark:text-gray-400 hover:underline">{{ __('Manage all') }} →</a>
                <a href="{{ route('episodes.create', $anime->id) }}" class="px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white rounded-lg text-xs font-semibold">+ {{ __('Add Episode') }}</a>
            </div>
        </div>

        @if($episodesBySeries->isEmpty())
            <div class="px-5 py-10 text-center text-gray-400 dark:text-gray-500 text-sm">
                {{ __('No episodes yet.') }}
                <a href="{{ route('episodes.create', $anime->id) }}" class="ml-1 text-green-600 hover:underline">{{ __('Add the first one') }}</a>
            </div>
        @else
            @foreach($episodesBySeries as $seriesNum => $episodes)
                <div class="px-5 py-3 border-b border-gray-50 dark:border-gray-700/50 last:border-0">
                    <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-3">
                        {{ __('Series') }} {{ $seriesNum }} · {{ $episodes->count() }} {{ __('episodes') }}
                        <a href="{{ route('episodes.create', ['anime' => $anime->id, 'series' => $seriesNum]) }}" class="ml-3 text-green-500 hover:text-green-700 normal-case font-medium">+ {{ __('Add to this series') }}</a>
                    </p>
                    <div class="grid grid-cols-4 sm:grid-cols-6 md:grid-cols-8 lg:grid-cols-10 gap-2">
                        @foreach($episodes as $ep)
                            <a href="{{ route('episodes.edit', [$anime->id, $ep->id]) }}"
                               class="flex flex-col items-center justify-center p-2 rounded-lg border text-center transition-colors
                                      {{ $ep->is_active ? 'border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-900/20 hover:bg-green-100' : 'border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30 hover:bg-gray-100 dark:hover:bg-gray-700 opacity-60' }}"
                               title="{{ $ep->title ?: 'Episode '.$ep->number }}">
                                <span class="text-xs font-bold text-gray-700 dark:text-gray-200">{{ $ep->number }}</span>
                                @if($ep->duration)
                                    <span class="text-[10px] text-gray-400">{{ $ep->duration }}m</span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>
@endsection
