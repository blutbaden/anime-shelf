@extends('layouts.admin')
@section('page-title', __('Studios'))
@section('content')
<div class="flex items-center justify-between mb-5">
    <form method="GET" class="flex gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search...') }}"
               class="px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-anime-500 w-52">
        <button class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-sm">{{ __('Search') }}</button>
    </form>
    <a href="{{ route('studios.create') }}" class="px-4 py-2 bg-anime-600 hover:bg-anime-700 text-white rounded-lg text-sm font-semibold">+ {{ __('Add Studio') }}</a>
</div>

<div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 dark:bg-gray-700/50">
            <tr>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('Studio') }}</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide hidden sm:table-cell">{{ __('Founded') }}</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide hidden md:table-cell">{{ __('Anime') }}</th>
                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
            @forelse($studios as $studio)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                @if($studio->photo)
                                    <img src="{{ asset('storage/'.$studio->photo->file) }}" alt="" class="w-full h-full object-cover">
                                @else
                                    <span class="font-bold text-anime-500">{{ strtoupper(substr($studio->name,0,1)) }}</span>
                                @endif
                            </div>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $studio->name }}</span>
                        </div>
                    </td>
                    <td class="px-5 py-3 hidden sm:table-cell text-gray-600 dark:text-gray-300">{{ $studio->founded_year ?? '—' }}</td>
                    <td class="px-5 py-3 hidden md:table-cell text-gray-600 dark:text-gray-300">{{ $studio->animes_count ?? 0 }}</td>
                    <td class="px-5 py-3 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('studios.edit', $studio->id) }}" class="text-anime-500 hover:text-anime-700 text-xs font-medium">{{ __('Edit') }}</a>
                            <form action="{{ route('studios.destroy', $studio->id) }}" method="POST" onsubmit="return confirm('{{ __('Delete?') }}')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-600 text-xs">{{ __('Delete') }}</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-5 py-8 text-center text-gray-500 dark:text-gray-400">{{ __('No studios found.') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $studios->withQueryString()->links() }}</div>
@endsection
