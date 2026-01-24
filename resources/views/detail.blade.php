@extends('layouts.app')

@section('title', 'Nonton ' . $anime->title . ' Subtitle Indonesia')
@section('meta_description', 'Nonton anime ' . $anime->title . ' subtitle Indonesia. ' . Str::limit($anime->synopsis, 150))
@section('meta_keywords', $anime->title . ', nonton ' . $anime->title . ', ' . $anime->title . ' sub indo, streaming ' . $anime->title . ', ' . $anime->genres->pluck('name')->join(', '))
@section('og_type', 'video.tv_show')
@section('og_image', Str::startsWith($anime->poster_url, 'posters/') ? asset('storage/' . $anime->poster_url) : $anime->poster_url)

@section('ld_json')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "Movie",
  "name": "{{ $anime->title }}",
  "description": "{{ Str::limit($anime->synopsis, 200) }}",
  "image": "{{ Str::startsWith($anime->poster_url, 'posters/') ? asset('storage/' . $anime->poster_url) : $anime->poster_url }}",
  "aggregateRating": {
    "@@type": "AggregateRating",
    "ratingValue": "{{ $anime->rating ?? '0' }}",
    "bestRating": "10",
    "worstRating": "1",
    "ratingCount": "100"
  },
  "genre": [
    @foreach($anime->genres as $genre)
      "{{ $genre->name }}"{{ !$loop->last ? ',' : '' }}
    @endforeach
  ]
}
</script>
@endsection

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
                    @if($genre->slug)
                        <a href="{{ route('genres.show', $genre->slug) }}" class="bg-white/10 hover:bg-white/20 px-3 py-1 rounded-full text-xs transition cursor-pointer">{{ $genre->name }}</a>
                    @else
                        <span class="bg-white/10 px-3 py-1 rounded-full text-xs opacity-50">{{ $genre->name }}</span>
                    @endif
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
                        <a href="{{ route('anime.watch', ['animeSlug' => $anime->slug, 'episodeSlug' => $episodeSlugOnly]) }}" class="episode-link flex items-center justify-between p-4 hover:bg-white/5 transition">
                            <div class="flex items-center gap-4">
                                <span class="episode-number w-8 h-8 flex items-center justify-center bg-dark-primary rounded-lg text-accent-green font-bold text-xs">{{ $episode->episode_number }}</span>
                                <span class="episode-title font-medium truncate">{{ $anime->title }} Episode {{ $episode->episode_number }}</span>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 opacity-30" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    @endforeach
                </div>
            </div>

            @if($relatedAnimes->isNotEmpty())
                <div class="mt-12">
                    <h2 class="text-2xl font-bold mb-6 flex items-center gap-2">
                        <span class="w-2 h-8 bg-accent-green rounded-full"></span>
                        Anime Terkait
                    </h2>
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                        @foreach($relatedAnimes as $related)
                            <a href="{{ route('anime.detail', $related->slug) }}" class="group">
                                <div class="relative aspect-[3/4] rounded-xl overflow-hidden mb-3">
                                    <img src="{{ Str::startsWith($related->poster_url, 'posters/') ? asset('storage/' . $related->poster_url) : $related->poster_url }}"
                                         class="w-full h-full object-cover transition duration-500 group-hover:scale-110"
                                         alt="{{ $related->title }}">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-3">
                                        <span class="text-white text-xs font-medium line-clamp-2">{{ $related->title }}</span>
                                    </div>
                                    <div class="absolute top-2 right-2 bg-black/60 backdrop-blur-md px-2 py-1 rounded-lg text-[10px] font-bold text-accent-green border border-white/10">
                                        ⭐ {{ $related->rating }}
                                    </div>
                                </div>
                                <h3 class="text-sm font-semibold line-clamp-1 group-hover:text-accent-green transition">{{ $related->title }}</h3>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-[10px] opacity-50">{{ $related->type }}</span>
                                    <span class="text-[10px] opacity-50">•</span>
                                    <span class="text-[10px] opacity-50">{{ $related->status }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection