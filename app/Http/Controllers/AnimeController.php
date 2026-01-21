<?php

namespace App\Http\Controllers;

use App\Models\Anime;
use App\Models\Episode;
use Illuminate\Http\Request;

class AnimeController extends Controller
{
    public function index()
    {
        $latestAnimes = Anime::with('genres')->latest()->take(12)->get();
        $latestEpisodes = Episode::with('anime')->latest()->take(12)->get();
        $sliderAnimes = Anime::with('genres')
            ->where('status', 'Berlangsung')
            ->inRandomOrder()
            ->take(5)
            ->get();
        
        // Fallback jika tidak ada anime "Berlangsung"
        if ($sliderAnimes->isEmpty()) {
            $sliderAnimes = Anime::with('genres')->orderBy('rating', 'desc')->take(5)->get();
        }

        $trendingAnime = $sliderAnimes->first();
        $topRatedAnimes = Anime::with('genres')->orderBy('rating', 'desc')->take(6)->get();
        $genres = \App\Models\Genre::inRandomOrder()->take(10)->get();
        
        return view('home', compact('latestAnimes', 'latestEpisodes', 'sliderAnimes', 'trendingAnime', 'topRatedAnimes', 'genres'));
    }

    public function search(Request $request)
    {
        return $this->directory($request);
    }

    public function directory(Request $request)
    {
        $query = $request->input('q');
        $status = $request->input('status');
        $type = $request->input('type');
        $order = $request->input('order', 'title-asc');
        $genres = $request->input('genres', []);
        $mode = $request->input('mode', 'image'); // 'image' or 'text'

        $animeQuery = Anime::with('genres');

        if ($query) {
            $animeQuery->where(function($q) use ($query) {
                $q->where('title', 'LIKE', "%{$query}%")
                  ->orWhere('synopsis', 'LIKE', "%{$query}%");
            });
        }

        if ($status && $status !== 'All') {
            $animeQuery->where('status', $status);
        }

        if ($type && $type !== 'All') {
            $animeQuery->where('type', $type);
        }

        if (!empty($genres)) {
            foreach ($genres as $genreSlug) {
                $animeQuery->whereHas('genres', function($q) use ($genreSlug) {
                    $q->where('slug', $genreSlug);
                });
            }
        }

        // Ordering
        switch ($order) {
            case 'title-asc': $animeQuery->orderBy('title', 'asc'); break;
            case 'title-desc': $animeQuery->orderBy('title', 'desc'); break;
            case 'latest-updated': $animeQuery->latest('updated_at'); break;
            case 'latest-added': $animeQuery->latest('created_at'); break;
            case 'popular': $animeQuery->orderBy('rating', 'desc'); break;
            default: $animeQuery->orderBy('title', 'asc');
        }

        if ($mode === 'text') {
            $animes = $animeQuery->get()->groupBy(function($item) {
                $firstChar = strtoupper(substr($item->title, 0, 1));
                return is_numeric($firstChar) ? '#' : $firstChar;
            })->sortKeys();
        } else {
            $animes = $animeQuery->paginate(24)->withQueryString();
        }

        $allGenres = \App\Models\Genre::whereNotNull('name')->where('name', '!=', '')->orderBy('name')->get();
        
        return view('directory', compact('animes', 'query', 'status', 'type', 'order', 'genres', 'mode', 'allGenres'));
    }

    public function show($slug)
    {
        $anime = Anime::with(['genres', 'episodes' => function($q) {
            $q->orderBy('episode_number', 'desc');
        }])->where('slug', $slug)->firstOrFail();

        return view('detail', compact('anime'));
    }

    public function watch($animeSlug, $episodeSlug)
    {
        $fullSlug = $animeSlug . '/episode/' . $episodeSlug;
        
        $episode = Episode::with(['anime.episodes', 'videos', 'downloads'])
            ->where('slug', $fullSlug)
            ->orWhere('slug', $episodeSlug)
            ->firstOrFail();
            
        $anime = $episode->anime;
        
        $prevEpisode = $anime->episodes()->where('episode_number', '<', $episode->episode_number)->orderBy('episode_number', 'desc')->first();
        $nextEpisode = $anime->episodes()->where('episode_number', '>', $episode->episode_number)->orderBy('episode_number', 'asc')->first();

        return view('watch', compact('episode', 'anime', 'prevEpisode', 'nextEpisode'));
    }

    public function popular()
    {
        $animes = Anime::with('genres')
            ->where('rating', '>=', 8)
            ->orderBy('rating', 'desc')
            ->paginate(24);
        
        $title = "Anime Populer";
        return view('directory', compact('animes', 'title'));
    }

    public function newReleases()
    {
        $animes = Anime::with('genres')
            ->latest('created_at')
            ->paginate(24);
            
        $title = "Rilisan Terbaru";
        return view('directory', compact('animes', 'title'));
    }

    public function genres()
    {
        $allGenres = \App\Models\Genre::whereNotNull('name')
            ->where('name', '!=', '')
            ->orderBy('name')
            ->get();
            
        return view('genres', compact('allGenres'));
    }

    public function genreShow($slug)
    {
        $genre = \App\Models\Genre::where('slug', $slug)->firstOrFail();
        $animes = Anime::with('genres')
            ->whereHas('genres', function($q) use ($slug) {
                $q->where('slug', $slug);
            })
            ->paginate(24);
            
        $title = "Genre: " . $genre->name;
        return view('directory', compact('animes', 'title'));
    }

    public function sitemap()
    {
        $animes = Anime::whereNotNull('slug')->get();
        $episodes = Episode::with('anime')->whereNotNull('slug')->get();
        $genres = \App\Models\Genre::whereNotNull('slug')->where('slug', '!=', '')->get();

        return response()->view('sitemap', [
            'animes' => $animes,
            'episodes' => $episodes,
            'genres' => $genres,
        ])->header('Content-Type', 'text/xml');
    }
}
