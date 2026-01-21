@extends('layouts.app')

@section('title', $anime->title)

@section('content')
    <div class="flex flex-col md:flex-row gap-8">
        <div class="w-full md:w-1/4">
            <div class="sticky top-24">
                <img src="{{ Str::startsWith($anime->poster_url, 'posters/') ? asset('storage/' . $anime->poster_url) : $anime->poster_url }}" class="w-full rounded-2xl shadow-2xl mb-6" alt="{{ $anime->title }}">
                <div class="bg-dark-secondary p-6 rounded-2xl border border-white/5">
                    <h3 class="text-lg font-bold mb-4 text-accent-green">Informasi</h3>
                    <ul class="space-y-3 text-sm">
                        <li><span class="opacity-50">Status:</span> {{ $anime->status }}</li>
                        <li><span class="opacity-50">Tipe:</span> {{ $anime->type }}</li>
                        <li><span class="opacity-50">Skor:</span> ⭐ {{ $anime->rating }}</li>
                        <li><span class="opacity-50">Rilis:</span> {{ $anime->release_date }}</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="w-full md:w-3/4">
            <h1 class="text-4xl font-bold mb-4">{{ $anime->title }}</h1>
            <div class="flex flex-wrap gap-2 mb-6">
                @foreach($anime->genres as $genre)
                    <a href="{{ route('genres.show', $genre->slug) }}" class="bg-white/10 hover:bg-white/20 px-3 py-1 rounded-full text-xs transition cursor-pointer">{{ $genre->name }}</a>
                @endforeach
            </div>

            <div class="bg-dark-secondary p-8 rounded-2xl border border-white/5 mb-8">
                <h2 class="text-xl font-bold mb-4 text-accent-green">Sinopsis</h2>
                <p class="text-white/80 leading-relaxed">{{ $anime->synopsis }}</p>
            </div>

            <div class="bg-dark-secondary rounded-2xl border border-white/5 overflow-hidden">
                <div class="p-6 border-b border-white/5 flex justify-between items-center">
                    <h2 class="text-xl font-bold">Daftar Episode</h2>
                    <span class="text-xs opacity-50">{{ $anime->episodes->count() }} Episode</span>
                </div>
                <div class="divide-y divide-white/5 max-h-[600px] overflow-y-auto">
                    @foreach($anime->episodes as $episode)
                        @php
                            $episodeSlugOnly = Str::afterLast($episode->slug, '/');
                        @endphp
                        <a href="{{ route('anime.watch', ['animeSlug' => $anime->slug, 'episodeSlug' => $episodeSlugOnly]) }}" class="flex items-center justify-between p-4 hover:bg-white/5 transition">
                            <div class="flex items-center gap-4">
                                <span class="w-8 h-8 flex items-center justify-center bg-dark-primary rounded-lg text-accent-green font-bold text-xs">{{ $episode->episode_number }}</span>
                                <span class="font-medium truncate">{{ $episode->title ?: "Episode " . $episode->episode_number }}</span>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 opacity-30" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection