@extends('layouts.admin')
@section('page-title', __('Add Episode') . ' — ' . $anime->title)
@section('content')

<div class="mb-5">
    <a href="{{ route('episodes.index', $anime) }}" class="text-xs text-gray-500 dark:text-gray-400 hover:underline">← {{ __('Back to Episodes') }}</a>
</div>

<div class="max-w-2xl bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
    <form method="POST" action="{{ route('episodes.store', $anime) }}" class="space-y-5">
        @csrf

        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Series #') }} <span class="text-red-500">*</span></label>
                <input type="number" name="series" value="{{ old('series', $series) }}" min="1" required
                       class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-anime-500 focus:border-transparent">
                <p class="text-xs text-gray-400 mt-1">{{ __('Max existing: :n', ['n' => $maxSeries]) }}</p>
                @error('series')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Episode #') }} <span class="text-red-500">*</span></label>
                <input type="number" name="number" value="{{ old('number', $next) }}" min="1" required
                       class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-anime-500 focus:border-transparent">
                @error('number')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Duration (min)') }}</label>
                <input type="number" name="duration" value="{{ old('duration') }}" min="1" max="300" placeholder="24"
                       class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-anime-500 focus:border-transparent">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Title') }}</label>
            <input type="text" name="title" value="{{ old('title') }}" placeholder="{{ __('Optional episode title') }}"
                   class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-anime-500 focus:border-transparent">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Video URL') }} <span class="text-red-500">*</span></label>
            <input type="url" name="url" value="{{ old('url') }}" required
                   placeholder="https://www.youtube.com/watch?v=... or direct video URL"
                   class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-anime-500 focus:border-transparent">
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ __('Supports YouTube, Dailymotion, or any direct .mp4/.webm URL') }}</p>
            @error('url')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Description') }}</label>
            <textarea name="description" rows="3" placeholder="{{ __('Optional episode summary…') }}"
                      class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-anime-500 focus:border-transparent">{{ old('description') }}</textarea>
        </div>

        <div class="flex items-center gap-2">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', true) ? 'checked' : '' }}
                   class="rounded border-gray-300 dark:border-gray-600 text-anime-600 focus:ring-anime-500">
            <label for="is_active" class="text-sm text-gray-700 dark:text-gray-300">{{ __('Visible to users') }}</label>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="px-5 py-2 bg-anime-600 hover:bg-anime-700 text-white rounded-lg text-sm font-semibold">{{ __('Save Episode') }}</button>
            <a href="{{ route('episodes.index', $anime) }}" class="px-5 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg text-sm hover:bg-gray-50 dark:hover:bg-gray-700">{{ __('Cancel') }}</a>
        </div>
    </form>
</div>
@endsection
