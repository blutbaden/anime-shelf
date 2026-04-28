@extends('layouts.app')

@section('title', __('My Profile'))

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">{{ __('My Profile') }}</h1>

    {{-- Profile Info --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-5">{{ __('Profile Information') }}</h2>

        @if(session('profile_updated'))
            <div class="mb-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-lg px-4 py-3 text-sm text-green-700 dark:text-green-400">
                {{ __('Profile updated successfully.') }}
            </div>
        @endif

        <form action="{{ route('update-user-profile') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PATCH')

            <div class="flex items-center gap-5">
                <div class="w-20 h-20 rounded-full overflow-hidden bg-anime-100 dark:bg-anime-900/30 flex items-center justify-center">
                    @if(auth()->user()->photo)
                        <img src="{{ asset('storage/'.auth()->user()->photo->file) }}" alt="" class="w-full h-full object-cover">
                    @else
                        <span class="text-3xl font-bold text-anime-600">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                    @endif
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Profile Photo') }}</label>
                    <input type="file" name="photo" accept="image/*" class="text-sm text-gray-600 dark:text-gray-400">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Name') }}</label>
                <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" required
                       class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-anime-500">
                @error('name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Email') }}</label>
                <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required
                       class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-anime-500">
                @error('email')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            <button type="submit" class="px-6 py-2.5 bg-anime-600 hover:bg-anime-700 text-white font-semibold rounded-lg text-sm transition-colors">
                {{ __('Save Changes') }}
            </button>
        </form>
    </div>

    {{-- Change Password --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-5">{{ __('Change Password') }}</h2>

        @if(session('password_updated'))
            <div class="mb-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-lg px-4 py-3 text-sm text-green-700 dark:text-green-400">
                {{ __('Password updated successfully.') }}
            </div>
        @endif

        <form action="{{ route('update-password') }}" method="POST" class="space-y-5">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Current Password') }}</label>
                <input type="password" name="current_password" required
                       class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-anime-500">
                @error('current_password')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('New Password') }}</label>
                <input type="password" name="password" required
                       class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-anime-500">
                @error('password')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Confirm New Password') }}</label>
                <input type="password" name="password_confirmation" required
                       class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-anime-500">
            </div>
            <button type="submit" class="px-6 py-2.5 bg-anime-600 hover:bg-anime-700 text-white font-semibold rounded-lg text-sm transition-colors">
                {{ __('Update Password') }}
            </button>
        </form>
    </div>

    {{-- Danger Zone --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-red-200 dark:border-red-800 p-6">
        <h2 class="text-lg font-semibold text-red-600 dark:text-red-400 mb-2">{{ __('Danger Zone') }}</h2>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">{{ __('Permanently delete your account and all associated data. This action cannot be undone.') }}</p>
        <form action="{{ route('account.delete') }}" method="POST" onsubmit="return confirm('{{ __('Are you sure? This cannot be undone.') }}')">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg text-sm transition-colors">
                {{ __('Delete My Account') }}
            </button>
        </form>
    </div>
</div>
@endsection
