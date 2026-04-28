@extends('layouts.admin')
@section('page-title', __('Quotes'))
@section('content')
<div class="flex items-center justify-between mb-5">
    <h2 class="text-gray-600 dark:text-gray-400 text-sm">{{ $quotes->total() }} {{ __('quotes') }}</h2>
    <a href="{{ route('quotes.create') }}" class="px-4 py-2 bg-anime-600 hover:bg-anime-700 text-white rounded-lg text-sm font-semibold">+ {{ __('Add Quote') }}</a>
</div>
<div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 dark:bg-gray-700/50">
            <tr>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">{{ __('Quote') }}</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase hidden sm:table-cell">{{ __('Character') }}</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase hidden md:table-cell">{{ __('Anime') }}</th>
                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
            @forelse($quotes as $quote)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                    <td class="px-5 py-3 text-gray-700 dark:text-gray-300 max-w-xs">
                        <p class="italic truncate">"{{ $quote->body }}"</p>
                    </td>
                    <td class="px-5 py-3 hidden sm:table-cell text-gray-600 dark:text-gray-300">{{ $quote->character_name }}</td>
                    <td class="px-5 py-3 hidden md:table-cell text-gray-600 dark:text-gray-300 truncate max-w-[160px]">{{ $quote->anime?->title }}</td>
                    <td class="px-5 py-3 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('quotes.edit', $quote->id) }}" class="text-anime-500 hover:text-anime-700 text-xs font-medium">{{ __('Edit') }}</a>
                            <form action="{{ route('quotes.destroy', $quote->id) }}" method="POST" onsubmit="return confirm('{{ __('Delete?') }}')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-600 text-xs">{{ __('Delete') }}</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-5 py-8 text-center text-gray-500 dark:text-gray-400">{{ __('No quotes.') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $quotes->links() }}</div>
@endsection
