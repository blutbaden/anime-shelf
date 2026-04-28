@extends('layouts.admin')
@section('page-title', __('Contacts'))
@section('content')
<div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 dark:bg-gray-700/50">
            <tr>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">{{ __('Sender') }}</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase hidden sm:table-cell">{{ __('Subject') }}</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase hidden md:table-cell">{{ __('Date') }}</th>
                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
            @forelse($contacts as $contact)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 {{ !$contact->is_read ? 'bg-anime-50/20 dark:bg-anime-900/10' : '' }}">
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-2">
                            @if(!$contact->is_read)<span class="w-2 h-2 rounded-full bg-anime-500 shrink-0"></span>@endif
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $contact->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $contact->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3 hidden sm:table-cell text-gray-600 dark:text-gray-300">{{ $contact->subject }}</td>
                    <td class="px-5 py-3 hidden md:table-cell text-gray-500 dark:text-gray-400 text-xs">{{ $contact->created_at->format('M d, Y') }}</td>
                    <td class="px-5 py-3 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('contacts.show', $contact->id) }}" class="text-anime-500 hover:text-anime-700 text-xs font-medium">{{ __('View') }}</a>
                            <form action="{{ route('contacts.destroy', $contact->id) }}" method="POST" onsubmit="return confirm('{{ __('Delete?') }}')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-600 text-xs">{{ __('Delete') }}</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-5 py-8 text-center text-gray-500 dark:text-gray-400">{{ __('No messages.') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $contacts->links() }}</div>
@endsection
