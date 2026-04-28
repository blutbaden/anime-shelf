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
            <div class="flex flex-wrap gap-4 mt-2 text-sm">
                <span class="text-gray-700 dark:text-gray-300">
                    <strong>{{ $animeCompleted }}</strong> {{ __('completed') }}
                </span>
                <span class="text-gray-700 dark:text-gray-300">
                    <strong>{{ $shelfCounts['watching'] ?? 0 }}</strong> {{ __('watching') }}
                </span>
                <span class="text-gray-700 dark:text-gray-300">
                    <strong>{{ $reviewCount }}</strong> {{ __('reviews') }}
                </span>
            </div>
            @if($favoriteGenre)
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ __('Favourite genre') }}: <span class="text-anime-600 dark:text-anime-400 font-medium">{{ $favoriteGenre }}</span></p>
            @endif
        </div>
    </div>

    {{-- Currently watching --}}
    @if($currentlyWatching->count())
    <div class="mb-8">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">▶ {{ __('Currently Watching') }}</h2>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            @foreach($currentlyWatching as $anime)
                <a href="{{ route('anime', $anime->id) }}" class="group">
                    <div class="aspect-[3/4] rounded-xl overflow-hidden bg-gray-200 dark:bg-gray-700">
                        @if($anime->photo)
                            <img src="{{ asset('storage/'.$anime->photo->file) }}" alt="{{ $anime->title }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy">
                        @endif
                    </div>
                    <p class="text-xs font-medium text-gray-700 dark:text-gray-300 truncate mt-1">{{ $anime->title }}</p>
                </a>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Recent reviews --}}
    @if($reviews->count())
    <div>
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">⭐ {{ __('Recent Reviews') }}</h2>
        <div class="space-y-4">
            @foreach($reviews as $review)
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3">
                            @if($review->anime?->photo)
                                <a href="{{ route('anime', $review->anime->id) }}">
                                    <img src="{{ asset('storage/'.$review->anime->photo->file) }}" alt=""
                                         class="w-10 h-12 object-cover rounded-lg">
                                </a>
                            @endif
                            <div>
                                <a href="{{ route('anime', $review->anime->id) }}"
                                   class="font-semibold text-sm text-gray-900 dark:text-white hover:text-anime-600 dark:hover:text-anime-400">
                                    {{ $review->anime?->title }}
                                </a>
                                <p class="text-xs text-gray-400 mt-0.5">{{ $review->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        <span class="text-sm font-bold text-yellow-500 shrink-0">{{ $review->rate }}/10</span>
                    </div>
                    <p class="text-sm text-gray-700 dark:text-gray-300 mt-3 line-clamp-3">{{ $review->body }}</p>
                </div>
            @endforeach
        </div>
        <div class="mt-4">{{ $reviews->links() }}</div>
    </div>
    @else
        <div class="text-center py-12">
            <p class="text-gray-500 dark:text-gray-400">{{ __('No reviews yet.') }}</p>
        </div>
    @endif
</div>
@endsection
