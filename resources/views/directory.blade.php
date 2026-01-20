@extends('layouts.app')

@section('title', isset($query) ? "Hasil Pencarian: $query" : 'Daftar Anime')

@section('content')
    <h2 class="text-2xl font-bold mb-8 border-l-4 border-accent-green pl-4 text-accent-green uppercase tracking-wider">
        {{ isset($query) ? "Hasil Pencarian: \"$query\"" : "Daftar Semua Anime" }}
    </h2>
    
    @if($animes->isEmpty())
        <div class="bg-dark-secondary p-8 rounded-xl text-center">
            <p class="opacity-60">Tidak ada anime yang ditemukan.</p>
        </div>
    @endif

    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-6 mb-8">
        @foreach($animes as $anime)
            <a href="{{ route('anime.detail', $anime->slug) }}" class="group bg-dark-secondary rounded-xl overflow-hidden shadow-lg transition hover:ring-2 hover:ring-accent-green">
                <div class="aspect-[3/4] overflow-hidden">
                    <img src="{{ Str::startsWith($anime->poster_url, 'posters/') ? asset('storage/' . $anime->poster_url) : $anime->poster_url }}" class="h-full w-full object-cover transition duration-300 group-hover:scale-110" alt="{{ $anime->title }}">
                </div>
                <div class="p-3 text-center">
                    <h3 class="font-semibold text-xs line-clamp-2">{{ $anime->title }}</h3>
                </div>
            </a>
        @endforeach
    </div>

    <div class="mt-8">
        {{ $animes->appends(['q' => $query ?? ''])->links() }}
    </div>
@endsection