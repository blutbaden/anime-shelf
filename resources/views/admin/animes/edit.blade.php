@extends('layouts.admin')

@section('page-title', __('Edit Anime'))

@section('content')
<div class="max-w-4xl">
    <a href="{{ route('animes.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 mb-5">
        ← {{ __('Back to list') }}
    </a>

    @if($errors->any())
        <div class="mb-5 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-xl p-4">
            @foreach($errors->all() as $error)
                <p class="text-sm text-red-600 dark:text-red-400">{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form action="{{ route('animes.update', $anime->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')
        @include('admin.animes._form', ['anime' => $anime])
        <div class="flex gap-3">
            <button type="submit" class="px-6 py-2.5 bg-anime-600 hover:bg-anime-700 text-white font-semibold rounded-lg text-sm">{{ __('Save Changes') }}</button>
            <a href="{{ route('animes.index') }}" class="px-6 py-2.5 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-semibold rounded-lg text-sm hover:bg-gray-50 dark:hover:bg-gray-700">{{ __('Cancel') }}</a>
        </div>
    </form>
</div>
@endsection
