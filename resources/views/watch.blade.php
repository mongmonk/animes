@extends('layouts.app')

@section('title', "Nonton " . $anime->title . " Episode " . $episode->episode_number . " Subtitle Indonesia")
@section('meta_description', "Nonton streaming anime " . $anime->title . " Episode " . $episode->episode_number . " subtitle Indonesia. Nikmati kualitas terbaik secara gratis di AnimeStream.")
@section('meta_keywords', "nonton " . $anime->title . " episode " . $episode->episode_number . ", streaming " . $anime->title . " ep " . $episode->episode_number . ", " . $anime->title . " sub indo")
@section('og_type', 'video.episode')
@section('og_image', Str::startsWith($anime->poster_url, 'posters/') ? asset('storage/' . $anime->poster_url) : $anime->poster_url)

@section('ld_json')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "Episode",
  "name": "{{ $anime->title }} Episode {{ $episode->episode_number }}",
  "episodeNumber": "{{ $episode->episode_number }}",
  "partOfTVSeries": {
    "@@type": "TVSeries",
    "name": "{{ $anime->title }}",
    "url": "{{ route('anime.detail', $anime->slug) }}"
  },
  "image": "{{ Str::startsWith($anime->poster_url, 'posters/') ? asset('storage/' . $anime->poster_url) : $anime->poster_url }}",
  "description": "Nonton {{ $anime->title }} Episode {{ $episode->episode_number }} Subtitle Indonesia."
}
</script>
@endsection

