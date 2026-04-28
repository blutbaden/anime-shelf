@extends('layouts.app')

@section('title', __('Unsubscribed'))

@section('content')
<div class="max-w-md mx-auto px-4 py-20 text-center">
    <div class="w-20 h-20 mx-auto bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mb-6">
        <svg class="w-10 h-10 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
    </div>
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">{{ __('Unsubscribed') }}</h1>
    <p class="text-gray-500 dark:text-gray-400 mb-8">{{ __("You've been removed from our newsletter list. You won't receive any further emails.") }}</p>
    <a href="{{ route('home') }}" class="px-6 py-2.5 bg-anime-600 hover:bg-anime-700 text-white font-semibold rounded-xl transition-colors">
        {{ __('Back to Home') }}
    </a>
</div>
@endsection
