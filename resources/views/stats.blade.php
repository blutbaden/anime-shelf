@extends('layouts.app')

@section('title', __('My Stats'))

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">📊 {{ __('My Anime Stats') }}</h1>

    {{-- Overview cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
        @php
            $cards = [
                ['label' => __('Completed'),     'value' => $totalCompleted,                          'icon' => '✅'],
                ['label' => __('Watching'),       'value' => $shelfCounts['watching'] ?? 0,            'icon' => '▶️'],
                ['label' => __('Plan to Watch'),  'value' => $shelfCounts['plan_to_watch'] ?? 0,       'icon' => '📋'],
                ['label' => __('Episodes Seen'),  'value' => number_format($totalEpisodes),             'icon' => '🎬'],
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
            <h2 class="font-bold text-gray-900 dark:text-white mb-4">🎯 {{ __('Yearly Goal') }} ({{ $currentYear }})</h2>

            @if($goalTarget > 0)
                <div class="mb-4">
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-600 dark:text-gray-400">{{ $thisYear }} / {{ $goalTarget }} {{ __('anime') }}</span>
                        <span class="font-bold text-anime-600">{{ $goalPct }}%</span>
                    </div>
                    <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                        <div class="h-full bg-anime-500 rounded-full transition-all duration-500" style="width:{{ $goalPct }}%"></div>
                    </div>
                    @if($goalPct >= 100)
                        <p class="text-green-600 dark:text-green-400 font-semibold text-sm mt-2">🎉 {{ __('Goal reached!') }}</p>
                    @endif
                </div>
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">{{ __('No goal set yet for this year.') }}</p>
            @endif

            <form action="{{ route('goal.set') }}" method="POST" class="flex gap-2">
                @csrf
                <input type="number" name="goal" min="1" max="999"
                       placeholder="{{ __('Set goal...') }}"
                       value="{{ $goalTarget > 0 ? $goalTarget : '' }}"
                       class="flex-1 px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-anime-500">
                <button type="submit" class="px-4 py-2 bg-anime-600 hover:bg-anime-700 text-white font-semibold rounded-lg text-sm transition-colors">{{ __('Set') }}</button>
            </form>
        </div>

        {{-- Favorite genres --}}
        @if(!empty($genreCounts))
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="font-bold text-gray-900 dark:text-white mb-4">🎭 {{ __('Top Genres') }}</h2>
            <div class="space-y-3">
                @php $maxGenre = max($genreCounts) ?: 1; @endphp
                @foreach(array_slice($genreCounts, 0, 6, true) as $name => $count)
                    @php $pct = round($count / $maxGenre * 100); @endphp
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-700 dark:text-gray-300">{{ $name }}</span>
                            <span class="text-gray-500 dark:text-gray-400">{{ $count }}</span>
                        </div>
                        <div class="h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                            <div class="h-full bg-anime-400 rounded-full" style="width:{{ $pct }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Monthly completed --}}
        @if($monthlyCompleted->count())
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 lg:col-span-2">
            <h2 class="font-bold text-gray-900 dark:text-white mb-4">📅 {{ __('Monthly Activity') }}</h2>
            <div class="flex items-end gap-2 h-32">
                @php $maxVal = $monthlyCompleted->max('total') ?: 1; @endphp
                @foreach($monthlyCompleted as $row)
                    @php $pct = round($row->total / $maxVal * 100); @endphp
                    <div class="flex-1 flex flex-col items-center gap-1">
                        <span class="text-xs font-semibold text-anime-600 dark:text-anime-400">{{ $row->total ?: '' }}</span>
                        <div class="w-full rounded-t-md bg-anime-400 dark:bg-anime-600 transition-all" style="height:{{ max(4, $pct) }}%"></div>
                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $row->month }}</span>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Extra stats --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="font-bold text-gray-900 dark:text-white mb-4">📝 {{ __('More Stats') }}</h2>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500 dark:text-gray-400">{{ __('Reviews written') }}</span>
                    <span class="font-semibold text-gray-900 dark:text-white">{{ $reviewCount }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 dark:text-gray-400">{{ __('On Hold') }}</span>
                    <span class="font-semibold text-gray-900 dark:text-white">{{ $shelfCounts['on_hold'] ?? 0 }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 dark:text-gray-400">{{ __('Dropped') }}</span>
                    <span class="font-semibold text-gray-900 dark:text-white">{{ $shelfCounts['dropped'] ?? 0 }}</span>
                </div>
                @if($favoriteGenre)
                <div class="flex justify-between">
                    <span class="text-gray-500 dark:text-gray-400">{{ __('Favourite genre') }}</span>
                    <span class="font-semibold text-anime-600 dark:text-anime-400">{{ $favoriteGenre }}</span>
                </div>
                @endif
                <div class="flex justify-between">
                    <span class="text-gray-500 dark:text-gray-400">{{ __('Completed this year') }}</span>
                    <span class="font-semibold text-gray-900 dark:text-white">{{ $thisYear }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
