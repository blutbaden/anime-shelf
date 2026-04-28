@php $editing = isset($genre); @endphp
@if($editing && $genre->photo)
    <img src="{{ asset('storage/'.$genre->photo->file) }}" alt="" class="w-16 h-16 rounded-lg object-cover mb-2">
@endif
<div>
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Image') }}</label>
    <input type="file" name="photo" accept="image/*" class="text-sm text-gray-600 dark:text-gray-400">
</div>
<div>
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Name') }} *</label>
    <input type="text" name="name" required value="{{ old('name', $genre->name ?? '') }}" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-anime-500">
</div>
<div>
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Description') }}</label>
    <textarea name="description" rows="3" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-anime-500 resize-none">{{ old('description', $genre->description ?? '') }}</textarea>
</div>
