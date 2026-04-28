@extends('layouts.admin')
@section('page-title', __('Users'))
@section('content')
<div class="flex items-center justify-between mb-5">
    <form method="GET" class="flex gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search users...') }}"
               class="px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-anime-500 w-52">
        <button class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-sm">{{ __('Search') }}</button>
    </form>
    <a href="{{ route('users.create') }}" class="px-4 py-2 bg-anime-600 hover:bg-anime-700 text-white rounded-lg text-sm font-semibold">+ {{ __('Add User') }}</a>
</div>

<div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 dark:bg-gray-700/50">
            <tr>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">{{ __('User') }}</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase hidden md:table-cell">{{ __('Role') }}</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase hidden lg:table-cell">{{ __('Joined') }}</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase hidden sm:table-cell">{{ __('Status') }}</th>
                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
            @forelse($users as $user)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-anime-100 dark:bg-anime-900/30 flex items-center justify-center text-anime-700 dark:text-anime-300 font-bold text-sm shrink-0">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $user->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3 hidden md:table-cell">
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $user->role?->name === 'admin' ? 'bg-anime-100 dark:bg-anime-900/30 text-anime-700 dark:text-anime-400' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400' }}">
                            {{ $user->role?->name ?? 'user' }}
                        </span>
                    </td>
                    <td class="px-5 py-3 hidden lg:table-cell text-gray-500 dark:text-gray-400 text-xs">{{ $user->created_at->format('M d, Y') }}</td>
                    <td class="px-5 py-3 hidden sm:table-cell">
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $user->is_active ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400' }}">
                            {{ $user->is_active ? __('Active') : __('Banned') }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('users.edit', $user->id) }}" class="text-anime-500 hover:text-anime-700 text-xs font-medium">{{ __('Edit') }}</a>
                            <form action="{{ route('users.toggle-status', $user->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit" class="text-xs {{ $user->is_active ? 'text-yellow-500 hover:text-yellow-700' : 'text-green-500 hover:text-green-700' }} font-medium">
                                    {{ $user->is_active ? __('Ban') : __('Activate') }}
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-5 py-8 text-center text-gray-500 dark:text-gray-400">{{ __('No users.') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $users->withQueryString()->links() }}</div>
@endsection
