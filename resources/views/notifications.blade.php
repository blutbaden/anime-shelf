@extends('layouts.app')

@section('title', __('Notifications'))

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ __('Notifications') }}</h1>
        @if($notifications->whereNull('read_at')->count())
            <form action="{{ route('notifications.read-all') }}" method="POST">
                @csrf
                <button type="submit" class="text-sm text-anime-600 dark:text-anime-400 hover:underline">{{ __('Mark all as read') }}</button>
            </form>
        @endif
    </div>

    @if($notifications->count())
        <div class="space-y-2">
            @foreach($notifications as $notification)
                <div class="flex items-start gap-4 bg-white dark:bg-gray-800 rounded-xl border {{ $notification->read_at ? 'border-gray-100 dark:border-gray-700' : 'border-anime-200 dark:border-anime-800 bg-anime-50/30 dark:bg-anime-900/10' }} p-4">
                    <div class="w-9 h-9 rounded-full {{ $notification->read_at ? 'bg-gray-100 dark:bg-gray-700' : 'bg-anime-100 dark:bg-anime-900/40' }} flex items-center justify-center shrink-0">
                        @php $type = class_basename($notification->type); @endphp
                        @if(str_contains($type, 'Reply'))
                            <span class="text-lg">💬</span>
                        @elseif(str_contains($type, 'Review'))
                            <span class="text-lg">⭐</span>
                        @elseif(str_contains($type, 'Anime'))
                            <span class="text-lg">🎌</span>
                        @else
                            <span class="text-lg">🔔</span>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-gray-800 dark:text-gray-200">{{ $notification->data['message'] ?? '' }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $notification->created_at->diffForHumans() }}</p>
                    </div>
                    @if(!$notification->read_at)
                        <form action="{{ route('notifications.read', $notification->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="text-xs text-gray-400 hover:text-anime-600 dark:hover:text-anime-400 shrink-0">✓</button>
                        </form>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="mt-6">{{ $notifications->links() }}</div>
    @else
        <div class="text-center py-20">
            <svg class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            <p class="text-gray-500 dark:text-gray-400">{{ __('No notifications yet.') }}</p>
        </div>
    @endif
</div>
@endsection
