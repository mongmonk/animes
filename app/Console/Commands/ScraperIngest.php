<?php

namespace App\Console\Commands;

use App\Models\Anime;
use App\Models\Episode;
use App\Models\Genre;
use App\Services\AnimeScraperService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ScraperIngest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scraper:ingest {--url= : Base URL of the series list (e.g. https://situs.com/series)} {--file= : Path to local HTML file for testing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ingest anime data from HTML files';

    protected $scraper;

    public function __construct(AnimeScraperService $scraper)
    {
        parent::__construct();
        $this->scraper = $scraper;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filePath = $this->option('file');
        $url = $this->option('url');

        if ($url) {
            $this->info("Starting process from URL: {$url}");
            $baseUrl = Str::beforeLast($url, '/'); // Base site URL
            
            try {
                $response = \Illuminate\Support\Facades\Http::withoutVerifying()->get($url);
                if (!$response->successful()) {
                    $this->error("Failed to fetch URL. Status: " . $response->status());
                    return;
                }

                // Jika URL adalah halaman detail (bukan list), proses langsung detailnya
                if (Str::contains($url, '/series/')) {
                    $this->processAnimeDetail($response->body(), $baseUrl);
                } else {
                    $this->processFullCrawl($response->body(), $baseUrl);
                }
            } catch (\Exception $e) {
                $this->error("Error: " . $e->getMessage());
            }
        } elseif ($filePath && File::exists(base_path($filePath))) {
            $this->info("Processing single file: {$filePath}");
            $html = File::get(base_path($filePath));
            $this->processAnimeDetail($html);
        } else {
            $this->error("Please provide --url (e.g. https://situs.com/series)");
        }
    }

    private function processFullCrawl(string $html, string $baseUrl)
    {
        $animes = $this->scraper->scrapeSeriesList($html);
        $total = count($animes);
        $this->info("Found {$total} animes. Starting process...");

        foreach ($animes as $index => $item) {
            $currentCount = $index + 1;
            $detailUrl = rtrim($baseUrl, '/') . '/series/' . $item['slug'];
            
            $this->info("[{$currentCount}/{$total}] Processing: {$item['title']}");
            
            $this->crawlDetail($detailUrl, $baseUrl);

            if ($currentCount < $total) {
                $this->info("Waiting 1 second...");
                sleep(1);
            }
        }

        $this->info("Full crawl completed!");
    }

    private function crawlDetail(string $url, string $baseUrl)
    {
        try {
            $response = \Illuminate\Support\Facades\Http::withoutVerifying()->get($url);
            if ($response->successful()) {
                $this->processAnimeDetail($response->body(), $baseUrl);
            } else {
                $this->error("Failed to fetch detail: {$url}");
            }
        } catch (\Exception $e) {
            $this->error("Error crawling detail: " . $e->getMessage());
        }
    }


    private function processAnimeDetail(string $html, ?string $baseUrl = null)
    {
        $data = $this->scraper->scrapeAnimeDetail($html);
        if (empty($data)) {
            $this->error("Failed to scrape detail.");
            return;
        }

        $slug = Str::slug($data['title']); // Fallback slug if not provided in context
        
        // Save poster locally
        $localPoster = $this->scraper->saveImageLocally($data['poster_url'], $slug, $baseUrl);

        $anime = Anime::updateOrCreate(
            ['title' => $data['title']],
            [
                'slug' => $slug,
                'synopsis' => $data['synopsis'],
                'poster_url' => $localPoster ?: $data['poster_url'],
                'type' => $data['type'],
                'status' => $data['status'],
                'rating' => $data['rating'],
                'release_date' => $data['release_date'],
            ]
        );

        // Sync Genres
        $genreIds = [];
        foreach ($data['genres'] as $genreName) {
            $genre = Genre::firstOrCreate(
                ['slug' => Str::slug($genreName)],
                ['name' => $genreName]
            );
            $genreIds[] = $genre->id;
        }
        $anime->genres()->sync($genreIds);

        // Create/Update Episodes
        foreach ($data['episodes'] as $ep) {
            // Check if episode already has sources to avoid redundant scraping
            $existingEpisode = Episode::where('slug', $ep['slug'])->first();
            
            if ($existingEpisode && $existingEpisode->videos()->exists() && $existingEpisode->downloads()->exists()) {
                $this->info("  > Episode {$ep['episode_number']} already has sources, skipping...");
                continue;
            }

            $episode = Episode::updateOrCreate(
                ['slug' => $ep['slug']],
                [
                    'anime_id' => $anime->id,
                    'episode_number' => $ep['episode_number'],
                    'title' => $ep['title'],
                ]
            );

            if ($baseUrl) {
                $watchUrl = rtrim($baseUrl, '/') . '/series/' . $ep['slug'];
                $this->info("  > Fetching episode {$episode->episode_number}...");
                $this->crawlEpisode($watchUrl, $episode);
                
                // GC and delay
                unset($response);
                gc_collect_cycles();
                usleep(500000); // 0.5s delay
            }
        }

        $this->info("Anime detail processed: {$anime->title}");
    }

    private function crawlEpisode(string $url, Episode $episode)
    {
        try {
            $response = \Illuminate\Support\Facades\Http::withoutVerifying()->get($url);
            if ($response->successful()) {
                $data = $this->scraper->scrapeEpisodeWatch($response->body());
                
                // Clear existing sources
                $episode->videos()->delete();
                $episode->downloads()->delete();

                // Save videos
                foreach ($data['videos'] as $video) {
                    $episode->videos()->create($video);
                }

                // Save downloads
                foreach ($data['downloads'] as $download) {
                    $episode->downloads()->create($download);
                }

                $this->info("Updated sources for episode {$episode->episode_number} (" . count($data['videos']) . " videos, " . count($data['downloads']) . " downloads)");
            }
        } catch (\Exception $e) {
            $this->error("Failed to crawl episode: " . $e->getMessage());
        }
    }

    private function processEpisodeWatch(string $html)
    {
        $data = $this->scraper->scrapeEpisodeWatch($html);
        $this->info("Found " . count($data['videos']) . " videos and " . count($data['downloads']) . " downloads.");
    }
}
