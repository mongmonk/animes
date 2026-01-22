@extends('layouts.app')

@section('title', $title ?? (isset($query) ? "Hasil Pencarian: $query" : 'Daftar Anime'))

@section('content')
<div class="container mx-auto px-4 py-10 max-w-7xl">
    <!-- Breadcrumb & Title -->
    <div class="mb-10">
        <nav class="flex text-slate-500 dark:text-gray-400 text-xs uppercase tracking-widest mb-4" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('home') }}" class="hover:text-accent-green transition-colors">Beranda</a>
                </li>
                <li>
                    <div class="flex items-center">
                        <span class="mx-2">/</span>
                        <span class="text-slate-600 dark:text-gray-300 font-bold">{{ $title ?? 'Daftar Anime' }}</span>
                    </div>
                </li>
            </ol>
        </nav>
        <h2 class="text-4xl font-black text-slate-900 dark:text-white uppercase tracking-tighter">
            {{ $title ?? (isset($query) ? "Hasil Pencarian" : "Daftar Semua Anime") }}
            <span class="block h-1.5 w-24 bg-accent-green mt-4 rounded-full"></span>
        </h2>
    </div>

    @if(!isset($title))
    <!-- Modern Filter Card -->
    <div class="bg-white dark:bg-background-dark/80 backdrop-blur-xl rounded-3xl mb-12 shadow-2xl border border-slate-200 dark:border-white/5 overflow-hidden mt-10">
        <form action="{{ route('directory') }}" method="GET">
            <input type="hidden" name="mode" value="{{ $mode ?? 'image' }}">
            
            <!-- Search Bar Section -->
            <div class="p-8 border-b border-slate-100 dark:border-white/5 bg-slate-50/50 dark:bg-white/5">
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                        <svg class="h-6 w-6 text-slate-400 dark:text-gray-500 group-focus-within:text-accent-green transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" name="q" value="{{ $query }}" placeholder="Cari judul anime kesukaanmu..."
                           class="w-full bg-slate-100 dark:bg-dark-primary/50 border border-slate-200 dark:border-white/10 rounded-2xl pl-14 pr-6 py-5 focus:outline-none focus:ring-2 focus:ring-accent-green/50 focus:border-accent-green transition-all text-xl text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-gray-600 shadow-inner">
                </div>
            </div>

            <div class="p-8 space-y-10">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                    <!-- Status Selection -->
                    <div class="space-y-4">
                        <label class="text-xs font-black uppercase tracking-[0.2em] text-accent-green dark:text-accent-green/70">Status Series</label>
                        <div class="flex flex-wrap gap-3">
                            @php
                                $statusOptions = [
                                    'All' => 'All',
                                    'Berlangsung' => 'Currently Airing',
                                    'Selesai' => 'Finished Airing'
                                ];
                            @endphp
                            @foreach($statusOptions as $val => $label)
                                <label class="cursor-pointer group">
                                    <input type="radio" name="status" value="{{ $val }}" {{ ($status ?? 'All') == $val ? 'checked' : '' }} class="hidden peer">
                                    <div class="px-6 py-3 rounded-xl bg-slate-100 dark:bg-dark-primary border border-slate-200 dark:border-white/5 text-sm font-bold text-slate-600 dark:text-gray-400 peer-checked:bg-[#00aeef] peer-checked:text-white peer-checked:border-[#00aeef] group-hover:border-[#00aeef]/30 transition-all shadow-lg">
                                        {{ $label }}
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Type Selection -->
                    <div class="space-y-4">
                        <label class="text-xs font-black uppercase tracking-[0.2em] text-accent-green dark:text-accent-green/70">Tipe Konten</label>
                        <div class="flex flex-wrap gap-3">
                            @php
                                $typeOptions = [
                                    'All' => 'All',
                                    'TV' => 'Serial TV',
                                    'OVA' => 'OVA',
                                    'ONA' => 'ONA',
                                    'Special' => 'Special',
                                    'Movie' => 'BD'
                                ];
                            @endphp
                            @foreach($typeOptions as $label => $val)
                                <label class="cursor-pointer group">
                                    <input type="radio" name="type" value="{{ $val }}" {{ ($type ?? 'All') == $val ? 'checked' : '' }} class="hidden peer">
                                    <div class="px-6 py-3 rounded-xl bg-slate-100 dark:bg-dark-primary border border-slate-200 dark:border-white/5 text-sm font-bold text-slate-600 dark:text-gray-400 peer-checked:bg-[#00aeef] peer-checked:text-white peer-checked:border-[#00aeef] group-hover:border-[#00aeef]/30 transition-all shadow-lg">
                                        {{ $label }}
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Sort Options -->
                <div class="space-y-4">
                    <label class="text-xs font-black uppercase tracking-[0.2em] text-accent-green dark:text-accent-green/70">Urutkan Berdasarkan</label>
                    <div class="flex flex-wrap gap-3">
                        @php
                            $sortOptions = [
                                'title-asc' => 'A - Z',
                                'title-desc' => 'Z - A',
                                'latest-updated' => 'Update Terbaru',
                                'latest-added' => 'Baru Ditambahkan',
                                'popular' => 'Paling Populer'
                            ];
                        @endphp
                        @foreach($sortOptions as $val => $label)
                            <label class="cursor-pointer group">
                                <input type="radio" name="order" value="{{ $val }}" {{ ($order ?? 'title-asc') == $val ? 'checked' : '' }} class="hidden peer">
                                <div class="px-5 py-2.5 rounded-lg bg-slate-100 dark:bg-dark-primary border border-slate-200 dark:border-white/5 text-[11px] font-black uppercase tracking-wider text-slate-500 dark:text-gray-500 peer-checked:bg-[#00aeef] peer-checked:text-white peer-checked:border-[#00aeef] group-hover:border-[#00aeef]/20 transition-all">
                                    {{ $label }}
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Genre Grid -->
                <div class="space-y-4">
                    <label class="text-xs font-black uppercase tracking-[0.2em] text-accent-green dark:text-accent-green/70">Filter Genre</label>
                    <div class="bg-slate-100 dark:bg-black/20 rounded-2xl p-6 border border-slate-200 dark:border-white/5 shadow-inner">
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-x-6 gap-y-4">
                            @foreach($allGenres as $g)
                                <label class="flex items-center group cursor-pointer">
                                    <div class="relative flex items-center">
                                        <input type="checkbox" name="genres[]" value="{{ $g->slug }}" {{ in_array($g->slug, $genres ?? []) ? 'checked' : '' }}
                                               class="w-5 h-5 appearance-none border-2 border-slate-300 dark:border-white/10 rounded-lg bg-white dark:bg-dark-primary checked:bg-[#00aeef] checked:border-[#00aeef] transition-all cursor-pointer peer">
                                        <svg class="absolute inset-0 m-auto w-3 h-3 text-white scale-0 peer-checked:scale-100 transition-transform pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <span class="ml-5 text-xs font-bold text-slate-500 dark:text-gray-500 group-hover:text-slate-900 dark:group-hover:text-white transition-colors">{{ $g->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Action Button -->
                <div class="pt-4">
                    <button type="submit" class="w-full bg-[#00aeef] hover:bg-[#00aeef]/90 text-white font-black py-5 rounded-2xl transition-all uppercase tracking-[0.3em] text-sm shadow-xl shadow-[#00aeef]/20 hover:-translate-y-0.5 active:translate-y-0 border border-white/10">
                        Terapkan Filter
                    </button>
                </div>
            </div>
        </form>
    </div>
    @endif

    @if(!isset($title))
    <!-- View Mode Switcher -->
    <div class="flex justify-center mb-12">
        <div class="inline-flex p-1.5 bg-white dark:bg-dark-secondary/60 rounded-2xl border border-slate-200 dark:border-white/5 shadow-2xl">
            <a href="{{ request()->fullUrlWithQuery(['mode' => 'image']) }}"
               class="flex items-center space-x-2 px-8 py-3 rounded-xl text-xs font-black uppercase tracking-widest transition-all {{ ($mode ?? 'image') === 'image' ? 'bg-accent-green text-dark-primary shadow-lg' : 'text-slate-400 dark:text-gray-500 hover:text-slate-900 dark:hover:text-white' }}">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                <span>Grid Visual</span>
            </a>
            <a href="{{ request()->fullUrlWithQuery(['mode' => 'text']) }}"
               class="flex items-center space-x-2 px-8 py-3 rounded-xl text-xs font-black uppercase tracking-widest transition-all {{ ($mode ?? 'image') === 'text' ? 'bg-accent-green text-dark-primary shadow-lg' : 'text-slate-400 dark:text-gray-500 hover:text-slate-900 dark:hover:text-white' }}">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                <span>Daftar Teks</span>
            </a>
        </div>
    </div>
    @endif

    @if(($mode ?? 'image') === 'text')
        <!-- Alphabet Quick Nav -->
        <div class="flex flex-wrap justify-center gap-3 mb-12 bg-slate-100 dark:bg-background-dark/80 p-6 rounded-3xl border border-slate-200 dark:border-white/5">
            @foreach(range('A', 'Z') as $char)
                <a href="#char-{{ $char }}" class="w-11 h-11 flex items-center justify-center bg-white dark:bg-dark-primary rounded-xl hover:bg-accent-green hover:text-dark-primary transition-all font-black border border-slate-200 dark:border-white/5 text-slate-500 dark:text-gray-500 shadow-lg hover:-translate-y-1">
                    {{ $char }}
                </a>
            @endforeach
            <a href="#char-hash" class="w-11 h-11 flex items-center justify-center bg-white dark:bg-dark-primary rounded-xl hover:bg-accent-green hover:text-dark-primary transition-all font-black border border-slate-200 dark:border-white/5 text-slate-500 dark:text-gray-500 shadow-lg hover:-translate-y-1">#</a>
        </div>

        <div class="space-y-16">
            @forelse($animes as $char => $group)
                <div id="char-{{ $char === '#' ? 'hash' : $char }}" class="scroll-mt-24">
                    <div class="flex items-center space-x-6 mb-8">
                        <h3 class="text-5xl font-black text-slate-900 dark:text-white opacity-20">{{ $char }}</h3>
                        <div class="h-px flex-grow bg-gradient-to-r from-accent-green/50 to-transparent"></div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-x-12 gap-y-4">
                        @foreach($group as $anime)
                            <a href="{{ route('anime.detail', $anime->slug) }}" class="group flex items-center rounded-2xl hover:bg-slate-100 dark:hover:bg-white/5 transition-all p-2">
                                <div class="w-2.5 h-2.5 bg-accent-green rounded-full mr-4 group-hover:scale-150 transition-transform shadow-[0_0_10px_rgba(0,255,0,0.5)]"></div>
                                <span class="text-lg font-bold text-slate-700 dark:text-gray-400 group-hover:text-slate-900 dark:group-hover:text-white transition-colors line-clamp-1">{{ $anime->title }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="bg-dark-secondary/40 p-20 rounded-3xl text-center border border-white/5">
                    <div class="inline-flex p-6 bg-dark-primary rounded-full mb-6">
                        <svg class="w-12 h-12 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 9.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <p class="text-gray-500 text-xl font-bold italic">Maaf, anime tidak ditemukan.</p>
                </div>
            @endforelse
        </div>
    @else
        <!-- Visual Grid Mode -->
        @if($animes->isEmpty())
            <div class="bg-dark-secondary/40 p-20 rounded-3xl text-center border border-white/5">
                <div class="inline-flex p-6 bg-dark-primary rounded-full mb-6">
                    <svg class="w-12 h-12 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 9.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <p class="text-gray-500 text-xl font-bold italic">Maaf, anime tidak ditemukan.</p>
            </div>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-8 mb-16">
                @foreach($animes as $anime)
                    <a href="{{ route('anime.detail', $anime->slug) }}" class="group relative bg-dark-secondary rounded-2xl overflow-hidden shadow-2xl transition-all hover:-translate-y-2 hover:shadow-accent-green/10">
                        <div class="aspect-[3/4] overflow-hidden">
                            <img src="{{ Str::startsWith($anime->poster_url, 'posters/') ? asset('storage/' . $anime->poster_url) : $anime->poster_url }}" 
                                 class="h-full w-full object-cover transition duration-700 group-hover:scale-110 group-hover:rotate-2" 
                                 alt="{{ $anime->title }}"
                                 loading="lazy">
                            <div class="absolute inset-0 bg-gradient-to-t from-dark-primary via-transparent to-transparent opacity-60 group-hover:opacity-40 transition-opacity"></div>
                        </div>
                        <div class="absolute bottom-0 inset-x-0 p-4 text-center">
                            <h3 class="font-black text-xs sm:text-sm line-clamp-2 text-white group-hover:text-accent-green transition-colors leading-tight drop-shadow-lg">{{ $anime->title }}</h3>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-16 flex justify-center pb-10">
                <div class="bg-white/80 dark:bg-dark-secondary/80 backdrop-blur-xl p-6 rounded-2xl border border-slate-200 dark:border-white/10 shadow-2xl overflow-x-auto min-w-[300px]">
                    <div class="pagination-wrapper">
                        {{ $animes->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        @endif
    @endif
</div>

<style>
    .shadow-inner {
        box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.5);
    }
    .scroll-mt-24 {
        scroll-margin-top: 3rem;
    }
</style>
@endsection