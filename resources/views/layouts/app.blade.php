<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="theme-color" content="#4725f4">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta name="description" content="@yield('meta_description', 'Nonton anime subtitle Indonesia terlengkap dan terupdate hanya di AnimeStream. Nikmati streaming anime favorit Anda dengan kualitas terbaik.')">
    <meta name="keywords" content="@yield('meta_keywords', 'nonton anime, streaming anime, anime sub indo, anime stream, anime terbaru')">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('title', 'AnimeStream') - Portal Anime">
    <meta property="og:description" content="@yield('meta_description', 'Nonton anime subtitle Indonesia terlengkap dan terupdate hanya di AnimeStream.')">
    <meta property="og:image" content="@yield('og_image', asset('icon.svg'))">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="@yield('title', 'AnimeStream') - Portal Anime">
    <meta property="twitter:description" content="@yield('meta_description', 'Nonton anime subtitle Indonesia terlengkap dan terupdate hanya di AnimeStream.')">
    <meta property="twitter:image" content="@yield('og_image', asset('icon.svg'))">

    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="{{ asset('icon.svg') }}">
    <title>@yield('title', 'AnimeStream') - Portal Anime</title>

    @yield('ld_json')
    <!-- Google Fonts -->
    <link rel="icon" type="image/svg+xml" href="/icon.svg">
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Spline+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
    <!-- Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <style>
        .episode-link:visited .episode-title {
            color: rgba(157, 157, 157, 0.16);
        }
        .text-accent-green {
            color: #32d583!important;
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#4725f4",
                        "background-light": "#f6f5f8",
                        "background-dark": "#131022",
                        "card-dark": "#1c1833",
                        "card-light": "#ffffff",
                    },
                    fontFamily: {
                        "display": ["Spline Sans", "sans-serif"]
                    },
                    borderRadius: {"DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px"},
                },
            },
        }
    </script>
    <style>
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-white font-display antialiased selection:bg-primary selection:text-white" x-data="{
    darkMode: document.documentElement.classList.contains('dark'),
    searchOpen: false,
    deferredPrompt: null,
    showInstallBtn: false,
    init() {
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            this.deferredPrompt = e;
            this.showInstallBtn = true;
        });
        window.addEventListener('appinstalled', () => {
            this.showInstallBtn = false;
            this.deferredPrompt = null;
        });
    },
    async installPWA() {
        if (!this.deferredPrompt) return;
        this.deferredPrompt.prompt();
        const { outcome } = await this.deferredPrompt.userChoice;
        if (outcome === 'accepted') {
            this.showInstallBtn = false;
        }
        this.deferredPrompt = null;
    },
    toggleTheme() {
        this.darkMode = !this.darkMode;
        if (this.darkMode) {
            document.documentElement.classList.add('dark');
            localStorage.setItem('theme', 'dark');
        } else {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('theme', 'light');
        }
    }
}">
<div class="relative min-h-screen flex flex-col overflow-x-hidden">
    <!-- Navigation Bar -->
    <header class="sticky top-0 z-50 w-full border-b border-gray-200 dark:border-white/10 bg-background-light/80 dark:bg-background-dark/80 backdrop-blur-md">
        <div class="mx-auto max-w-[1440px] px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between gap-4">
                <!-- Logo & Links -->
                <div class="flex items-center gap-8">
                    <a class="flex items-center gap-2 group" href="/">
                        <div class="flex items-center justify-center size-8 rounded bg-primary text-white">
                            <span class="material-symbols-outlined">play_arrow</span>
                        </div>
                        <span class="text-lg font-bold tracking-tight">AnimeStream</span>
                    </a>
                    <nav class="hidden md:flex items-center gap-6">
                        <a class="text-sm font-medium {{ request()->routeIs('home') ? 'text-primary' : 'text-gray-500 dark:text-gray-400 hover:text-primary dark:hover:text-white' }} transition-colors" href="{{ route('home') }}">Home</a>
                        <a class="text-sm font-medium {{ request()->routeIs('directory') ? 'text-primary' : 'text-gray-500 dark:text-gray-400 hover:text-primary dark:hover:text-white' }} transition-colors" href="{{ route('directory') }}">Series</a>
                        <a class="text-sm font-medium {{ request()->routeIs('popular') ? 'text-primary' : 'text-gray-500 dark:text-gray-400 hover:text-primary dark:hover:text-white' }} transition-colors" href="{{ route('popular') }}">Popular</a>
                        <a class="text-sm font-medium {{ request()->routeIs('new-releases') ? 'text-primary' : 'text-gray-500 dark:text-gray-400 hover:text-primary dark:hover:text-white' }} transition-colors" href="{{ route('new-releases') }}">New Releases</a>
                        <a class="text-sm font-medium {{ request()->routeIs('genres.*') ? 'text-primary' : 'text-gray-500 dark:text-gray-400 hover:text-primary dark:hover:text-white' }} transition-colors" href="{{ route('genres.index') }}">Genre</a>
                    </nav>
                </div>
                <!-- Search & Actions -->
                <div class="flex flex-1 items-center justify-end gap-4">
                    <!-- Search Bar -->
                    <div class="hidden sm:flex max-w-xs w-full">
                        <form action="{{ route('anime.search') }}" method="GET" class="relative w-full text-gray-500 focus-within:text-primary">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <span class="material-symbols-outlined text-[20px]">search</span>
                            </div>
                            <input name="q" class="block w-full rounded-lg border-none bg-gray-100 dark:bg-[#292249] py-2 pl-10 pr-4 text-sm text-gray-900 dark:text-white placeholder-gray-500 focus:ring-2 focus:ring-primary" placeholder="Search anime..." type="text" value="{{ request('q') }}"/>
                        </form>
                    </div>
                    <!-- Mobile Search Icon -->
                    <button class="sm:hidden p-2 text-gray-500 hover:text-primary" @click="searchOpen = !searchOpen">
                        <span class="material-symbols-outlined">search</span>
                    </button>
                    <div class="flex items-center gap-2 border-l border-gray-200 dark:border-white/10 pl-4">
                        <!-- Theme Toggle -->
                        <button @click="toggleTheme" class="p-2 rounded-lg hover:bg-gray-200 dark:hover:bg-[#292249] transition-colors text-gray-600 dark:text-gray-300">
                            <span class="material-symbols-outlined" x-text="darkMode ? 'light_mode' : 'dark_mode'">dark_mode</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="flex-grow w-full max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-10">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="mt-20 border-t border-gray-200 dark:border-white/10 bg-white dark:bg-[#18152c]">
        <div class="mx-auto max-w-[1440px] px-4 py-12 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="col-span-1 md:col-span-1">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="flex items-center justify-center size-8 rounded bg-primary text-white">
                            <span class="material-symbols-outlined">play_arrow</span>
                        </div>
                        <span class="text-lg font-bold tracking-tight">AnimeStream</span>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">The best place to stream your favorite anime anytime, anywhere. Experience the magic of animation.</p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold mb-4">Navigation</h3>
                    <ul class="space-y-2 text-sm text-gray-500 dark:text-gray-400">
                        <li><a class="{{ request()->routeIs('home') ? 'text-primary' : 'hover:text-primary' }} transition-colors" href="{{ route('home') }}">Home</a></li>
                        <li><a class="{{ request()->routeIs('directory') ? 'text-primary' : 'hover:text-primary' }} transition-colors" href="{{ route('directory') }}">Series</a></li>
                        <li><a class="{{ request()->routeIs('popular') ? 'text-primary' : 'hover:text-primary' }} transition-colors" href="{{ route('popular') }}">Popular</a></li>
                        <li><a class="{{ request()->routeIs('new-releases') ? 'text-primary' : 'hover:text-primary' }} transition-colors" href="{{ route('new-releases') }}">New Releases</a></li>
                        <li><a class="{{ request()->routeIs('genres.*') ? 'text-primary' : 'hover:text-primary' }} transition-colors" href="{{ route('genres.index') }}">Genre</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-sm font-semibold mb-4">Support</h3>
                    <ul class="space-y-2 text-sm text-gray-500 dark:text-gray-400">
                        <li><a class="hover:text-primary transition-colors" href="#">Help Center</a></li>
                        <li><a class="hover:text-primary transition-colors" href="#">Terms of Service</a></li>
                        <li><a class="hover:text-primary transition-colors" href="#">Privacy Policy</a></li>
                        <li><a class="hover:text-primary transition-colors" href="#">Contact Us</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-sm font-semibold mb-4">Get the App</h3>
                    <div class="flex flex-col gap-3">
                        <button x-show="showInstallBtn" @click="installPWA" class="flex items-center gap-3 bg-primary text-white px-4 py-2 rounded-lg hover:opacity-90 transition-opacity shadow-lg shadow-primary/20">
                            <span class="material-symbols-outlined">download</span>
                            <div class="text-left">
                                <div class="text-[10px] uppercase leading-none">Install</div>
                                <div class="text-sm font-bold leading-none">AnimeStream</div>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
            <div class="mt-12 pt-8 border-t border-gray-200 dark:border-white/5 text-center text-sm text-gray-500 dark:text-gray-400">
                <p>© {{ date('Y') }} AnimeStream. All rights reserved.</p>
            </div>
        </div>
    </footer>
</div>
</body>
</html>