<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}"
    x-data="{ darkMode: localStorage.getItem('dark') === 'true', mobileMenu: false, userMenu: false, notifOpen: false }"
    x-init="$watch('darkMode', v => { localStorage.setItem('dark', v); document.documentElement.classList.toggle('dark', v) })"
    :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Anime Shelf'))</title>
    <meta name="description" content="@yield('meta_description', 'Track your anime watch list, discover new series and movies.')">
    <link rel="canonical" href="@yield('canonical', url()->current())">

    {{-- Open Graph --}}
    <meta property="og:site_name" content="{{ config('app.name') }}">
    <meta property="og:type"      content="@yield('og_type', 'website')">
    <meta property="og:url"       content="@yield('canonical', url()->current())">
    <meta property="og:title"     content="@yield('og_title', config('app.name') . ' — Discover Anime')">
    <meta property="og:description" content="@yield('meta_description', 'Track your anime watch list, discover new series and movies.')">
    <meta property="og:image"     content="@yield('og_image', asset('images/og-default.png'))">

    {{-- Twitter Card --}}
    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:title"       content="@yield('og_title', config('app.name') . ' — Discover Anime')">
    <meta name="twitter:description" content="@yield('meta_description', 'Track your anime watch list, discover new series and movies.')">
    <meta name="twitter:image"       content="@yield('og_image', asset('images/og-default.png'))">

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Tajawal:wght@300;400;500;700&display=swap" rel="stylesheet">

    {{-- CSS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Alpine.js CDN --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.css">

    @stack('head')
</head>
<body class="bg-gray-50 text-gray-900 dark:bg-gray-950 dark:text-gray-100 min-h-screen flex flex-col transition-colors duration-200">

    {{-- ── Navbar ─────────────────────────────────────────────────────────────── --}}
    <nav class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 sticky top-0 z-40 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">

                {{-- Logo --}}
                <a href="{{ route('home') }}" class="flex items-center gap-2 font-bold text-xl text-anime-600 dark:text-anime-400">
                    <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                    {{ config('app.name') }}
                </a>

                {{-- Desktop nav --}}
                <div class="hidden md:flex items-center gap-6 text-sm font-medium">
                    <a href="{{ route('home') }}" class="hover:text-anime-600 dark:hover:text-anime-400 transition">{{ __('Home') }}</a>
                    <a href="{{ route('animes') }}" class="hover:text-anime-600 dark:hover:text-anime-400 transition">{{ __('Anime') }}</a>
                    <a href="{{ route('genres.public') }}" class="hover:text-anime-600 dark:hover:text-anime-400 transition">{{ __('Genres') }}</a>
                    <a href="{{ route('studios.public') }}" class="hover:text-anime-600 dark:hover:text-anime-400 transition">{{ __('Studios') }}</a>
                </div>

                {{-- Right controls --}}
                <div class="flex items-center gap-3">
                    {{-- Search bar (desktop) --}}
                    <div class="hidden md:block relative" x-data="searchAutocomplete()">
                        <input type="text" x-model="query" @input.debounce.350ms="fetch()" @focus="open = true"
                            @click.away="open = false"
                            placeholder="{{ __('Search anime…') }}"
                            class="w-52 pl-9 pr-3 py-1.5 text-sm rounded-full border border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-anime-500">
                        <svg class="absolute left-2.5 top-2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <div x-show="open && results.length" class="absolute top-full left-0 mt-1 w-72 bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 z-50 overflow-hidden">
                            <template x-for="item in results" :key="item.id">
                                <a :href="item.url" class="flex items-center gap-3 px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 transition autocomplete-item">
                                    <img :src="item.image ? '/images/anime/'+item.image : '/images/default.png'" :alt="item.title" class="w-8 h-10 object-cover rounded">
                                    <div>
                                        <p class="text-sm font-medium" x-text="item.title"></p>
                                        <p class="text-xs text-gray-500" x-text="item.studio + ' · ' + item.type"></p>
                                    </div>
                                </a>
                            </template>
                        </div>
                    </div>

                    {{-- Dark mode --}}
                    <button @click="darkMode = !darkMode" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition" :title="darkMode ? 'Light mode' : 'Dark mode'">
                        <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                        <svg x-show="darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </button>

                    {{-- Language --}}
                    @include('partials.lang-switcher')

                    @auth
                        {{-- Notifications --}}
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="relative p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                @if(auth()->user()->unreadNotifications->count())
                                    <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                                @endif
                            </button>
                            <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 z-50 overflow-hidden">
                                <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                                    <span class="font-semibold text-sm">{{ __('Notifications') }}</span>
                                    <a href="{{ route('notifications.read-all') }}" onclick="event.preventDefault(); document.getElementById('read-all-form').submit();" class="text-xs text-anime-600 hover:underline">{{ __('Mark all read') }}</a>
                                </div>
                                <form id="read-all-form" action="{{ route('notifications.read-all') }}" method="POST" class="hidden">@csrf</form>
                                <div class="max-h-64 overflow-y-auto">
                                    @forelse(auth()->user()->notifications()->limit(10)->get() as $n)
                                        <a href="{{ $n->data['url'] ?? '#' }}" class="block px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition {{ $n->read_at ? 'opacity-60' : '' }}">
                                            <p class="text-sm">{{ $n->data['message'] ?? '' }}</p>
                                            <p class="text-xs text-gray-400 mt-0.5">{{ $n->created_at->diffForHumans() }}</p>
                                        </a>
                                    @empty
                                        <div class="px-4 py-6 text-center text-sm text-gray-400">{{ __('No notifications') }}</div>
                                    @endforelse
                                </div>
                                <a href="{{ route('notifications') }}" class="block text-center text-xs text-anime-600 hover:underline py-2 border-t border-gray-100 dark:border-gray-700">{{ __('View all') }}</a>
                            </div>
                        </div>

                        {{-- User menu --}}
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center gap-2 focus:outline-none">
                                @if(auth()->user()->photo)
                                    <img src="{{ asset('images/users/' . auth()->user()->photo->file) }}" class="w-8 h-8 rounded-full object-cover ring-2 ring-anime-500">
                                @else
                                    <div class="w-8 h-8 rounded-full bg-anime-600 flex items-center justify-center text-white text-sm font-bold">{{ substr(auth()->user()->name, 0, 1) }}</div>
                                @endif
                            </button>
                            <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 z-50 py-1 text-sm">
                                <div class="px-4 py-2 border-b border-gray-100 dark:border-gray-700">
                                    <p class="font-semibold truncate">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-gray-400 truncate">{{ auth()->user()->email }}</p>
                                </div>
                                <a href="{{ route('profile') }}" class="block px-4 py-2 hover:bg-gray-50 dark:hover:bg-gray-700">{{ __('Profile') }}</a>
                                <a href="{{ route('watch-list') }}" class="block px-4 py-2 hover:bg-gray-50 dark:hover:bg-gray-700">{{ __('Watch List') }}</a>
                                <a href="{{ route('favorites') }}" class="block px-4 py-2 hover:bg-gray-50 dark:hover:bg-gray-700">{{ __('Favorites') }}</a>
                                <a href="{{ route('stats') }}" class="block px-4 py-2 hover:bg-gray-50 dark:hover:bg-gray-700">{{ __('My Stats') }}</a>
                                @if(auth()->user()->isAdmin())
                                    <a href="/admin/dashboard" class="block px-4 py-2 hover:bg-gray-50 dark:hover:bg-gray-700 text-anime-600 font-medium">{{ __('Admin') }}</a>
                                @endif
                                <div class="border-t border-gray-100 dark:border-gray-700 mt-1">
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-4 py-2 hover:bg-gray-50 dark:hover:bg-gray-700 text-red-500">{{ __('Logout') }}</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium hover:text-anime-600 transition">{{ __('Login') }}</a>
                        <a href="{{ route('register') }}" class="text-sm font-medium bg-anime-600 hover:bg-anime-700 text-white px-4 py-1.5 rounded-full transition">{{ __('Sign up') }}</a>
                    @endauth

                    {{-- Mobile menu btn --}}
                    <button @click="mobileMenu = !mobileMenu" class="md:hidden p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                </div>
            </div>

            {{-- Mobile menu --}}
            <div x-show="mobileMenu" class="md:hidden pb-4 border-t border-gray-100 dark:border-gray-800 pt-3 space-y-1">
                <a href="{{ route('home') }}" class="block px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800">{{ __('Home') }}</a>
                <a href="{{ route('animes') }}" class="block px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800">{{ __('Anime') }}</a>
                <a href="{{ route('genres.public') }}" class="block px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800">{{ __('Genres') }}</a>
                <a href="{{ route('studios.public') }}" class="block px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800">{{ __('Studios') }}</a>
                {{-- Mobile search --}}
                <form action="{{ route('search') }}" method="GET" class="px-3 pt-2">
                    <input name="search" type="text" placeholder="{{ __('Search…') }}" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 focus:outline-none">
                </form>
            </div>
        </div>
    </nav>

    {{-- Flash messages handled by Toastr (see bottom of body) --}}

    {{-- ── Page content ────────────────────────────────────────────────────────── --}}
    <main class="flex-1">
        @yield('content')
    </main>

    {{-- ── Footer ──────────────────────────────────────────────────────────────── --}}
    <footer class="bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800 mt-auto">
        <div class="max-w-7xl mx-auto px-4 py-10 grid grid-cols-1 md:grid-cols-3 gap-8">
            <div>
                <p class="font-bold text-lg text-anime-600 dark:text-anime-400">{{ config('app.name') }}</p>
                <p class="text-sm text-gray-500 mt-2">{{ __('Track anime, write reviews, discover new series.') }}</p>
            </div>
            <div>
                <p class="font-semibold text-sm mb-3">{{ __('Quick Links') }}</p>
                <ul class="space-y-1 text-sm text-gray-500">
                    <li><a href="{{ route('animes') }}" class="hover:text-anime-600 transition">{{ __('Anime Catalog') }}</a></li>
                    <li><a href="{{ route('genres.public') }}" class="hover:text-anime-600 transition">{{ __('Genres') }}</a></li>
                    <li><a href="{{ route('contact') }}" class="hover:text-anime-600 transition">{{ __('Contact') }}</a></li>
                </ul>
            </div>
            <div>
                <p class="font-semibold text-sm mb-3">{{ __('Subscribe') }}</p>
                @auth
                    @if(!$isSubscribed)
                        <form action="{{ route('subscribe') }}" method="POST" class="flex gap-2">
                            @csrf
                            <input type="email" name="email" value="{{ auth()->user()->email }}" placeholder="{{ __('Email') }}"
                                class="flex-1 px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 focus:outline-none">
                            <button type="submit" class="px-4 py-2 bg-anime-600 text-white text-sm rounded-lg hover:bg-anime-700 transition">{{ __('Subscribe') }}</button>
                        </form>
                    @else
                        <p class="text-sm text-gray-500">{{ __('You are subscribed to our newsletter.') }}</p>
                    @endif
                @else
                    <form action="{{ route('subscribe') }}" method="POST" class="flex gap-2">
                        @csrf
                        <input type="email" name="email" placeholder="{{ __('Your email') }}"
                            class="flex-1 px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 focus:outline-none">
                        <button type="submit" class="px-4 py-2 bg-anime-600 text-white text-sm rounded-lg hover:bg-anime-700 transition">{{ __('Subscribe') }}</button>
                    </form>
                @endauth
            </div>
        </div>
        <div class="border-t border-gray-100 dark:border-gray-800 py-4 text-center text-xs text-gray-400">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </div>
    </footer>


    <script>
        function searchAutocomplete() {
            return {
                query: '',
                results: [],
                open: false,
                async fetch() {
                    if (this.query.length < 3) { this.results = []; return; }
                    const res = await window.axios.get('/anime/autocomplete', { params: { q: this.query } });
                    this.results = res.data;
                    this.open = true;
                }
            }
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.js"></script>
    <script>
    toastr.options = { positionClass: 'toast-top-right', timeOut: 4000, progressBar: true, closeButton: true, newestOnTop: true };
    @if(session('success')) toastr.success(@json(session('success'))); @endif
    @if(session('error'))   toastr.error(@json(session('error')));     @endif
    @if(session('warning')) toastr.warning(@json(session('warning'))); @endif
    @if(session('info'))    toastr.info(@json(session('info')));       @endif
    @if($errors->any())
        @foreach($errors->all() as $error)
            toastr.error(@json($error));
        @endforeach
    @endif
    </script>
    @stack('scripts')
</body>
</html>
