@extends('layouts.app')

@section('title', __('Genres'))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">{{ __('Anime Genres') }}</h1>

    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
        @foreach($genres as $genre)
            <a href="{{ route('animes', ['genre' => $genre->id]) }}"
               class="group relative overflow-hidden rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-5 hover:border-anime-400 hover:shadow-md transition-all text-center">
                @if($genre->photo)
                    <img src="{{ asset('storage/'.$genre->photo->file) }}" alt="{{ $genre->name }}" class="absolute inset-0 w-full h-full object-cover opacity-10 group-hover:opacity-20 transition-opacity">
                @endif
                <div class="relative">
                    <p class="font-bold text-gray-900 dark:text-white group-hover:text-anime-600 dark:group-hover:text-anime-400 transition-colors">{{ $genre->name }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $genre->animes_count ?? 0 }} {{ __('anime') }}</p>
                </div>
            </a>
        @endforeach
    </div>
</div>
@endsection
