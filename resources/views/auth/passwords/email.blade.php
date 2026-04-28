<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: localStorage.getItem('darkMode')==='true' }" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Reset Password') }} — {{ config('app.name') }}</title>
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="min-h-screen bg-gray-50 dark:bg-gray-900 flex items-center justify-center p-4">

<div class="w-full max-w-md">
    <div class="text-center mb-8">
        <a href="{{ route('home') }}" class="inline-flex items-center gap-3">
            <svg class="w-10 h-10 text-anime-500" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 14.5v-9l6 4.5-6 4.5z"/>
            </svg>
            <span class="text-2xl font-bold text-gray-900 dark:text-white">AnimeShelf</span>
        </a>
        <p class="mt-2 text-gray-500 dark:text-gray-400 text-sm">{{ __('Reset your password') }}</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-8">

        @if (session('status'))
            <div class="mb-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-lg p-3">
                <p class="text-sm text-green-600 dark:text-green-400">{{ session('status') }}</p>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-lg p-3">
                @foreach ($errors->all() as $error)
                    <p class="text-sm text-red-600 dark:text-red-400">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <p class="text-sm text-gray-600 dark:text-gray-400 mb-5">{{ __("Enter your email address and we'll send you a link to reset your password.") }}</p>

        <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
            @csrf
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Email') }}</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                       class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-anime-500 focus:border-transparent text-sm">
            </div>
            <button type="submit" class="w-full py-2.5 px-4 bg-anime-600 hover:bg-anime-700 text-white font-semibold rounded-lg transition-colors text-sm">
                {{ __('Send Reset Link') }}
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-gray-500 dark:text-gray-400">
            <a href="{{ route('login') }}" class="text-anime-600 hover:text-anime-700 dark:text-anime-400 font-medium">← {{ __('Back to Sign In') }}</a>
        </p>
    </div>
</div>

<script src="{{ mix('js/app.js') }}"></script>
</body>
</html>
