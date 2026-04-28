@extends('layouts.admin')

@section('page-title', __('Dashboard'))

@section('content')

{{-- Stats --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @php
        $cards = [
            ['label'=>__('Total Anime'),    'value'=>$stats['anime']   ?? 0, 'icon'=>'M15 10l4.553-2.069A1 1 0 0121 8.868V15.131a1 1 0 01-1.447.9L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z', 'color'=>'anime'],
            ['label'=>__('Total Users'),    'value'=>$stats['users']   ?? 0, 'icon'=>'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z', 'color'=>'blue'],
            ['label'=>__('Reviews'),        'value'=>$stats['reviews'] ?? 0, 'icon'=>'M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-3 3-3-3z', 'color'=>'green'],
            ['label'=>__('Studios'),        'value'=>$stats['studios'] ?? 0, 'icon'=>'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', 'color'=>'yellow'],
        ];
    @endphp
    @foreach($cards as $card)
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ $card['label'] }}</span>
                <svg class="w-5 h-5 text-{{ $card['color'] }}-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $card['icon'] }}"/>
                </svg>
            </div>
            <div class="text-3xl font-extrabold text-gray-900 dark:text-white">{{ number_format($card['value']) }}</div>
        </div>
    @endforeach
</div>

{{-- Extra stats row --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 text-center">
        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total_views'] ?? 0) }}</div>
        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('Total Views') }}</div>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 text-center">
        <div class="text-2xl font-bold text-yellow-500">{{ number_format($stats['pending_reviews'] ?? 0) }}</div>
        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('Pending Reviews') }}</div>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 text-center">
        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['subscribers'] ?? 0) }}</div>
        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('Subscribers') }}</div>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 text-center">
        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['genres'] ?? 0) }}</div>
        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('Genres') }}</div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- Top anime by views --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-700">
            <h2 class="font-semibold text-gray-900 dark:text-white">{{ __('Top Anime by Views') }}</h2>
            <a href="{{ route('animes.index') }}" class="text-xs text-anime-600 dark:text-anime-400 hover:underline">{{ __('View all') }}</a>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            @forelse($topAnime as $anime)
                <div class="flex items-center gap-3 px-5 py-3">
                    <div class="w-8 h-10 rounded-lg overflow-hidden bg-gray-200 dark:bg-gray-700 shrink-0">
                        @if($anime->photo)
                            <img src="{{ asset('storage/'.$anime->photo->file) }}" alt="" class="w-full h-full object-cover">
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $anime->title }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $anime->type }}</p>
                    </div>
                    <span class="text-xs text-gray-500 dark:text-gray-400 shrink-0">{{ number_format($anime->views) }} views</span>
                    <a href="{{ route('animes.edit', $anime->id) }}" class="text-xs text-anime-600 dark:text-anime-400 hover:underline shrink-0">{{ __('Edit') }}</a>
                </div>
            @empty
                <p class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">{{ __('No anime yet.') }}</p>
            @endforelse
        </div>
    </div>

    {{-- User registrations chart --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-gray-900 dark:text-white">{{ __('New Users') }} ({{ __('last 6 months') }})</h2>
            <a href="{{ route('users.index') }}" class="text-xs text-anime-600 dark:text-anime-400 hover:underline">{{ __('View all') }}</a>
        </div>
        @if($registrations->count())
            <div class="flex items-end gap-2 h-28">
                @php $maxReg = $registrations->max('total') ?: 1; @endphp
                @foreach($registrations as $reg)
                    @php $pct = round($reg->total / $maxReg * 100); @endphp
                    <div class="flex-1 flex flex-col items-center gap-1">
                        <span class="text-xs font-semibold text-anime-600 dark:text-anime-400">{{ $reg->total }}</span>
                        <div class="w-full rounded-t-md bg-anime-400 dark:bg-anime-600 transition-all" style="height:{{ max(4, $pct) }}%"></div>
                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $reg->month }}</span>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-8">{{ __('No data yet.') }}</p>
        @endif
    </div>

    {{-- Recent audit logs --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden lg:col-span-2">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-700">
            <h2 class="font-semibold text-gray-900 dark:text-white">{{ __('Recent Activity') }}</h2>
            <a href="{{ route('audit-logs.index') }}" class="text-xs text-anime-600 dark:text-anime-400 hover:underline">{{ __('View all') }}</a>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            @forelse($recentAuditLogs as $log)
                <div class="flex items-center gap-3 px-5 py-3">
                    <span class="px-2 py-0.5 rounded text-xs font-semibold
                        {{ $log->action === 'created' ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' :
                           ($log->action === 'deleted' ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400' :
                           'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400') }} shrink-0">
                        {{ $log->action }}
                    </span>
                    <span class="text-sm text-gray-700 dark:text-gray-300 flex-1 min-w-0 truncate">
                        {{ $log->user?->name ?? __('System') }} — {{ class_basename($log->model_type) }} #{{ $log->model_id }}
                    </span>
                    <span class="text-xs text-gray-400 shrink-0">{{ $log->created_at->diffForHumans() }}</span>
                </div>
            @empty
                <p class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">{{ __('No activity yet.') }}</p>
            @endforelse
        </div>
    </div>
</div>

@endsection
