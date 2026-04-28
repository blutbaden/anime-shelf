@extends('layouts.admin')

@section('page-title', __('Anime'))

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3 mb-5">
    <form method="GET" class="flex gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search...') }}"
               class="px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-anime-500 w-56">
        <button class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-sm hover:bg-gray-200 dark:hover:bg-gray-600">{{ __('Search') }}</button>
        @if(request('search'))<a href="{{ route('animes.index') }}" class="px-3 py-2 text-sm text-red-500 hover:text-red-700">×</a>@endif
    </form>
    <div class="flex gap-2">
        <a href="{{ route('jikan.index') }}" class="px-4 py-2 border border-anime-300 dark:border-anime-700 text-anime-600 dark:text-anime-400 rounded-lg text-sm hover:bg-anime-50 dark:hover:bg-anime-900/20 font-medium">
            {{ __('Jikan Import') }}
        </a>
        <a href="{{ route('animes.create') }}" class="px-4 py-2 bg-anime-600 hover:bg-anime-700 text-white rounded-lg text-sm font-semibold">
            + {{ __('Add Anime') }}
        </a>
    </div>
</div>

<div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
    <form id="bulk-form" action="{{ route('animes.bulk-destroy') }}" method="POST" onsubmit="return confirm('{{ __('Delete selected?') }}')">
        @csrf
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700/50">
                <tr>
                    <th class="px-4 py-3 text-left"><input type="checkbox" id="check-all" class="w-4 h-4 rounded border-gray-300 text-anime-600"></th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('Anime') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide hidden sm:table-cell">{{ __('Type') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide hidden md:table-cell">{{ __('Status') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide hidden lg:table-cell">{{ __('Views') }}</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($animes as $anime)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                        <td class="px-4 py-3"><input type="checkbox" name="ids[]" value="{{ $anime->id }}" class="bulk-check w-4 h-4 rounded border-gray-300 text-anime-600"></td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-10 rounded-lg overflow-hidden bg-gray-200 dark:bg-gray-700 shrink-0">
                                    @if($anime->photo)
                                        <img src="{{ asset('storage/'.$anime->photo->file) }}" alt="" class="w-full h-full object-cover">
                                    @endif
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ Str::limit($anime->title, 40) }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $anime->studio?->name }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 hidden sm:table-cell text-gray-600 dark:text-gray-300">{{ $anime->type }}</td>
                        <td class="px-4 py-3 hidden md:table-cell">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                {{ $anime->status === 'airing' ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' :
                                   ($anime->status === 'upcoming' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400' :
                                   'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400') }}">
                                {{ ucfirst($anime->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 hidden lg:table-cell text-gray-600 dark:text-gray-300">{{ number_format($anime->views) }}</td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('animes.show', $anime->id) }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200" title="{{ __('View') }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <a href="{{ route('episodes.index', $anime->id) }}" class="text-green-500 hover:text-green-700" title="{{ __('Episodes') }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </a>
                                <a href="{{ route('animes.edit', $anime->id) }}" class="text-anime-500 hover:text-anime-700" title="{{ __('Edit') }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <form action="{{ route('animes.destroy', $anime->id) }}" method="POST" onsubmit="return confirm('{{ __('Delete this anime?') }}')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-400 hover:text-red-600" title="{{ __('Delete') }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-10 text-center text-gray-500 dark:text-gray-400">{{ __('No anime found.') }}</td></tr>
                @endforelse
            </tbody>
        </table>

        @if($animes->count())
            <div class="flex items-center gap-3 px-4 py-3 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30">
                <button type="submit" class="px-4 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded-lg text-xs font-semibold">{{ __('Delete Selected') }}</button>
            </div>
        @endif
    </form>
</div>

<div class="mt-4">{{ $animes->withQueryString()->links() }}</div>

@push('scripts')
<script>
document.getElementById('check-all').addEventListener('change', function () {
    document.querySelectorAll('.bulk-check').forEach(cb => cb.checked = this.checked);
});
</script>
@endpush
@endsection
