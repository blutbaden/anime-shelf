@extends('layouts.app')

@section('title', __('My Watch List'))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ activeTab: '{{ request('status', 'all') }}' }">

    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">{{ __('My Watch List') }}</h1>

    {{-- Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mb-6">
        @php
            $statusColors = [
                'all' => 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300',
                'plan_to_watch' => 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300',
                'watching' => 'bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300',
                'completed' => 'bg-anime-50 dark:bg-anime-900/20 text-anime-700 dark:text-anime-300',
                'on_hold' => 'bg-yellow-50 dark:bg-yellow-900/20 text-yellow-700 dark:text-yellow-300',
                'dropped' => 'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300',
            ];
            $statusLabels = [
                'all' => __('All'),
                'plan_to_watch' => __('Plan to Watch'),
                'watching' => __('Watching'),
                'completed' => __('Completed'),
                'on_hold' => __('On Hold'),
                'dropped' => __('Dropped'),
            ];
        @endphp
        @foreach($counts as $status => $count)
            <a href="{{ route('watch-list', ['status' => $status === 'all' ? null : $status]) }}"
               class="rounded-xl px-3 py-3 text-center transition-all {{ request('status', 'all') === $status ? 'ring-2 ring-anime-500 shadow-sm' : '' }} {{ $statusColors[$status] ?? '' }}">
                <div class="text-2xl font-extrabold">{{ $count }}</div>
                <div class="text-xs font-medium mt-0.5">{{ $statusLabels[$status] ?? $status }}</div>
            </a>
        @endforeach
    </div>

    @if($animes->count())
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
            @foreach($animes as $anime)
                <div class="group relative">
                    <a href="{{ route('anime', $anime->id) }}">
                        <div class="aspect-[3/4] rounded-xl overflow-hidden bg-gray-200 dark:bg-gray-700 mb-2">
                            @if($anime->photo)
                                <img src="{{ asset('storage/'.$anime->photo->file) }}" alt="{{ $anime->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy">
                            @endif
                            @php $pivot = $anime->pivot; @endphp
                            @if($pivot?->current_episode && $anime->episodes)
                                <div class="absolute bottom-0 left-0 right-0 h-1 bg-gray-300/50">
                                    <div class="h-full bg-anime-500" style="width: {{ min(100, round($pivot->current_episode/$anime->episodes*100)) }}%"></div>
                                </div>
                            @endif
                        </div>
                    </a>
                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $anime->title }}</p>
                    @if($pivot?->current_episode !== null && $anime->episodes)
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Ep') }} {{ $pivot->current_episode }}/{{ $anime->episodes }}</p>
                    @endif
                    @if($pivot?->score)
                        <p class="text-xs text-yellow-500">★ {{ $pivot->score }}/10</p>
                    @endif

                    <form action="{{ route('watch-list.store') }}" method="POST" class="mt-1.5">
                        @csrf
                        <input type="hidden" name="anime_id" value="{{ $anime->id }}">
                        <select name="status" onchange="this.form.submit()" class="w-full text-xs px-2 py-1 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-1 focus:ring-anime-500">
                            <option value="plan_to_watch" {{ $pivot?->status === 'plan_to_watch' ? 'selected' : '' }}>{{ __('Plan to Watch') }}</option>
                            <option value="watching" {{ $pivot?->status === 'watching' ? 'selected' : '' }}>{{ __('Watching') }}</option>
                            <option value="completed" {{ $pivot?->status === 'completed' ? 'selected' : '' }}>{{ __('Completed') }}</option>
                            <option value="on_hold" {{ $pivot?->status === 'on_hold' ? 'selected' : '' }}>{{ __('On Hold') }}</option>
                            <option value="dropped" {{ $pivot?->status === 'dropped' ? 'selected' : '' }}>{{ __('Dropped') }}</option>
                        </select>
                    </form>
                </div>
            @endforeach
        </div>

        <div class="mt-8">{{ $animes->withQueryString()->links() }}</div>
    @else
        <div class="text-center py-20">
            <svg class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            <p class="text-gray-500 dark:text-gray-400 font-medium">{{ __('Your watch list is empty.') }}</p>
            <a href="{{ route('animes') }}" class="mt-3 inline-block text-anime-600 dark:text-anime-400 text-sm hover:underline">{{ __('Browse anime') }} →</a>
        </div>
    @endif
</div>
@endsection
