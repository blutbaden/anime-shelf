@php $editing = isset($quote); @endphp
<div>
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Anime') }} *</label>
    <select name="anime_id" required class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-anime-500">
        <option value="">{{ __('Select anime...') }}</option>
        @foreach($animes as $anime)
            <option value="{{ $anime->id }}" {{ old('anime_id', $quote->anime_id ?? '') == $anime->id ? 'selected' : '' }}>{{ $anime->title }}</option>
        @endforeach
    </select>
</div>
<div>
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Character Name') }} *</label>
    <input type="text" name="character_name" required value="{{ old('character_name', $quote->character_name ?? '') }}" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-anime-500">
</div>
<div>
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Quote') }} *</label>
    <textarea name="body" rows="4" required class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-anime-500 resize-none">{{ old('body', $quote->body ?? '') }}</textarea>
</div>
