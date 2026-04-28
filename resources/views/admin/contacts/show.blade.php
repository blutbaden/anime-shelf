@extends('layouts.admin')
@section('page-title', __('Message'))
@section('content')
<div class="max-w-2xl">
    <a href="{{ route('contacts.index') }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 mb-5 inline-block">← {{ __('Back') }}</a>
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
        <div class="mb-4 pb-4 border-b border-gray-100 dark:border-gray-700">
            <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $contact->subject }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('From') }} <strong>{{ $contact->name }}</strong> &lt;{{ $contact->email }}&gt; · {{ $contact->created_at->format('M d, Y H:i') }}</p>
        </div>
        <div class="prose dark:prose-invert max-w-none text-gray-700 dark:text-gray-300 text-sm leading-relaxed whitespace-pre-wrap">{{ $contact->body }}</div>
        <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-700 flex justify-end">
            <form action="{{ route('contacts.destroy', $contact->id) }}" method="POST" onsubmit="return confirm('{{ __('Delete?') }}')">
                @csrf @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-semibold">{{ __('Delete') }}</button>
            </form>
        </div>
    </div>
</div>
@endsection
