@extends('layouts.admin')

@section('page-title', __('Dashboard'))

@section('content')

{{-- Stats --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @php
        $stats = [
            ['label'=>__('Total Anime'), 'value'=>$totalAnimes ?? 0, 'icon'=>'M15 10l4.553-2.069A1 1 0 0121 8.868V15.131a1 1 0 01-1.447.9L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z', 'color'=>'anime'],
            ['label'=>__('Total Users'), 'value'=>$totalUsers ?? 0, 'icon'=>'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z', 'color'=>'blue'],
            ['label'=>__('Reviews'), 'value'=>$totalReviews ?? 0, 'icon'=>'M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-3 3-3-3z', 'color'=>'green'],
            ['label'=>__('Studios'), 'value'=>$totalStudios ?? 0, 'icon'=>'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', 'color'=>'yellow'],
        ];
    @endphp
    @foreach($stats as $stat)
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ $stat['label'] }}</span>
                <svg class="w-5 h-5 text-{{ $stat['color'] }}-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $stat['icon'] }}"/></svg>
            </div>
            <div class="text-3xl font-extrabold text-gray-900 dark:text-white">{{ number_format($stat['value']) }}</div>
        </div>
    @endforeach
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- Recent anime --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-700">
            <h2 class="font-semibold text-gray-900 dark:text-white">{{ __('Recent Anime') }}</h2>
            <a href="{{ route('animes.index') }}" class="text-xs text-anime-600 dark:text-anime-400 hover:underline">{{ __('View all') }}</a>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            @forelse($recentAnimes ?? [] as $anime)
                <div class="flex items-center gap-3 px-5 py-3">
                    <div class="w-10 h-12 rounded-lg overflow-hidden bg-gray-200 dark:bg-gray-700 shrink-0">
                        @if($anime->photo)
                            <img src="{{ asset('storage/'.$anime->photo->file) }}" alt="" class="w-full h-full object-cover">
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $anime->title }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $anime->type }} · {{ $anime->created_at->diffForHumans() }}</p>
                    </div>
                    <a href="{{ route('animes.edit', $anime->id) }}" class="text-xs text-anime-600 dark:text-anime-400 hover:underline shrink-0">{{ __('Edit') }}</a>
                </div>
            @empty
                <p class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">{{ __('No anime yet.') }}</p>
            @endforelse
        </div>
    </div>

    {{-- Recent reviews --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-700">
            <h2 class="font-semibold text-gray-900 dark:text-white">{{ __('Recent Reviews') }}</h2>
            <a href="{{ route('reviews.index') }}" class="text-xs text-anime-600 dark:text-anime-400 hover:underline">{{ __('View all') }}</a>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            @forelse($recentReviews ?? [] as $review)
                <div class="px-5 py-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $review->user?->name ?? __('Unknown') }}</span>
                        <span class="text-xs text-yellow-500 font-bold">{{ $review->rate }}/10</span>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 truncate">{{ $review->anime?->title }}</p>
                    <p class="text-xs text-gray-600 dark:text-gray-300 mt-1 line-clamp-2">{{ $review->body }}</p>
                    @if(!$review->is_active)
                        <span class="inline-block mt-1 text-xs bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400 px-2 py-0.5 rounded-full">{{ __('Pending') }}</span>
                    @endif
                </div>
            @empty
                <p class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">{{ __('No reviews yet.') }}</p>
            @endforelse
        </div>
    </div>

    {{-- New users --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-700">
            <h2 class="font-semibold text-gray-900 dark:text-white">{{ __('New Users') }}</h2>
            <a href="{{ route('users.index') }}" class="text-xs text-anime-600 dark:text-anime-400 hover:underline">{{ __('View all') }}</a>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            @forelse($recentUsers ?? [] as $user)
                <div class="flex items-center gap-3 px-5 py-3">
                    <div class="w-8 h-8 rounded-full bg-anime-100 dark:bg-anime-900/30 flex items-center justify-center text-anime-700 dark:text-anime-300 font-bold text-sm shrink-0">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $user->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                    </div>
                    <span class="text-xs text-gray-400">{{ $user->created_at->diffForHumans() }}</span>
                </div>
            @empty
                <p class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">{{ __('No users yet.') }}</p>
            @endforelse
        </div>
    </div>

    {{-- Contacts --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-700">
            <h2 class="font-semibold text-gray-900 dark:text-white">{{ __('Recent Messages') }}</h2>
            <a href="{{ route('contacts.index') }}" class="text-xs text-anime-600 dark:text-anime-400 hover:underline">{{ __('View all') }}</a>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            @forelse($recentContacts ?? [] as $contact)
                <div class="px-5 py-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $contact->name }}</span>
                        @if(!$contact->is_read)
                            <span class="w-2 h-2 rounded-full bg-anime-500"></span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $contact->subject }}</p>
                </div>
            @empty
                <p class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">{{ __('No messages.') }}</p>
            @endforelse
        </div>
    </div>
</div>

@endsection
