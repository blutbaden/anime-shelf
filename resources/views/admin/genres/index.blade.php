@extends('layouts.admin')
@section('page-title', __('Genres'))
@section('content')
<div class="flex items-center justify-between mb-5">
    <h2 class="text-gray-600 dark:text-gray-400 text-sm">{{ $genres->total() }} {{ __('genres') }}</h2>
    <a href="{{ route('genres.create') }}" class="px-4 py-2 bg-anime-600 hover:bg-anime-700 text-white rounded-lg text-sm font-semibold">+ {{ __('Add Genre') }}</a>
</div>
<div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 dark:bg-gray-700/50">
            <tr>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">{{ __('Name') }}</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase hidden sm:table-cell">{{ __('Slug') }}</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase hidden md:table-cell">{{ __('Anime') }}</th>
                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
            @forelse($genres as $genre)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                    <td class="px-5 py-3 font-medium text-gray-900 dark:text-white">{{ $genre->name }}</td>
                    <td class="px-5 py-3 hidden sm:table-cell text-gray-500 dark:text-gray-400 font-mono text-xs">{{ $genre->slug }}</td>
                    <td class="px-5 py-3 hidden md:table-cell text-gray-600 dark:text-gray-300">{{ $genre->animes_count ?? 0 }}</td>
                    <td class="px-5 py-3 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('genres.edit', $genre->id) }}" class="text-anime-500 hover:text-anime-700 text-xs font-medium">{{ __('Edit') }}</a>
                            <form action="{{ route('genres.destroy', $genre->id) }}" method="POST" onsubmit="return confirm('{{ __('Delete?') }}')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-600 text-xs">{{ __('Delete') }}</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-5 py-8 text-center text-gray-500 dark:text-gray-400">{{ __('No genres.') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $genres->links() }}</div>
@endsection
