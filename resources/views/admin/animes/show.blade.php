@extends('layouts.admin')

@section('page-title', $anime->title)

@section('content')
<div class="max-w-4xl">
    <div class="flex items-center justify-between mb-5">
        <a href="{{ route('animes.index') }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">← {{ __('Back') }}</a>
        <a href="{{ route('animes.edit', $anime->id) }}" class="px-4 py-2 bg-anime-600 hover:bg-anime-700 text-white rounded-lg text-sm font-semibold">{{ __('Edit') }}</a>
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
</div>
@endsection