@section('content')
    <div class="max-w-5xl mx-auto">
        <nav class="mb-6 flex items-center text-sm opacity-60">
            <a href="/" class="hover:text-accent-green transition">Beranda</a>
            <span class="mx-2">/</span>
            <a href="{{ route('anime.detail', $anime->slug) }}" class="hover:text-accent-green transition">{{ $anime->title }}</a>
            <span class="mx-2">/</span>
            <span class="text-white">Episode {{ $episode->episode_number }}</span>
        </nav>

        <div class="bg-black aspect-video rounded-2xl overflow-hidden shadow-2xl mb-4 ring-1 ring-white/10">
            @if($episode->video_embed_url)
                <iframe id="video-player" src="{{ $episode->video_embed_url }}" class="w-full h-full" frameborder="0" allowfullscreen></iframe>
            @else
                <div class="w-full h-full flex items-center justify-center text-white/50 flex-col">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mb-4 opacity-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <p class="text-lg text-center px-4">Video tidak tersedia atau link embed belum ditemukan.</p>
                </div>
            @endif
        </div>

        @if($episode->videos->count() > 0)
            <div class="flex flex-wrap gap-2 mb-8">
                @foreach($episode->videos as $video)
                    <button
                        onclick="changeServer('{{ $video->url }}', this)"
                        class="server-btn px-4 py-2 rounded-lg text-sm font-medium transition {{ $loop->first ? 'bg-accent-green text-dark-primary' : 'bg-white/5 hover:bg-white/10 text-white border border-white/5' }}"
                    >
                        {{ $video->source ?: 'Server ' . ($loop->iteration) }}
                    </button>
                @endforeach
            </div>
        @endif

        <div class="flex flex-col md:flex-row justify-between items-center gap-6 bg-dark-secondary p-6 rounded-2xl border border-white/5 mb-8 shadow-xl">
            <div class="flex-1 text-center md:text-left">
                <h1 class="text-xl font-bold line-clamp-1">{{ $anime->title }}</h1>
                <p class="text-accent-green font-medium">Episode {{ $episode->episode_number }}</p>
            </div>
            <div class="flex gap-4">
                @if($prevEpisode)
                    <a href="{{ route('anime.watch', ['animeSlug' => $anime->slug, 'episodeSlug' => basename($prevEpisode->slug)]) }}" class="bg-white/10 hover:bg-white/20 px-6 py-2 rounded-xl transition flex items-center gap-2 border border-white/10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        Prev
                    </a>
                @endif
                
                @if($nextEpisode)
                    <a href="{{ route('anime.watch', ['animeSlug' => $anime->slug, 'episodeSlug' => basename($nextEpisode->slug)]) }}" class="bg-accent-green text-dark-primary font-bold px-6 py-2 rounded-xl transition hover:opacity-90 flex items-center gap-2 shadow-lg shadow-accent-green/20">
                        Next
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </a>
                @endif
            </div>
        </div>

        <div class="bg-dark-secondary p-6 rounded-2xl border border-white/5 shadow-xl">
            <h2 class="text-lg font-bold mb-4 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-accent-green" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
                Download Episode
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                @php
                    $downloadConfigs = [
                        ['label' => '480p'],
                        ['label' => '720p'],
                        ['label' => '1080p'],
                    ];
                @endphp

                @foreach($downloadConfigs as $index => $config)
                    @php
                        $download = $episode->downloads->get($index);
                        $url = $download ? $download->url : 'https://indanime.my.id';
                    @endphp
                    <a href="{{ $url }}" target="_blank" class="flex items-center justify-between p-3 bg-white/5 hover:bg-white/10 rounded-xl transition border border-white/5 group">
                        <div>
                            <span class="text-xs font-bold text-accent-green block uppercase">{{ $config['label'] }}</span>
                            <span class="text-sm opacity-70"> Download Dari Server {{ $index + 1 }}</span>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 opacity-0 group-hover:opacity-100 transition text-accent-green" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Anime Info Section --}}
        <div class="mt-12 bg-dark-secondary rounded-2xl border border-white/5 overflow-hidden shadow-xl">
            <div class="flex flex-col md:flex-row">
                <div class="w-full md:w-64 shrink-0">
                    <img src="{{ Str::startsWith($anime->poster_url, 'posters/') ? asset('storage/' . $anime->poster_url) : $anime->poster_url }}" alt="{{ $anime->title }}" class="w-full h-full object-cover aspect-[3/4]">
                </div>
                <div class="p-6 md:p-8 flex-1">
                    <div class="flex flex-wrap gap-2 mb-4">
                        @foreach($anime->genres as $genre)
                            @if($genre->slug)
                                <a href="{{ route('genres.show', ['slug' => $genre->slug]) }}" class="text-xs font-bold px-3 py-1 bg-accent-green/10 text-accent-green rounded-full border border-accent-green/20 hover:bg-accent-green hover:text-dark-primary transition">
                                    {{ $genre->name }}
                                </a>
                            @else
                                <span class="text-xs font-bold px-3 py-1 bg-white/5 text-white/50 rounded-full border border-white/10">
                                    {{ $genre->name }}
                                </span>
                            @endif
                        @endforeach
                    </div>
                    <h2 class="text-2xl font-bold mb-4">{{ $anime->title }}</h2>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-y-4 gap-x-6 text-sm mb-6">
                        <div>
                            <span class="block opacity-50 text-xs uppercase mb-1">Status</span>
                            <span class="font-medium">{{ $anime->status }}</span>
                        </div>
                        <div>
                            <span class="block opacity-50 text-xs uppercase mb-1">Type</span>
                            <span class="font-medium">{{ $anime->type }}</span>
                        </div>
                        <div>
                            <span class="block opacity-50 text-xs uppercase mb-1">Rating</span>
                            <span class="font-medium text-accent-green">★ {{ $anime->rating }}</span>
                        </div>
                        <div>
                            <span class="block opacity-50 text-xs uppercase mb-1">Total Episode</span>
                            <span class="font-medium">{{ $anime->total_episodes }}</span>
                        </div>
                        <div>
                            <span class="block opacity-50 text-xs uppercase mb-1">Studio</span>
                            <span class="font-medium">{{ $anime->studio }}</span>
                        </div>
                        <div>
                            <span class="block opacity-50 text-xs uppercase mb-1">Season</span>
                            <span class="font-medium">{{ $anime->season }} {{ $anime->year }}</span>
                        </div>
                    </div>
                    <div class="prose prose-invert prose-sm max-w-none line-clamp-4 opacity-70">
                        {!! $anime->synopsis !!}
                    </div>
                    <a href="{{ route('anime.detail', $anime->slug) }}" class="inline-block mt-6 text-accent-green font-bold text-sm hover:underline">
                        Lihat Detail Selengkapnya →
                    </a>
                </div>
            </div>
        </div>

        {{-- Episode List Section --}}
        <div class="mt-8 bg-dark-secondary rounded-2xl border border-white/5 overflow-hidden shadow-xl">
            <div class="p-6 border-b border-white/5 flex items-center justify-between">
                <h3 class="text-lg font-bold flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-accent-green" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" />
                    </svg>
                    Daftar Episode
                </h3>
                <span class="text-xs opacity-50">{{ $anime->episodes->count() }} Episode</span>
            </div>
            <div class="max-h-[450px] overflow-y-auto custom-scrollbar">
                <div class="flex flex-col">
                    @foreach($anime->episodes->sortByDesc('episode_number') as $ep)
                        <a href="{{ route('anime.watch', ['animeSlug' => $anime->slug, 'episodeSlug' => basename($ep->slug)]) }}"
                           class="episode-link flex items-center justify-between p-4 border-b border-white/5 transition-all duration-200 group {{ $ep->id == $episode->id ? 'bg-accent-green/10' : 'hover:bg-white/5' }}">
                            <div class="flex items-center gap-4">
                                <div class="episode-number w-10 h-10 flex items-center justify-center rounded-lg text-xs font-bold {{ $ep->id == $episode->id ? 'bg-accent-green text-dark-primary shadow-lg shadow-accent-green/20' : 'bg-white/5 text-accent-green' }}">
                                    {{ $ep->episode_number }}
                                </div>
                                <div class="flex flex-col">
                                    <span class="episode-title text-sm font-medium {{ $ep->id == $episode->id ? 'text-accent-green' : 'text-white/90 group-hover:text-white' }}">
                                        {{ $anime->title }} Episode {{ $ep->episode_number }}
                                    </span>
                                </div>
                            </div>
                            <div class="opacity-30 group-hover:opacity-100 transition-opacity">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 {{ $ep->id == $episode->id ? 'text-accent-green' : 'text-white' }}" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Related Anime Section --}}
        @if($relatedAnimes->isNotEmpty())
            <div class="mt-12 mb-12">
                <h3 class="text-xl font-bold mb-6 flex items-center gap-2">
                    <span class="w-2 h-6 bg-accent-green rounded-full"></span>
                    Rekomendasi Anime Terkait
                </h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-4">
                    @foreach($relatedAnimes as $related)
                        <a href="{{ route('anime.detail', $related->slug) }}" class="group block">
                            <div class="relative aspect-[3/4] rounded-xl overflow-hidden mb-2 ring-1 ring-white/10 group-hover:ring-accent-green/50 transition">
                                <img src="{{ Str::startsWith($related->poster_url, 'posters/') ? asset('storage/' . $related->poster_url) : $related->poster_url }}" alt="{{ $related->title }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-3">
                                    <span class="text-[10px] font-bold bg-accent-green text-dark-primary px-2 py-0.5 rounded">
                                        {{ $related->type }}
                                    </span>
                                </div>
                            </div>
                            <h4 class="text-sm font-bold line-clamp-2 group-hover:text-accent-green transition">{{ $related->title }}</h4>
                            <div class="flex items-center gap-1 mt-1 opacity-60 text-[10px]">
                                <span>{{ $related->status }}</span>
                                <span>•</span>
                                <span class="text-accent-green">★ {{ $related->rating }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <script>
        function changeServer(url, btn) {
            // Update iframe src
            document.getElementById('video-player').src = url;
            
            // Update button styles
            document.querySelectorAll('.server-btn').forEach(el => {
                el.classList.remove('bg-accent-green', 'text-dark-primary');
                el.classList.add('bg-white/5', 'hover:bg-white/10', 'text-white', 'border', 'border-white/5');
            });
            
            btn.classList.remove('bg-white/5', 'hover:bg-white/10', 'text-white', 'border', 'border-white/5');
            btn.classList.add('bg-accent-green', 'text-dark-primary');
        }
    </script>
@endsection