@php $editing = isset($studio); @endphp

@if($editing && $studio->photo)
    <div class="mb-2">
        <img src="{{ asset('storage/'.$studio->photo->file) }}" alt="" class="w-20 h-20 rounded-xl object-cover">
    </div>
@endif

<div>
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Logo / Image') }}</label>
    <input type="file" name="photo" accept="image/*" class="text-sm text-gray-600 dark:text-gray-400">
</div>
<div>
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Name') }} *</label>
    <input type="text" name="name" required value="{{ old('name', $studio->name ?? '') }}" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-anime-500">
</div>
<div>
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Description') }}</label>
    <textarea name="description" rows="3" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-anime-500 resize-none">{{ old('description', $studio->description ?? '') }}</textarea>
</div>
<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Founded Year') }}</label>
        <input type="number" name="founded_year" value="{{ old('founded_year', $studio->founded_year ?? '') }}" min="1900" max="{{ now()->year }}" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-anime-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Headquarters') }}</label>
        <input type="text" name="headquarters" value="{{ old('headquarters', $studio->headquarters ?? '') }}" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-anime-500">
    </div>
</div>
<div>
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Website') }}</label>
    <input type="url" name="website" value="{{ old('website', $studio->website ?? '') }}" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-anime-500">
</div>
