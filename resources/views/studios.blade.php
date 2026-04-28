@extends('layouts.app')

@section('title', __('Studios'))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ __('Anime Studios') }}</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1">{{ $studios->total() }} {{ __('studios') }}</p>
    </div>

    <form method="GET" action="{{ route('studios.public') }}" class="mb-6">
        <div class="flex gap-3">
            <div class="relative flex-1 max-w-sm">
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="{{ __('Search studios...') }}"
                       class="w-full pl-9 pr-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-anime-500">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            @if($search)
                <a href="{{ route('studios.public') }}" class="px-3 py-2 text-sm text-red-500 hover:text-red-700 flex items-center">× {{ __('Clear') }}</a>
            @endif
        </div>
    </form>

    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-5">
        @foreach($studios as $studio)
            <a href="{{ route('studio.public', $studio->id) }}" class="group text-center">
                <div class="w-24 h-24 mx-auto rounded-2xl overflow-hidden bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 group-hover:border-anime-400 group-hover:shadow-md transition-all mb-3 flex items-center justify-center">
                    @if($studio->photo)
                        <img src="{{ asset('storage/'.$studio->photo->file) }}" alt="{{ $studio->name }}" class="w-full h-full object-cover">
                    @else
                        <span class="text-3xl font-extrabold text-anime-500">{{ strtoupper(substr($studio->name, 0, 1)) }}</span>
                    @endif
                </div>
                <p class="font-semibold text-sm text-gray-900 dark:text-white group-hover:text-anime-600 dark:group-hover:text-anime-400 transition-colors">{{ $studio->name }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $studio->animes_count ?? 0 }} {{ __('titles') }}</p>
            </a>
        @endforeach
    </div>

    <div class="mt-8">{{ $studios->links() }}</div>
</div>
@endsection
