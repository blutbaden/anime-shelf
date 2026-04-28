@extends('layouts.app')

@section('title', __('My Stats'))

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">📊 {{ __('My Anime Stats') }}</h1>

    {{-- Overview cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
        @php
            $cards = [
                ['label' => __('Total Watched'), 'value' => $totalCompleted ?? 0, 'color' => 'anime', 'icon' => '✅'],
                ['label' => __('Watching'), 'value' => $totalWatching ?? 0, 'color' => 'green', 'icon' => '▶️'],
                ['label' => __('Plan to Watch'), 'value' => $totalPlanned ?? 0, 'color' => 'blue', 'icon' => '📋'],
                ['label' => __('Episodes Seen'), 'value' => number_format($totalEpisodes ?? 0), 'color' => 'yellow', 'icon' => '🎬'],
            ];
        @endphp
        @foreach($cards as $card)
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-5 text-center">
                <div class="text-3xl mb-2">{{ $card['icon'] }}</div>
                <div class="text-3xl font-extrabold text-gray-900 dark:text-white">{{ $card['value'] }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 font-medium">{{ $card['label'] }}</div>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Watch Goal --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="font-bold text-gray-900 dark:text-white mb-4">🎯 {{ __('Yearly Goal') }} ({{ now()->year }})</h2>

            @if($goal)
                @php $pct = min(100, round(($totalCompleted/$goal->goal)*100)); @endphp
                <div class="mb-4">
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-600 dark:text-gray-400">{{ $totalCompleted }} / {{ $goal->goal }} {{ __('anime') }}</span>
                        <span class="font-bold text-anime-600">{{ $pct }}%</span>
                    </div>
                    <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                        <div class="h-full bg-anime-500 rounded-full transition-all duration-500" style="width:{{ $pct }}%"></div>
                    </div>
                    @if($pct >= 100)
                        <p class="text-green-600 dark:text-green-400 font-semibold text-sm mt-2">🎉 {{ __('Goal reached!') }}</p>
                    @endif
                </div>
            @endif

            <form action="{{ route('goal.set') }}" method="POST" class="flex gap-2">
                @csrf
                <input type="number" name="goal" min="1" max="999" placeholder="{{ __('Set goal...') }}" value="{{ $goal?->goal }}"
                       class="flex-1 px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-anime-500">
                <button type="submit" class="px-4 py-2 bg-anime-600 hover:bg-anime-700 text-white font-semibold rounded-lg text-sm transition-colors">{{ __('Set') }}</button>
            </form>
        </div>

        {{-- Favorite genres --}}
        @if(isset($topGenres) && $topGenres->count())
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="font-bold text-gray-900 dark:text-white mb-4">🎭 {{ __('Top Genres') }}</h2>
            <div class="space-y-3">
                @foreach($topGenres->take(6) as $genre)
                    @php $pct = $topGenres->max('count') > 0 ? round($genre->count / $topGenres->max('count') * 100) : 0; @endphp
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-700 dark:text-gray-300">{{ $genre->name }}</span>
                            <span class="text-gray-500 dark:text-gray-400">{{ $genre->count }}</span>
                        </div>
                        <div class="h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                            <div class="h-full bg-anime-400 rounded-full" style="width:{{ $pct }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Monthly activity --}}
        @if(isset($monthlyActivity) && count($monthlyActivity))
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 lg:col-span-2">
            <h2 class="font-bold text-gray-900 dark:text-white mb-4">📅 {{ __('Monthly Activity') }}</h2>
            <div class="flex items-end gap-2 h-32">
                @php $maxVal = max(array_column($monthlyActivity, 'count')) ?: 1; @endphp
                @foreach($monthlyActivity as $month)
                    @php $pct = round($month['count'] / $maxVal * 100); @endphp
                    <div class="flex-1 flex flex-col items-center gap-1">
                        <span class="text-xs font-semibold text-anime-600 dark:text-anime-400">{{ $month['count'] ?: '' }}</span>
                        <div class="w-full rounded-t-md bg-anime-400 dark:bg-anime-600 transition-all" style="height:{{ max(4, $pct) }}%"></div>
                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $month['month'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
