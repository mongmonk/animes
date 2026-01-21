<?php

namespace App\Console\Commands;

use App\Models\Anime;
use App\Models\Episode;
use App\Models\Genre;
use App\Services\AnimeScraperService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ScraperCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scraper:cron {--url=https://anime.oploverz.ac : URL homepage Oploverz}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scrape episode terbaru dari Oploverz secara otomatis';

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
        $url = $this->option('url');
        $this->info("Memulai pengecekan episode terbaru dari: {$url}");

        try {
            $response = Http::withoutVerifying()->get($url);
            if (!$response->successful()) {
                $this->error("Gagal mengambil data dari homepage. Status: " . $response->status());
                return;
            }

            $latestEpisodes = $this->scraper->scrapeLatestEpisodes($response->body());
            $this->info("Ditemukan " . count($latestEpisodes) . " episode di homepage.");

            foreach ($latestEpisodes as $item) {
                $this->processEpisode($item, $url);
            }

            $this->info("Proses sinkronisasi selesai!");
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
        }
    }

    private function processEpisode(array $item, string $baseUrl)
    {
        $this->info("Mengecek: {$item['anime_title']} - Episode {$item['episode_number']}");

        // Cek apakah episode sudah ada
        $existingEpisode = Episode::where('slug', $item['slug'])->first();
        if ($existingEpisode && $existingEpisode->videos()->exists()) {
            $this->info("  > Episode sudah ada, skip.");
            return;
        }

        // Cek anime berdasarkan slug atau title
        $anime = Anime::where('slug', $item['anime_slug'])->orWhere('title', $item['anime_title'])->first();
        $detailBaseUrl = rtrim($baseUrl, '/');

        if (!$anime) {
            $this->warn("  > Anime '{$item['anime_title']}' belum ada. Mengambil detail serial...");
            $seriesUrl = $detailBaseUrl . '/series/' . $item['anime_slug'];
            $this->crawlFullAnime($seriesUrl, $detailBaseUrl);
            return;
        }

        // Jika anime sudah ada, proses episode ini
        $episodeUrl = $item['full_url'] ?: $detailBaseUrl . '/series/' . $item['anime_slug'] . '/' . $item['episode_number'];
        
        try {
            $response = Http::withoutVerifying()->get($episodeUrl);
            if ($response->successful()) {
                $this->updateEpisodeData($response->body(), $anime, $item);
                $this->info("  > BERHASIL: Episode {$item['episode_number']} diperbarui.");
            }
        } catch (\Exception $e) {
            $this->error("  > Gagal crawl episode: " . $e->getMessage());
        }

        // Delay sedikit biar tidak kena ban
        usleep(500000);
    }

    private function crawlFullAnime(string $url, string $baseUrl)
    {
        try {
            $response = Http::withoutVerifying()->get($url);
            if ($response->successful()) {
                $data = $this->scraper->scrapeAnimeDetail($response->body());
                if (empty($data)) return;

                $slug = Str::slug($data['title']);
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

                $this->info("  > Anime '{$anime->title}' berhasil dibuat/diperbarui.");

                // Crawl semua episode
                foreach ($data['episodes'] as $ep) {
                    $this->info("    - Mengambil episode {$ep['episode_number']}...");
                    $epWatchUrl = $baseUrl . '/series/' . $ep['slug'];
                    $epResponse = Http::withoutVerifying()->get($epWatchUrl);
                    if ($epResponse->successful()) {
                        $this->updateEpisodeData($epResponse->body(), $anime, [
                            'slug' => $ep['slug'],
                            'episode_number' => $ep['episode_number'],
                            'anime_title' => $anime->title
                        ]);
                    }
                    usleep(300000);
                }
            }
        } catch (\Exception $e) {
            $this->error("Error crawl full anime: " . $e->getMessage());
        }
    }

    private function updateEpisodeData(string $html, Anime $anime, array $item)
    {
        $episode = Episode::updateOrCreate(
            ['slug' => $item['slug']],
            [
                'anime_id' => $anime->id,
                'episode_number' => $item['episode_number'],
                'title' => $item['anime_title'] . " Episode " . $item['episode_number'],
            ]
        );

        $data = $this->scraper->scrapeEpisodeWatch($html);
        
        $episode->videos()->delete();
        foreach ($data['videos'] as $video) {
            $episode->videos()->create($video);
        }

        $episode->downloads()->delete();
        foreach ($data['downloads'] as $download) {
            $episode->downloads()->create($download);
        }
    }
}