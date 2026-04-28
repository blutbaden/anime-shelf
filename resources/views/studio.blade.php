@extends('layouts.app')

@section('title', $studio->name)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col sm:flex-row items-start gap-6 mb-8">
        <div class="w-24 h-24 rounded-2xl overflow-hidden bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 flex items-center justify-center shrink-0">
            @if($studio->photo)
                <img src="{{ asset('storage/'.$studio->photo->file) }}" alt="{{ $studio->name }}" class="w-full h-full object-cover">
            @else
                <span class="text-3xl font-extrabold text-anime-500">{{ strtoupper(substr($studio->name, 0, 1)) }}</span>
            @endif
        </div>
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $studio->name }}</h1>
            @if($studio->headquarters)<p class="text-gray-500 dark:text-gray-400 text-sm mt-1">📍 {{ $studio->headquarters }}</p>@endif
            @if($studio->founded_year)<p class="text-gray-500 dark:text-gray-400 text-sm">📅 {{ __('Founded') }} {{ $studio->founded_year }}</p>@endif
            @if($studio->website)
                <a href="{{ $studio->website }}" target="_blank" rel="noopener noreferrer" class="text-anime-600 dark:text-anime-400 text-sm hover:underline">🔗 {{ $studio->website }}</a>
            @endif
            @if($studio->description)
                <p class="mt-3 text-gray-700 dark:text-gray-300 text-sm max-w-2xl">{{ $studio->description }}</p>
            @endif
        </div>
    </div>

    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-5">{{ __('Anime by') }} {{ $studio->name }} <span class="text-gray-400 font-normal text-base">({{ $animes->total() }})</span></h2>

    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
        @foreach($animes as $anime)
            <a href="{{ route('anime', $anime->id) }}" class="group">
                <div class="aspect-[3/4] rounded-xl overflow-hidden bg-gray-200 dark:bg-gray-700 mb-2">
                    @if($anime->photo)
                        <img src="{{ asset('storage/'.$anime->photo->file) }}" alt="{{ $anime->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy">
                    @endif
                </div>
                <p class="text-sm font-medium text-gray-900 dark:text-white truncate group-hover:text-anime-600 dark:group-hover:text-anime-400">{{ $anime->title }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $anime->type }} · {{ $anime->season_year }}</p>
            </a>
        @endforeach
    </div>

    <div class="mt-8">{{ $animes->links() }}</div>
</div>
@endsection
