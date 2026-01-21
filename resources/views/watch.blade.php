@extends('layouts.app')

@section('title', "Nonton " . ($episode->title ?: $anime->title . " Episode " . $episode->episode_number))

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

        @if($episode->downloads->count() > 0)
            <div class="bg-dark-secondary p-6 rounded-2xl border border-white/5 shadow-xl">
                <h2 class="text-lg font-bold mb-4 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-accent-green" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                    Download Episode
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                    @foreach($episode->downloads as $download)
                        <a href="{{ $download->url }}" target="_blank" class="flex items-center justify-between p-3 bg-white/5 hover:bg-white/10 rounded-xl transition border border-white/5 group">
                            <div>
                                <span class="text-xs font-bold text-accent-green block uppercase">{{ $download->quality }}</span>
                                <span class="text-sm opacity-70">{{ $download->host }}</span>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 opacity-0 group-hover:opacity-100 transition text-accent-green" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
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