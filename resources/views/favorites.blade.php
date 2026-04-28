@extends('layouts.app')

@section('title', __('My Favorites'))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">❤ {{ __('My Favorites') }}</h1>

    @if($animes->count())
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
            @foreach($animes as $anime)
                <a href="{{ route('anime', $anime->id) }}" class="group">
                    <div class="aspect-[3/4] rounded-xl overflow-hidden bg-gray-200 dark:bg-gray-700 mb-2">
                        @if($anime->photo)
                            <img src="{{ asset('storage/'.$anime->photo->file) }}" alt="{{ $anime->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                            </div>
                        @endif
                    </div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate group-hover:text-anime-600 dark:group-hover:text-anime-400">{{ $anime->title }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $anime->type }} · {{ $anime->season_year }}</p>
                </a>
            @endforeach
        </div>

        <div class="mt-8">{{ $animes->links() }}</div>
    @else
        <div class="text-center py-20">
            <svg class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
            <p class="text-gray-500 dark:text-gray-400 font-medium">{{ __('No favorites yet.') }}</p>
            <a href="{{ route('animes') }}" class="mt-3 inline-block text-anime-600 dark:text-anime-400 text-sm hover:underline">{{ __('Explore anime') }} →</a>
        </div>
    @endif
</div>
@endsection
