@extends('layouts.app')
@section('title', 'Ep. ' . $episode->number . ($episode->title ? ' — ' . $episode->title : '') . ' · ' . $anime->title)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

    {{-- Breadcrumb --}}
    <nav class="text-xs text-gray-500 dark:text-gray-400 mb-4 flex items-center gap-1">
        <a href="{{ route('animes') }}" class="hover:underline">{{ __('Anime') }}</a>
        <span>/</span>
        <a href="{{ route('anime', $anime) }}" class="hover:underline">{{ $anime->title }}</a>
        <span>/</span>
        <span class="text-gray-700 dark:text-gray-300">{{ __('Episode :n', ['n' => $episode->number]) }}</span>
    </nav>

    <div class="grid grid-cols-1 xl:grid-cols-4 gap-6">

        {{-- ── Player ──────────────────────────────────────────────────── --}}
        <div class="xl:col-span-3 space-y-4">

            {{-- Video --}}
            <div class="bg-black rounded-xl overflow-hidden">
                @if($episode->is_direct_video)
                    <video controls autoplay class="w-full max-h-[70vh]" preload="metadata">
                        <source src="{{ $episode->url }}" type="video/mp4">
                        {{ __('Your browser does not support HTML5 video.') }}
                    </video>
                @else
                    <div class="relative w-full" style="padding-bottom:56.25%">
                        <iframe
                            src="{{ $episode->embed_url }}"
                            title="{{ $episode->title ?? 'Episode ' . $episode->number }}"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; fullscreen"
                            allowfullscreen
                            class="absolute inset-0 w-full h-full border-0">
                        </iframe>
                    </div>
                @endif
            </div>

            {{-- Title & nav --}}
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-lg font-bold text-gray-900 dark:text-white">
                        {{ __('Episode :n', ['n' => $episode->number]) }}
                        @if($episode->title) — {{ $episode->title }} @endif
                    </h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                        <a href="{{ route('anime', $anime) }}" class="hover:underline">{{ $anime->title }}</a>
                        @if($episode->duration) · {{ $episode->duration }} {{ __('min') }} @endif
                    </p>
                </div>

                <div class="flex gap-2 shrink-0">
                    @if($prevEpisode)
                        <a href="{{ route('episode.watch', [$anime, $prevEpisode]) }}"
                           class="px-3 py-1.5 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg text-sm hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-1">
                            ← {{ __('Ep. :n', ['n' => $prevEpisode->number]) }}
                        </a>
                    @endif
                    @if($nextEpisode)
                        <a href="{{ route('episode.watch', [$anime, $nextEpisode]) }}"
                           class="px-3 py-1.5 bg-anime-600 hover:bg-anime-700 text-white rounded-lg text-sm flex items-center gap-1">
                            {{ __('Ep. :n', ['n' => $nextEpisode->number]) }} →
                        </a>
                    @endif
                </div>
            </div>

            {{-- Description --}}
            @if($episode->description)
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                    {{ $episode->description }}
                </div>
            @endif
        </div>

        {{-- ── Episode List sidebar ────────────────────────────────────── --}}
        <div class="xl:col-span-1">
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <h2 class="font-semibold text-gray-900 dark:text-white text-sm">{{ __('Episodes') }}</h2>
                    <span class="text-xs text-gray-400">{{ $allEpisodes->count() }}</span>
                </div>
                <ul class="divide-y divide-gray-100 dark:divide-gray-700 max-h-[60vh] overflow-y-auto">
                    @foreach($allEpisodes as $ep)
                        <li>
                            <a href="{{ route('episode.watch', [$anime, $ep]) }}"
                               class="flex items-center gap-3 px-4 py-2.5 text-sm hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors
                                      {{ $ep->id === $episode->id ? 'bg-anime-50 dark:bg-anime-900/20 text-anime-700 dark:text-anime-300 font-semibold' : 'text-gray-700 dark:text-gray-300' }}">
                                <span class="w-8 text-center shrink-0 font-mono text-xs
                                             {{ $ep->id === $episode->id ? 'text-anime-600 dark:text-anime-400' : 'text-gray-400' }}">
                                    {{ $ep->number }}
                                </span>
                                <span class="truncate">{{ $ep->title ?? __('Episode :n', ['n' => $ep->number]) }}</span>
                                @if($ep->id === $episode->id)
                                    <span class="ml-auto shrink-0 w-1.5 h-1.5 rounded-full bg-anime-500"></span>
                                @endif
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

    </div>
</div>
@endsection
