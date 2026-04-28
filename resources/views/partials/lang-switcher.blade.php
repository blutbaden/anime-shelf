<div x-data="{ open: false }" class="relative">
    <button @click="open=!open" class="flex items-center gap-1 px-2 py-1.5 rounded-lg text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
        <span class="font-medium uppercase">{{ app()->getLocale() }}</span>
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
    </button>

    <div x-show="open" @click.outside="open=false" x-transition
         class="absolute right-0 mt-1 w-28 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg overflow-hidden z-50">
        <a href="{{ route('lang.switch', 'en') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 {{ app()->getLocale() === 'en' ? 'font-bold text-anime-600 dark:text-anime-400' : '' }}">
            🇬🇧 English
        </a>
        <a href="{{ route('lang.switch', 'ar') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 {{ app()->getLocale() === 'ar' ? 'font-bold text-anime-600 dark:text-anime-400' : '' }}">
            🇸🇦 العربية
        </a>
    </div>
</div>
