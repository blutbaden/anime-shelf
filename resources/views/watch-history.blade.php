@extends('layouts.app')

@section('title', __('Watch History'))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">{{ __('Watch History') }}</h1>

    @if($animes->count())
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
            @foreach($animes as $anime)
                <div class="group relative">
                    <a href="{{ route('anime', $anime->id) }}">
                        <div class="aspect-[3/4] rounded-xl overflow-hidden bg-gray-200 dark:bg-gray-700 mb-2">
                            @if($anime->photo)
                                <img src="{{ asset('storage/'.$anime->photo->file) }}" alt="{{ $anime->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy">
                            @endif
                            <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <form action="{{ route('watch-history.destroy', $anime->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-7 h-7 bg-red-500 text-white rounded-full flex items-center justify-center text-xs hover:bg-red-600" title="{{ __('Remove') }}" onclick="return confirm('{{ __('Remove from history?') }}')">×</button>
                                </form>
                            </div>
                        </div>
                    </a>
                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $anime->title }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ __('Completed') }} {{ $anime->pivot->completed_at ? \Carbon\Carbon::parse($anime->pivot->completed_at)->diffForHumans() : '' }}
                    </p>
                </div>
            @endforeach
        </div>

        <div class="mt-8">{{ $animes->links() }}</div>
    @else
        <div class="text-center py-20">
            <svg class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-gray-500 dark:text-gray-400 font-medium">{{ __('No watch history yet.') }}</p>
            <a href="{{ route('animes') }}" class="mt-3 inline-block text-anime-600 dark:text-anime-400 text-sm hover:underline">{{ __('Start watching') }} →</a>
        </div>
    @endif
</div>
@endsection
