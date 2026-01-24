@extends('layouts.app')

@section('title', 'Daftar Genre Anime Terlengkap')
@section('meta_description', 'Cari anime favoritmu berdasarkan genre. Mulai dari Action, Adventure, Comedy, hingga Romance semuanya tersedia di AnimeStream.')

@section('content')
<div class="container mx-auto px-4 py-10 max-w-7xl">
    <!-- Breadcrumb & Title -->
    <div class="mb-10">
        <nav class="flex text-gray-500 text-xs uppercase tracking-widest mb-4" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('home') }}" class="hover:text-primary transition-colors">Beranda</a>
                </li>
                <li>
                    <div class="flex items-center">
                        <span class="mx-2">/</span>
                        <span class="text-gray-500 dark:text-gray-300">Genre</span>
                    </div>
                </li>
            </ol>
        </nav>
        <h2 class="text-4xl font-black text-slate-900 dark:text-white uppercase tracking-tighter">
            Daftar Genre
            <span class="block h-1.5 w-24 bg-primary mt-4 rounded-full"></span>
        </h2>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-6">
        @foreach($allGenres as $genre)
            <a href="{{ route('genres.show', $genre->slug) }}" class="group relative bg-dark-secondary/40 backdrop-blur-xl p-6 rounded-2xl border border-white/5 hover:border-primary/50 transition-all hover:-translate-y-1 shadow-xl">
                <div class="flex flex-col items-center gap-3">
                    <span class="text-sm font-bold text-gray-400 group-hover:text-white transition-colors text-center">{{ $genre->name }}</span>
                    <div class="h-1 w-0 bg-primary group-hover:w-full transition-all duration-300 rounded-full"></div>
                </div>
            </a>
        @endforeach
    </div>
</div>
@endsection