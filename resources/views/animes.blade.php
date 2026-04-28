@extends('layouts.app')

@section('title', __('Anime Catalog'))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ __('Anime Catalog') }}</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1">{{ $animes->total() }} {{ __('titles') }}</p>
    </div>

    <div class="flex flex-col lg:flex-row gap-6">

        {{-- Sidebar filters --}}
        <aside class="lg:w-64 shrink-0" x-data="{ open: false }">
            <button @click="open=!open" class="lg:hidden w-full flex items-center justify-between px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm font-medium mb-3">
                {{ __('Filters') }}
                <svg class="w-4 h-4" :class="open?'rotate-180':''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>

            <div :class="open ? 'block' : 'hidden lg:block'" class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 space-y-5">
                <form method="GET" action="{{ route('animes') }}" id="filter-form">
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-3">{{ __('Filters') }}</h3>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">{{ __('Type') }}</label>
                        @foreach(['TV','Movie','OVA','ONA','Special'] as $t)
                            <label class="flex items-center gap-2 mb-1.5 cursor-pointer">
                                <input type="checkbox" name="type[]" value="{{ $t }}" {{ in_array($t, (array)request('type')) ? 'checked' : '' }} onchange="this.form.submit()" class="w-4 h-4 rounded border-gray-300 text-anime-600 focus:ring-anime-500">
                                <span class="text-sm text-gray-700 dark:text-gray-300">{{ $t }}</span>
                            </label>
                        @endforeach
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">{{ __('Status') }}</label>
                        @foreach(['airing'=>__('Airing'),'finished'=>__('Finished'),'upcoming'=>__('Upcoming')] as $val=>$label)
                            <label class="flex items-center gap-2 mb-1.5 cursor-pointer">
                                <input type="checkbox" name="status[]" value="{{ $val }}" {{ in_array($val, (array)request('status')) ? 'checked' : '' }} onchange="this.form.submit()" class="w-4 h-4 rounded border-gray-300 text-anime-600 focus:ring-anime-500">
                                <span class="text-sm text-gray-700 dark:text-gray-300">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">{{ __('Genre') }}</label>
                        @foreach($genres as $genre)
                            <label class="flex items-center gap-2 mb-1.5 cursor-pointer">
                                <input type="checkbox" name="genre[]" value="{{ $genre->id }}" {{ in_array($genre->id, (array)request('genre')) ? 'checked' : '' }} onchange="this.form.submit()" class="w-4 h-4 rounded border-gray-300 text-anime-600 focus:ring-anime-500">
                                <span class="text-sm text-gray-700 dark:text-gray-300">{{ $genre->name }}</span>
                            </label>
                        @endforeach
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">{{ __('Season') }}</label>
                        @foreach(['Winter','Spring','Summer','Fall'] as $s)
                            <label class="flex items-center gap-2 mb-1.5 cursor-pointer">
                                <input type="checkbox" name="season[]" value="{{ $s }}" {{ in_array($s, (array)request('season')) ? 'checked' : '' }} onchange="this.form.submit()" class="w-4 h-4 rounded border-gray-300 text-anime-600 focus:ring-anime-500">
                                <span class="text-sm text-gray-700 dark:text-gray-300">{{ $s }}</span>
                            </label>
                        @endforeach
                    </div>

                    @if(request()->hasAny(['type','status','genre','season','search','sort']))
                        <a href="{{ route('animes') }}" class="block text-center text-xs text-red-500 hover:text-red-700 mt-3">× {{ __('Clear filters') }}</a>
                    @endif
                </form>
            </div>
        </aside>

        {{-- Main content --}}
        <div class="flex-1 min-w-0">

            {{-- Sort + search bar --}}
            <div class="flex flex-wrap items-center gap-3 mb-5">
                <div class="flex-1 relative min-w-[200px]">
                    <input type="text" name="search" form="filter-form" value="{{ request('search') }}" placeholder="{{ __('Search anime...') }}"
                           class="w-full pl-9 pr-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-anime-500">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <select name="sort" form="filter-form" onchange="document.getElementById('filter-form').submit()"
                        class="px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-anime-500">
                    <option value="newest" {{ request('sort')=='newest'?'selected':'' }}>{{ __('Newest') }}</option>
                    <option value="popular" {{ request('sort')=='popular'?'selected':'' }}>{{ __('Most Popular') }}</option>
                    <option value="rating" {{ request('sort')=='rating'?'selected':'' }}>{{ __('Top Rated') }}</option>
                    <option value="title" {{ request('sort')=='title'?'selected':'' }}>{{ __('A-Z') }}</option>
                </select>
            </div>

            {{-- Grid --}}
            @if($animes->count())
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                    @foreach($animes as $anime)
                        <a href="{{ route('anime', $anime->id) }}" class="group">
                            <div class="relative aspect-[3/4] rounded-xl overflow-hidden bg-gray-200 dark:bg-gray-700 mb-2">
                                @if($anime->photo)
                                    <img src="{{ asset('storage/'.$anime->photo->file) }}" alt="{{ $anime->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.069A1 1 0 0121 8.868V15.131a1 1 0 01-1.447.9L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                    </div>
                                @endif
                                <div class="absolute top-2 left-2 flex flex-col gap-1">
                                    <span class="text-xs bg-black/70 text-white px-1.5 py-0.5 rounded">{{ $anime->type }}</span>
                                    @if($anime->status === 'airing')
                                        <span class="text-xs bg-green-600 text-white px-1.5 py-0.5 rounded">● Live</span>
                                    @endif
                                </div>
                                @if($anime->average_rating)
                                    <div class="absolute bottom-2 left-2 bg-black/70 text-yellow-400 text-xs font-bold px-1.5 py-0.5 rounded flex items-center gap-0.5">
                                        ★ {{ number_format($anime->average_rating, 1) }}
                                    </div>
                                @endif
                            </div>
                            <p class="text-sm font-semibold text-gray-900 dark:text-white truncate group-hover:text-anime-600 dark:group-hover:text-anime-400 transition-colors">{{ $anime->title }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $anime->studio?->name ?? '—' }} · {{ $anime->season_year }}</p>
                        </a>
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $animes->withQueryString()->links() }}
                </div>
            @else
                <div class="text-center py-20">
                    <svg class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-gray-500 dark:text-gray-400 font-medium">{{ __('No anime found.') }}</p>
                    <a href="{{ route('animes') }}" class="mt-3 inline-block text-anime-600 dark:text-anime-400 text-sm hover:underline">{{ __('Clear filters') }}</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
