@extends('layouts.admin')
@section('page-title', __('Audit Logs'))
@section('content')

<div class="flex flex-wrap items-center justify-between gap-3 mb-5">
    <form method="GET" class="flex flex-wrap gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search action/model...') }}"
               class="px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-anime-500 w-48">
        <select name="action" class="px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-anime-500">
            <option value="">{{ __('All actions') }}</option>
            @foreach(['created','updated','deleted'] as $a)
                <option value="{{ $a }}" {{ request('action') === $a ? 'selected' : '' }}>{{ ucfirst($a) }}</option>
            @endforeach
        </select>
        <input type="date" name="from" value="{{ request('from') }}" class="px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-anime-500">
        <input type="date" name="to" value="{{ request('to') }}" class="px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-anime-500">
        <button class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-sm">{{ __('Filter') }}</button>
    </form>
    <div class="flex gap-2">
        <a href="{{ route('audit-logs.export.csv') }}" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg text-sm hover:bg-gray-50 dark:hover:bg-gray-700">⬇ CSV</a>
        <a href="{{ route('audit-logs.export.pdf') }}" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg text-sm hover:bg-gray-50 dark:hover:bg-gray-700">⬇ PDF</a>
    </div>
</div>

<div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 dark:bg-gray-700/50">
            <tr>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">{{ __('User') }}</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">{{ __('Action') }}</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase hidden sm:table-cell">{{ __('Model') }}</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase hidden md:table-cell">{{ __('IP') }}</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase hidden lg:table-cell">{{ __('When') }}</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
            @forelse($logs as $log)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                    <td class="px-5 py-3 text-gray-700 dark:text-gray-300">{{ $log->user?->name ?? __('System') }}</td>
                    <td class="px-5 py-3">
                        <span class="px-2 py-0.5 rounded text-xs font-semibold
                            {{ $log->action === 'created' ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' :
                               ($log->action === 'deleted' ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400' :
                               'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400') }}">
                            {{ $log->action }}
                        </span>
                    </td>
                    <td class="px-5 py-3 hidden sm:table-cell text-gray-500 dark:text-gray-400 text-xs">{{ class_basename($log->model_type) }} #{{ $log->model_id }}</td>
                    <td class="px-5 py-3 hidden md:table-cell text-gray-500 dark:text-gray-400 text-xs font-mono">{{ $log->ip_address }}</td>
                    <td class="px-5 py-3 hidden lg:table-cell text-gray-500 dark:text-gray-400 text-xs">{{ $log->created_at->format('M d, Y H:i') }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-5 py-8 text-center text-gray-500 dark:text-gray-400">{{ __('No logs found.') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $logs->withQueryString()->links() }}</div>
@endsection
