@extends('layouts.admin')
@section('page-title', __('Reviews'))
@section('content')
<div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 dark:bg-gray-700/50">
            <tr>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">{{ __('User') }}</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase hidden sm:table-cell">{{ __('Anime') }}</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase hidden md:table-cell">{{ __('Rate') }}</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase hidden lg:table-cell">{{ __('Status') }}</th>
                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
            @forelse($reviews as $review)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                    <td class="px-5 py-3">
                        <p class="font-medium text-gray-900 dark:text-white">{{ $review->user?->name ?? '—' }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 max-w-xs truncate">{{ $review->body }}</p>
                    </td>
                    <td class="px-5 py-3 hidden sm:table-cell text-gray-600 dark:text-gray-300 max-w-[160px] truncate">{{ $review->anime?->title }}</td>
                    <td class="px-5 py-3 hidden md:table-cell text-yellow-500 font-bold">{{ $review->rate }}/10</td>
                    <td class="px-5 py-3 hidden lg:table-cell">
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $review->is_active ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' : 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400' }}">
                            {{ $review->is_active ? __('Active') : __('Pending') }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <form action="{{ route('reviews.update', $review->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <input type="hidden" name="is_active" value="{{ $review->is_active ? 0 : 1 }}">
                                <button type="submit" class="text-xs {{ $review->is_active ? 'text-yellow-500 hover:text-yellow-700' : 'text-green-500 hover:text-green-700' }} font-medium">
                                    {{ $review->is_active ? __('Hide') : __('Approve') }}
                                </button>
                            </form>
                            <form action="{{ route('reviews.destroy', $review->id) }}" method="POST" onsubmit="return confirm('{{ __('Delete?') }}')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-600 text-xs">{{ __('Delete') }}</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-5 py-8 text-center text-gray-500 dark:text-gray-400">{{ __('No reviews.') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $reviews->links() }}</div>
@endsection
