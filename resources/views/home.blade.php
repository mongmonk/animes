@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <section class="mb-12">
        <h2 class="text-2xl font-bold mb-6 border-l-4 border-accent-green pl-4 text-accent-green uppercase tracking-wider">Anime Terbaru</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-6">
            @foreach($latestAnimes as $anime)
                <a href="{{ route('anime.detail', $anime->slug) }}" class="group">
                    <div class="relative aspect-[3/4] overflow-hidden rounded-xl bg-dark-secondary mb-3 shadow-lg transition-transform duration-300 group-hover:scale-105">
                        <img src="{{ Str::startsWith($anime->poster_url, 'posters/') ? asset('storage/' . $anime->poster_url) : $anime->poster_url }}" class="h-full w-full object-cover" alt="{{ $anime->title }}">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-4">
                            <span class="bg-accent-green text-dark-primary text-xs font-bold px-2 py-1 rounded">DETAIL</span>
                        </div>
                    </div>
                    <h3 class="font-semibold text-sm line-clamp-2 group-hover:text-accent-green transition">{{ $anime->title }}</h3>
                </a>
            @endforeach
        </div>
    </section>

    <section>
        <h2 class="text-2xl font-bold mb-6 border-l-4 border-accent-green pl-4 text-accent-green uppercase tracking-wider">Episode Terbaru</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($latestEpisodes as $episode)
                @php
                    $episodeSlugOnly = Str::afterLast($episode->slug, '/');
                @endphp
                <a href="{{ route('anime.watch', ['animeSlug' => $episode->anime->slug, 'episodeSlug' => $episodeSlugOnly]) }}" class="group bg-dark-secondary rounded-xl overflow-hidden flex shadow-md hover:ring-2 hover:ring-accent-green transition">
                    <div class="w-1/3 aspect-[3/4] flex-shrink-0">
                        <img src="{{ Str::startsWith($episode->anime->poster_url, 'posters/') ? asset('storage/' . $episode->anime->poster_url) : $episode->anime->poster_url }}" class="h-full w-full object-cover" alt="{{ $episode->anime->title }}">
                    </div>
                    <div class="p-4 flex flex-col justify-center overflow-hidden">
                        <h3 class="font-bold text-sm truncate">{{ $episode->anime->title }}</h3>
                        <p class="text-accent-green text-xs mt-1">Episode {{ $episode->episode_number }}</p>
                        <span class="text-[10px] opacity-50 mt-2">{{ $episode->created_at->diffForHumans() }}</span>
                    </div>
                </a>
            @endforeach
        </div>
    </section>
@endsection