@extends('layouts.app')

@section('title', __('Studios'))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">{{ __('Anime Studios') }}</h1>
    <p class="text-gray-500 dark:text-gray-400 mb-8">{{ $studios->total() }} {{ __('studios') }}</p>

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
