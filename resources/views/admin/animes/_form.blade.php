@php $editing = isset($anime); @endphp

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Left column --}}
    <div class="space-y-5">
        {{-- Cover image --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">{{ __('Cover Image') }}</label>
            @if($editing && $anime->photo)
                <img src="{{ asset('storage/'.$anime->photo->file) }}" alt="" class="w-full aspect-[3/4] object-cover rounded-lg mb-3">
            @endif
            <input type="file" name="photo" accept="image/*" class="text-sm text-gray-600 dark:text-gray-400 w-full">
        </div>

        {{-- Basic info --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 space-y-4">
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">{{ __('Type') }}</label>
                <select name="type" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-anime-500">
                    @foreach(['TV','Movie','OVA','ONA','Special'] as $t)
                        <option value="{{ $t }}" {{ old('type', $anime->type ?? 'TV') === $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">{{ __('Status') }}</label>
                <select name="status" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-anime-500">
                    @foreach(['airing','finished','upcoming'] as $s)
                        <option value="{{ $s }}" {{ old('status', $anime->status ?? '') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">{{ __('Episodes') }}</label>
                <input type="number" name="episodes" min="0" value="{{ old('episodes', $anime->episodes ?? '') }}" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-anime-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">{{ __('Episode Duration (min)') }}</label>
                <input type="number" name="episode_duration" min="0" value="{{ old('episode_duration', $anime->episode_duration ?? '') }}" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-anime-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">{{ __('Season') }}</label>
                <select name="season" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-anime-500">
                    <option value="">—</option>
                    @foreach(['Winter','Spring','Summer','Fall'] as $s)
                        <option value="{{ $s }}" {{ old('season', $anime->season ?? '') === $s ? 'selected' : '' }}>{{ $s }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">{{ __('Season Year') }}</label>
                <input type="number" name="season_year" min="1900" max="{{ now()->year+2 }}" value="{{ old('season_year', $anime->season_year ?? '') }}" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-anime-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">{{ __('Rating') }}</label>
                <select name="rating" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-anime-500">
                    <option value="">—</option>
                    @foreach(['G','PG','PG-13','R','R+'] as $r)
                        <option value="{{ $r }}" {{ old('rating', $anime->rating ?? '') === $r ? 'selected' : '' }}>{{ $r }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">{{ __('Studio') }}</label>
                <select name="studio_id" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-anime-500">
                    <option value="">—</option>
                    @foreach($studios as $studio)
                        <option value="{{ $studio->id }}" {{ old('studio_id', $anime->studio_id ?? '') == $studio->id ? 'selected' : '' }}>{{ $studio->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- Right column --}}
    <div class="lg:col-span-2 space-y-5">
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 space-y-4">
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">{{ __('Title') }} *</label>
                <input type="text" name="title" required value="{{ old('title', $anime->title ?? '') }}" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-anime-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">{{ __('Japanese Title') }}</label>
                <input type="text" name="title_japanese" value="{{ old('title_japanese', $anime->title_japanese ?? '') }}" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-anime-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">{{ __('Synopsis') }}</label>
                <textarea name="synopsis" rows="5" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-anime-500 resize-y">{{ old('synopsis', $anime->synopsis ?? '') }}</textarea>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">{{ __('Trailer URL (YouTube)') }}</label>
                <input type="url" name="trailer_url" value="{{ old('trailer_url', $anime->trailer_url ?? '') }}" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-anime-500">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">{{ __('Aired From') }}</label>
                    <input type="date" name="aired_from" value="{{ old('aired_from', isset($anime->aired_from) ? $anime->aired_from->format('Y-m-d') : '') }}" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-anime-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">{{ __('Aired To') }}</label>
                    <input type="date" name="aired_to" value="{{ old('aired_to', isset($anime->aired_to) ? $anime->aired_to->format('Y-m-d') : '') }}" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-anime-500">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">{{ __('Source') }}</label>
                    <input type="text" name="source" value="{{ old('source', $anime->source ?? '') }}" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-anime-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">{{ __('MAL ID') }}</label>
                    <input type="number" name="mal_id" value="{{ old('mal_id', $anime->mal_id ?? '') }}" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-anime-500">
                </div>
            </div>
        </div>

        {{-- Genres --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-3">{{ __('Genres') }}</label>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                @foreach($genres as $genre)
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="genres[]" value="{{ $genre->id }}"
                               {{ in_array($genre->id, old('genres', $editing ? $anime->genres->pluck('id')->toArray() : [])) ? 'checked' : '' }}
                               class="w-4 h-4 rounded border-gray-300 text-anime-600 focus:ring-anime-500">
                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $genre->name }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        {{-- Tags --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-3">{{ __('Tags') }}</label>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                @foreach($tags as $tag)
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="tags[]" value="{{ $tag->id }}"
                               {{ in_array($tag->id, old('tags', $editing ? $anime->tags->pluck('id')->toArray() : [])) ? 'checked' : '' }}
                               class="w-4 h-4 rounded border-gray-300 text-anime-600 focus:ring-anime-500">
                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $tag->name }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        {{-- SEO --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 space-y-4">
            <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('SEO') }}</h3>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('Meta Title') }}</label>
                <input type="text" name="meta_title" value="{{ old('meta_title', $anime->meta_title ?? '') }}" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-anime-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('Meta Description') }}</label>
                <textarea name="meta_description" rows="2" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-anime-500 resize-none">{{ old('meta_description', $anime->meta_description ?? '') }}</textarea>
            </div>
        </div>
    </div>
</div>
