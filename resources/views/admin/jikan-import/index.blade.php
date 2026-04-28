@extends('layouts.admin')
@section('page-title', __('Jikan Import'))
@section('content')

<div class="max-w-4xl space-y-6">

    {{-- Search import --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
        <h2 class="font-bold text-gray-900 dark:text-white mb-4">🔍 {{ __('Search & Import from MyAnimeList') }}</h2>

        <form action="{{ route('jikan.search') }}" method="POST" class="flex gap-3 mb-5">
            @csrf
            <input type="text" name="query" value="{{ old('query', request('query')) }}" required
                   placeholder="{{ __('Search anime title...') }}"
                   class="flex-1 px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-anime-500">
            <button type="submit" class="px-5 py-2.5 bg-anime-600 hover:bg-anime-700 text-white font-semibold rounded-lg text-sm">{{ __('Search') }}</button>
        </form>

        @if(isset($results) && count($results))
            <div class="space-y-3">
                @foreach($results as $result)
                    <div class="flex items-center gap-4 p-3 rounded-xl border border-gray-100 dark:border-gray-700 hover:border-anime-300 dark:hover:border-anime-700 transition-colors">
                        @if($result['images']['jpg']['image_url'] ?? null)
                            <img src="{{ $result['images']['jpg']['image_url'] }}" alt="" class="w-12 h-16 object-cover rounded-lg shrink-0">
                        @endif
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-gray-900 dark:text-white truncate">{{ $result['title'] }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $result['type'] }} · {{ $result['episodes'] ?? '?' }} eps · MAL ID: {{ $result['mal_id'] }}</p>
                            @if($result['synopsis'] ?? null)
                                <p class="text-xs text-gray-600 dark:text-gray-300 mt-1 line-clamp-2">{{ $result['synopsis'] }}</p>
                            @endif
                        </div>
                        <form action="{{ route('jikan.import') }}" method="POST" class="shrink-0">
                            @csrf
                            <input type="hidden" name="mal_id" value="{{ $result['mal_id'] }}">
                            <button type="submit" class="px-3 py-1.5 bg-anime-600 hover:bg-anime-700 text-white text-xs font-semibold rounded-lg whitespace-nowrap">
                                {{ __('Import') }}
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        @elseif(isset($results))
            <p class="text-gray-500 dark:text-gray-400 text-sm">{{ __('No results found.') }}</p>
        @endif
    </div>

    {{-- Import top --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
        <h2 class="font-bold text-gray-900 dark:text-white mb-2">📈 {{ __('Import Top Anime') }}</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">{{ __('Bulk import top anime from MyAnimeList by popularity or rating.') }}</p>

        <form action="{{ route('jikan.import-top') }}" method="POST" class="flex flex-wrap gap-3 items-end">
            @csrf
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('Count') }}</label>
                <input type="number" name="count" value="25" min="1" max="200" class="w-20 px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-anime-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('Filter') }}</label>
                <select name="filter" class="px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-anime-500">
                    <option value="bypopularity">{{ __('By Popularity') }}</option>
                    <option value="byrating">{{ __('By Rating') }}</option>
                    <option value="">{{ __('All') }}</option>
                </select>
            </div>
            <button type="submit" onclick="return confirm('{{ __('This may take a while. Continue?') }}')"
                    class="px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg text-sm">
                {{ __('Import Top Anime') }}
            </button>
        </form>

        @if(session('import_result'))
            <div class="mt-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-xl p-4 text-sm text-green-700 dark:text-green-400">
                {{ session('import_result') }}
            </div>
        @endif
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-xl p-4 text-sm text-green-700 dark:text-green-400">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-xl p-4 text-sm text-red-700 dark:text-red-400">
            {{ session('error') }}
        </div>
    @endif
</div>
@endsection
