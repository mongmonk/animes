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
        
        return view('home', compact('latestAnimes', 'latestEpisodes'));
    }

    public function search(Request $request)
    {
        $query = $request->input('q');
        
        $animes = Anime::with('genres')
            ->where('title', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->paginate(20);
            
        return view('directory', compact('animes', 'query'));
    }

    public function directory()
    {
        $animes = Anime::with('genres')->orderBy('title')->paginate(20);
        return view('directory', compact('animes'));
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
}
