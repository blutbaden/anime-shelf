@extends('layouts.admin')
@section('page-title', $anime->title . ' — Episodes')
@section('content')

<div class="flex items-center justify-between mb-5">
    <div>
        <a href="{{ route('animes.index') }}" class="text-xs text-gray-500 dark:text-gray-400 hover:underline">← {{ __('Back to Anime') }}</a>
        <h2 class="text-gray-600 dark:text-gray-400 text-sm mt-1">{{ $episodes->total() }} {{ __('episodes') }} · <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $anime->title }}</span></h2>
    </div>
    <a href="{{ route('episodes.create', $anime) }}" class="px-4 py-2 bg-anime-600 hover:bg-anime-700 text-white rounded-lg text-sm font-semibold">+ {{ __('Add Episode') }}</a>
</div>

@if(session('success'))
    <div class="mb-4 px-4 py-3 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 text-green-700 dark:text-green-300 rounded-lg text-sm">{{ session('success') }}</div>
@endif

<div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 dark:bg-gray-700/50">
            <tr>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">#</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">{{ __('Title') }}</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase hidden md:table-cell">{{ __('URL') }}</th>
                <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">{{ __('Status') }}</th>
                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
            @forelse($episodes as $episode)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                    <td class="px-5 py-3 font-mono text-gray-700 dark:text-gray-300">{{ $episode->number }}</td>
                    <td class="px-5 py-3 text-gray-900 dark:text-white">{{ $episode->title ?? __('Episode :n', ['n' => $episode->number]) }}</td>
                    <td class="px-5 py-3 hidden md:table-cell">
                        <a href="{{ $episode->url }}" target="_blank" class="text-anime-600 dark:text-anime-400 hover:underline truncate max-w-xs block">{{ Str::limit($episode->url, 50) }}</a>
                    </td>
                    <td class="px-5 py-3 text-center">
                        @if($episode->is_active)
                            <span class="px-2 py-0.5 bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300 rounded-full text-xs">{{ __('Active') }}</span>
                        @else
                            <span class="px-2 py-0.5 bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 rounded-full text-xs">{{ __('Hidden') }}</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-right space-x-2">
                        <a href="{{ route('episode.watch', [$anime, $episode]) }}" target="_blank"
                           class="inline-text text-gray-500 dark:text-gray-400 hover:text-anime-600 dark:hover:text-anime-400 text-xs">▶ {{ __('Preview') }}</a>
                        <a href="{{ route('episodes.edit', [$anime, $episode]) }}"
                           class="inline-text text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 text-xs">{{ __('Edit') }}</a>
                        <form method="POST" action="{{ route('episodes.destroy', [$anime, $episode]) }}" class="inline" onsubmit="return confirm('Delete episode {{ $episode->number }}?')">
                            @csrf @method('DELETE')
                            <button class="text-red-500 hover:text-red-700 text-xs">{{ __('Delete') }}</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-5 py-10 text-center text-gray-400 dark:text-gray-500">{{ __('No episodes yet.') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $episodes->links() }}</div>
@endsection
