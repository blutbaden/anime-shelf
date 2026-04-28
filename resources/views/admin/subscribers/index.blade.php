@extends('layouts.admin')
@section('page-title', __('Subscribers'))
@section('content')
<div class="flex flex-wrap items-center justify-between gap-3 mb-5">
    <p class="text-gray-600 dark:text-gray-400 text-sm">{{ $subscribers->total() }} {{ __('subscribers') }}</p>
    <form action="{{ route('subscribers.newsletter') }}" method="POST" onsubmit="return confirm('{{ __('Send newsletter to all subscribers?') }}')">
        @csrf
        <div class="flex gap-2">
            <input type="text" name="subject" required placeholder="{{ __('Newsletter subject...') }}" class="px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-anime-500 w-56">
            <button type="submit" class="px-4 py-2 bg-anime-600 hover:bg-anime-700 text-white rounded-lg text-sm font-semibold">{{ __('Send') }}</button>
        </div>
    </form>
</div>

<div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 dark:bg-gray-700/50">
            <tr>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">{{ __('Email') }}</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase hidden sm:table-cell">{{ __('Subscribed') }}</th>
                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
            @forelse($subscribers as $sub)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                    <td class="px-5 py-3 text-gray-900 dark:text-white">{{ $sub->email }}</td>
                    <td class="px-5 py-3 hidden sm:table-cell text-gray-500 dark:text-gray-400 text-xs">{{ $sub->created_at->format('M d, Y') }}</td>
                    <td class="px-5 py-3 text-right">
                        <form action="{{ route('subscribers.destroy', $sub->id) }}" method="POST" onsubmit="return confirm('{{ __('Remove subscriber?') }}')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-400 hover:text-red-600 text-xs">{{ __('Remove') }}</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="3" class="px-5 py-8 text-center text-gray-500 dark:text-gray-400">{{ __('No subscribers.') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $subscribers->links() }}</div>
@endsection
