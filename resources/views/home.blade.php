@extends('layouts.app')

@section('title', 'Homepage')

@section('content')
    <!-- Hero Section -->
    @if($trendingAnime)
    <section class="relative w-full rounded-2xl overflow-hidden shadow-2xl group">
        <div class="absolute inset-0 bg-cover bg-center transition-transform duration-700 group-hover:scale-105" 
             style='background-image: url("{{ Str::startsWith($trendingAnime->poster_url, 'posters/') ? asset('storage/' . $trendingAnime->poster_url) : $trendingAnime->poster_url }}");'>
        </div>
        <!-- Gradient Overlay -->
        <div class="absolute inset-0 bg-gradient-to-r from-background-dark/95 via-background-dark/60 to-transparent"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-background-dark via-transparent to-transparent"></div>
        <div class="relative z-10 flex flex-col justify-end min-h-[500px] md:min-h-[600px] p-6 md:p-12 lg:p-16 max-w-3xl">
            <span class="inline-flex items-center rounded-md bg-primary/20 px-2 py-1 text-xs font-medium text-primary ring-1 ring-inset ring-primary/30 w-fit mb-4">
                Trending #1
            </span>
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-black tracking-tight text-white mb-4 leading-tight">
                {{ $trendingAnime->title }}
            </h1>
            <p class="text-base md:text-lg text-gray-300 mb-8 line-clamp-3 max-w-2xl">
                {{ $trendingAnime->synopsis }}
            </p>
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('anime.detail', $trendingAnime->slug) }}" class="flex items-center gap-2 px-6 py-3.5 bg-primary hover:bg-primary/90 text-white rounded-xl font-bold transition-all transform hover:-translate-y-0.5 shadow-lg shadow-primary/25">
                    <span class="material-symbols-outlined filled">play_arrow</span>
                    Watch Now
                </a>
                <button class="flex items-center gap-2 px-6 py-3.5 bg-white/10 hover:bg-white/20 backdrop-blur-sm text-white rounded-xl font-bold transition-all border border-white/10">
                    <span class="material-symbols-outlined">add</span>
                    My List
                </button>
            </div>
        </div>
    </section>
    @endif

    <!-- Genre Filters -->
    <section class="w-full">
        <div class="flex gap-3 overflow-x-auto no-scrollbar pb-2">
            <a href="/series" class="flex-shrink-0 px-4 py-2 rounded-full bg-primary text-white text-sm font-semibold">All</a>
            @foreach($genres as $genre)
                <a href="{{ route('genres.show', $genre->slug) }}" class="flex-shrink-0 px-4 py-2 rounded-full bg-white dark:bg-card-dark border border-gray-200 dark:border-white/5 hover:border-primary dark:hover:border-primary text-gray-700 dark:text-gray-300 hover:text-primary transition-colors text-sm font-medium">
                    {{ $genre->name }}
                </a>
            @endforeach
        </div>
    </section>

    <!-- Currently Airing Section (Latest Episodes) -->
    <section class="flex flex-col gap-6">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold tracking-tight">Latest Episodes</h2>
            <a class="text-sm font-medium text-primary hover:text-primary/80 flex items-center gap-1" href="/series">
                View All <span class="material-symbols-outlined text-sm">arrow_forward_ios</span>
            </a>
        </div>
        <div class="w-full overflow-x-auto no-scrollbar pb-6 -mx-4 px-4 md:mx-0 md:px-0">
            <div class="flex gap-4 md:gap-6 min-w-max">
                @foreach($latestEpisodes as $episode)
                @php
                    $episodeSlugOnly = Str::afterLast($episode->slug, '/');
                @endphp
                <a href="{{ route('anime.watch', ['animeSlug' => $episode->anime->slug, 'episodeSlug' => $episodeSlugOnly]) }}" class="group relative w-44 md:w-56 cursor-pointer flex-shrink-0 flex flex-col gap-3">
                    <div class="relative aspect-[2/3] w-full overflow-hidden rounded-xl bg-card-dark shadow-lg">
                        <div class="absolute inset-0 bg-cover bg-center transition-transform duration-500 group-hover:scale-110" 
                             style='background-image: url("{{ Str::startsWith($episode->anime->poster_url, 'posters/') ? asset('storage/' . $episode->anime->poster_url) : $episode->anime->poster_url }}");'></div>
                        <div class="absolute inset-0 bg-black/20 group-hover:bg-black/40 transition-colors"></div>
                        <div class="absolute bottom-2 left-2 right-2">
                            <span class="inline-block rounded bg-primary px-2 py-0.5 text-xs font-bold text-white shadow-sm">Ep {{ $episode->episode_number }}</span>
                        </div>
                        <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                            <div class="size-12 rounded-full bg-primary/90 flex items-center justify-center text-white backdrop-blur-sm shadow-xl transform scale-75 group-hover:scale-100 transition-transform">
                                <span class="material-symbols-outlined filled">play_arrow</span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <h3 class="font-bold text-base line-clamp-1 group-hover:text-primary transition-colors">{{ $episode->anime->title }}</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $episode->anime->genres->take(2)->pluck('name')->join(', ') }}
                        </p>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Top Rated Grid (Latest Animes) -->
    <section class="flex flex-col gap-6">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold tracking-tight">Top Rated</h2>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4 md:gap-6">
            @foreach($topRatedAnimes as $anime)
            <a href="{{ route('anime.detail', $anime->slug) }}" class="group relative cursor-pointer flex flex-col gap-3">
                <div class="relative aspect-[2/3] w-full overflow-hidden rounded-xl bg-card-dark shadow-lg">
                    <div class="absolute inset-0 bg-cover bg-center transition-transform duration-500 group-hover:scale-110" 
                         style='background-image: url("{{ Str::startsWith($anime->poster_url, 'posters/') ? asset('storage/' . $anime->poster_url) : $anime->poster_url }}");'></div>
                    <!-- Rating Badge -->
                    <div class="absolute top-2 left-2">
                        <span class="flex items-center gap-1 rounded bg-black/60 backdrop-blur-md px-1.5 py-0.5 text-xs font-bold text-yellow-400 border border-white/10">
                            <span class="material-symbols-outlined text-[14px] filled">star</span>
                            {{ $anime->rating ?? '0.0' }}
                        </span>
                    </div>
                </div>
                <div>
                    <h3 class="font-bold text-sm md:text-base line-clamp-1 group-hover:text-primary transition-colors">{{ $anime->title }}</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ $anime->genres->take(2)->pluck('name')->join(', ') }}
                    </p>
                </div>
            </a>
            @endforeach
        </div>
    </section>
@endsection