@extends('layouts.app')

@section('title', $user->name)

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Header --}}
    <div class="flex items-center gap-5 mb-8">
        <div class="w-20 h-20 rounded-full overflow-hidden bg-anime-100 dark:bg-anime-900/30 flex items-center justify-center shrink-0">
            @if($user->photo)
                <img src="{{ asset('storage/'.$user->photo->file) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
            @else
                <span class="text-3xl font-bold text-anime-600">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
            @endif
        </div>
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $user->name }}</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Member since') }} {{ $user->created_at->format('F Y') }}</p>
            <div class="flex gap-4 mt-2 text-sm">
                <span class="text-gray-700 dark:text-gray-300"><strong>{{ $user->completedAnime->count() }}</strong> {{ __('completed') }}</span>
                <span class="text-gray-700 dark:text-gray-300"><strong>{{ $user->favoriteAnime->count() }}</strong> {{ __('favorites') }}</span>
                <span class="text-gray-700 dark:text-gray-300"><strong>{{ $user->reviews->count() }}</strong> {{ __('reviews') }}</span>
            </div>
        </div>
    </div>

    {{-- Recently completed --}}
    @if($user->completedAnime->count())
    <div class="mb-8">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">✅ {{ __('Recently Completed') }}</h2>
        <div class="grid grid-cols-3 sm:grid-cols-5 md:grid-cols-6 gap-3">
            @foreach($user->completedAnime->take(12) as $anime)
                <a href="{{ route('anime', $anime->id) }}" class="group">
                    <div class="aspect-[3/4] rounded-xl overflow-hidden bg-gray-200 dark:bg-gray-700">
                        @if($anime->photo)
                            <img src="{{ asset('storage/'.$anime->photo->file) }}" alt="{{ $anime->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy">
                        @endif
                    </div>
                    <p class="text-xs font-medium text-gray-700 dark:text-gray-300 truncate mt-1">{{ $anime->title }}</p>
                </a>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Favorites --}}
    @if($user->favoriteAnime->count())
    <div>
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">❤ {{ __('Favorites') }}</h2>
        <div class="grid grid-cols-3 sm:grid-cols-5 md:grid-cols-6 gap-3">
            @foreach($user->favoriteAnime->take(12) as $anime)
                <a href="{{ route('anime', $anime->id) }}" class="group">
                    <div class="aspect-[3/4] rounded-xl overflow-hidden bg-gray-200 dark:bg-gray-700">
                        @if($anime->photo)
                            <img src="{{ asset('storage/'.$anime->photo->file) }}" alt="{{ $anime->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy">
                        @endif
                    </div>
                    <p class="text-xs font-medium text-gray-700 dark:text-gray-300 truncate mt-1">{{ $anime->title }}</p>
                </a>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
