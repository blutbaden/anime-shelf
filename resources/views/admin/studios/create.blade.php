@extends('layouts.admin')
@section('page-title', __('Add Studio'))
@section('content')
<div class="max-w-2xl">
    <a href="{{ route('studios.index') }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 mb-5 inline-block">← {{ __('Back') }}</a>
    @if($errors->any())
        <div class="mb-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-xl p-4">
            @foreach($errors->all() as $e)<p class="text-sm text-red-600 dark:text-red-400">{{ $e }}</p>@endforeach
        </div>
    @endif
    <form action="{{ route('studios.store') }}" method="POST" enctype="multipart/form-data" class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 space-y-5">
        @csrf
        @include('admin.studios._form')
        <div class="flex gap-3 pt-2">
            <button type="submit" class="px-6 py-2.5 bg-anime-600 hover:bg-anime-700 text-white font-semibold rounded-lg text-sm">{{ __('Create') }}</button>
            <a href="{{ route('studios.index') }}" class="px-6 py-2.5 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg text-sm">{{ __('Cancel') }}</a>
        </div>
    </form>
</div>
@endsection
