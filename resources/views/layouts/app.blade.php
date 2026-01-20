<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'AnimeStream') | Portal Anime Sub Indo</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Mulish:wght@200..1000&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="manifest" href="/manifest.json">
    <style>
        body { font-family: 'Mulish', sans-serif; }
    </style>
</head>
<body class="bg-dark-primary min-h-screen flex flex-col transition-colors duration-300">
    <nav class="sticky top-0 z-50 bg-dark-secondary py-3 shadow-lg transition-colors duration-300">
        <div class="container mx-auto px-4 flex justify-between items-center">
            <a href="/" class="text-2xl font-bold text-accent-green">AnimeStream</a>
            <div class="hidden md:flex space-x-6">
                <a href="/" class="hover:text-accent-green transition">Beranda</a>
                <a href="/series" class="hover:text-accent-green transition">Daftar Anime</a>
                <a href="#" class="hover:text-accent-green transition">Jadwal</a>
            </div>
            <div class="flex items-center space-x-4" x-data="{
                darkMode: document.documentElement.classList.contains('dark'),
                searchOpen: false,
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
                <form x-show="searchOpen"
                      x-transition:enter="transition ease-out duration-200"
                      x-transition:enter-start="opacity-0 scale-95"
                      x-transition:enter-end="opacity-100 scale-100"
                      action="{{ route('anime.search') }}" method="GET" class="relative">
                    <input type="text" name="q" placeholder="Cari anime..."
                           class="bg-dark-primary text-white border border-white/10 rounded-full px-4 py-1 focus:outline-none focus:border-accent-green w-48 md:w-64">
                </form>

                <button @click="toggleTheme" class="p-2 hover:bg-white/10 rounded-full transition text-accent-green">
                    <!-- Sun icon -->
                    <svg x-show="darkMode" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 18v1m9-9h1M4 12H3m15.364-6.364l.707-.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
                    </svg>
                    <!-- Moon icon -->
                    <svg x-show="!darkMode" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                </button>
                <button @click="searchOpen = !searchOpen" class="p-2 hover:bg-white/10 rounded-full transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </button>
            </div>
        </div>
    </nav>

    <main class="flex-grow container mx-auto px-4 py-8">
        @yield('content')
    </main>

    <footer class="bg-dark-secondary py-8 border-t border-white/10">
        <div class="container mx-auto px-4 text-center">
            <p class="text-sm opacity-60">&copy; {{ date('Y') }} AnimeStream. All rights reserved.</p>
        </div>
    </footer>

    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js');
            });
        }
    </script>
</body>
</html>