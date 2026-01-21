<?php

namespace App\Services;

use App\Models\Anime;
use App\Models\Episode;
use App\Models\Genre;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler;
use Carbon\Carbon;

class AnimeScraperService
{
    /**
     * Scrape list of anime from series list page.
     */
    public function scrapeSeriesList(string $html): array
    {
        $crawler = new Crawler($html);
        $animes = [];

        $crawler->filter('div[id^="section-"] a[href^="/series/"]')->each(function (Crawler $node) use (&$animes) {
            $animes[] = [
                'title' => $node->text(),
                'slug' => str_replace('/series/', '', $node->attr('href')),
            ];
        });

        return $animes;
    }

    /**
     * Scrape latest episodes from homepage.
     */
    public function scrapeLatestEpisodes(string $html): array
    {
        $crawler = new Crawler($html);
        $episodes = [];

        // Oploverz modern menggunakan SvelteKit dan data seringkali di-serialize dalam bentuk array of objects
        // Kita cari semua objek yang memiliki pola series dan episodeNumber
        if (preg_match_all('/\{id:\d+,subbed:".*?",title:.*?,episodeNumber:"(\d+)",.*?series:\{id:\d+,seriesId:\d+,title:"(.*?)",.*?slug:"(.*?)",/s', $html, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $m) {
                $epNum = $m[1];
                $animeTitle = $m[2];
                $animeSlug = $m[3];
                
                $episodes[] = [
                    'anime_title' => $animeTitle,
                    'episode_number' => (float) $epNum,
                    'slug' => $animeSlug . '-' . $epNum,
                    'anime_slug' => $animeSlug,
                    'full_url' => 'https://anime.oploverz.ac/series/' . $animeSlug . '/' . $epNum
                ];
            }
        }

        // Jika regex spesifik gagal, coba cari pola JSON yang lebih umum
        if (empty($episodes)) {
            if (preg_match('/latestEpisodes\s*:\s*(\[.*?\])\s*,\s*meta/s', $html, $matches)) {
                $json = $matches[1];
                $jsonCleaned = preg_replace('/(\w+):/', '"$1":', $json);
                $jsonCleaned = preg_replace('/:\s*undefined/', ':null', $jsonCleaned);
                $jsonCleaned = str_replace("'", '"', $jsonCleaned);
                $data = json_decode($jsonCleaned, true);

                if ($data) {
                    foreach ($data as $item) {
                        $slug = $item['series']['slug'] ?? '';
                        $epNum = $item['episodeNumber'] ?? '';
                        $episodes[] = [
                            'anime_title' => $item['series']['title'] ?? 'Unknown',
                            'episode_number' => (float) $epNum,
                            'slug' => $slug . '-' . $epNum,
                            'anime_slug' => $slug,
                            'full_url' => 'https://anime.oploverz.ac/series/' . $slug . '/' . $epNum
                        ];
                    }
                }
            }
        }

        // Fallback ke crawler jika JSON tidak ditemukan
        if (empty($episodes)) {
            $crawler->filter('div.group.relative.cursor-pointer.flex-shrink-0')->each(function (Crawler $node) use (&$episodes) {
                $linkNode = $node->filter('a')->first();
                $titleNode = $node->filter('h3')->first();
                $epNode = $node->filter('span.inline-block.rounded.bg-primary')->first();

                if ($titleNode->count() > 0 && $epNode->count() > 0) {
                    $fullTitle = $titleNode->text();
                    $epText = $epNode->text();
                    
                    preg_match('/[\d\.]+/', $epText, $matches);
                    $epNumber = isset($matches[0]) ? (float) $matches[0] : 0;

                    $href = $linkNode->count() > 0 ? $linkNode->attr('href') : '';
                    $slug = Str::afterLast(rtrim($href, '/'), '/');

                    $episodes[] = [
                        'anime_title' => $fullTitle,
                        'episode_number' => $epNumber,
                        'slug' => $slug,
                        'full_url' => $href
                    ];
                }
            });
        }

        return $episodes;
    }

    /**
     * Scrape anime details from detail page.
     */
    public function scrapeAnimeDetail(string $html): array
    {
        $crawler = new Crawler($html);
        
        $titleNode = $crawler->filter('p.text-2xl.font-semibold');
        if ($titleNode->count() === 0) {
            return [];
        }

        $title = $titleNode->first()->text();
        
        // Synopsis is usually the 3rd paragraph in the detail section
        $synopsis = '';
        $detailContainer = $crawler->filter('div.p-6.flex.flex-col.gap-5')->first();
        if ($detailContainer->count() > 0) {
            $paragraphs = $detailContainer->filter('p');
            if ($paragraphs->count() >= 3) {
                $synopsis = $paragraphs->eq(2)->text();
            }
        }

        $posterUrl = '';
        $imgNode = $crawler->filter('img[alt^="cover-"]');
        if ($imgNode->count() > 0) {
            $posterUrl = $imgNode->first()->attr('src');
        }
        
        $metadata = [];
        $crawler->filter('ul.list-disc li')->each(function (Crawler $node) use (&$metadata) {
            $text = $node->text();
            if (Str::contains($text, ':')) {
                [$key, $value] = explode(':', $text, 2);
                $metadata[trim($key)] = trim($value);
            }
        });

        $genres = [];
        if (isset($metadata['Genre'])) {
            $genres = array_map('trim', explode(',', $metadata['Genre']));
        }

        $episodes = [];
        $crawler->filter('a[href*="/episode/"]')->each(function (Crawler $node) use (&$episodes) {
            $href = $node->attr('href');
            if (preg_match('/\/episode\/([\d\.]+)/', $href, $matches)) {
                $episodes[] = [
                    'episode_number' => (float) $matches[1],
                    'slug' => Str::after($href, '/series/'),
                    'title' => $node->filter('p')->count() > 0 ? $node->filter('p')->first()->text() : "Episode " . $matches[1],
                ];
            }
        });

        return [
            'title' => $title,
            'synopsis' => $synopsis,
            'poster_url' => $posterUrl,
            'type' => $metadata['Tipe'] ?? null,
            'status' => $metadata['Status'] ?? null,
            'rating' => isset($metadata['Skor']) ? (float) filter_var($metadata['Skor'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) : null,
            'release_date' => isset($metadata['Tanggal Rilis']) ? $this->parseDate($metadata['Tanggal Rilis']) : null,
            'genres' => $genres,
            'episodes' => $episodes,
        ];
    }

    /**
     * Download and save image locally.
     */
    public function saveImageLocally(string $url, string $slug, ?string $baseUrl = null): ?string
    {
        try {
            if (empty($url)) return null;

            // Handle relative URLs
            if (!Str::startsWith($url, ['http://', 'https://']) && $baseUrl) {
                $url = rtrim($baseUrl, '/') . '/' . ltrim($url, '/');
            }

            $response = Http::withoutVerifying()->get($url);
            if ($response->successful()) {
                $extension = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
                $filename = "posters/{$slug}.{$extension}";
                Storage::disk('public')->put($filename, $response->body());
                return $filename;
            }
        } catch (\Exception $e) {
            // Log error or handle silently
        }
        return null;
    }

    /**
     * Scrape episode watch details (embed URLs and download links).
     */
    public function scrapeEpisodeWatch(string $html): array
    {
        // Gunakan regex langsung pada HTML mentah untuk menghemat memori
        $htmlContent = $html;
        
        $videos = [];
        $downloads = [];

        // Extract videos using a more robust regex for the JS-like structure
        if (preg_match('/streamUrl:(\[.*?\]),content:/s', $htmlContent, $matches)) {
            $streamPart = $matches[1];
            if (preg_match_all('/\{source:"([^"]+)",url:"([^"]+)"\}/', $streamPart, $streamMatches, PREG_SET_ORDER)) {
                foreach ($streamMatches as $m) {
                    $videos[] = [
                        'source' => $m[1],
                        'url' => stripslashes($m[2]),
                    ];
                }
            }
        }

        // Extract downloads using a more manual but robust splitting approach
        if (preg_match('/downloadUrl:(\[.*?\]),streamUrl:/s', $htmlContent, $matches)) {
            $downloadPart = $matches[1];
            
            // Split by format
            $formats = preg_split('/\{format:"/', $downloadPart, -1, PREG_SPLIT_NO_EMPTY);
            foreach ($formats as $fPart) {
                if (preg_match('/^([^"]+)",resolutions:\[(.*)\]\}/s', $fPart, $fMatches)) {
                    $format = $fMatches[1];
                    $resPart = $fMatches[2];
                    
                    // Split resolutions within format
                    $resGroups = preg_split('/\{quality:"/', $resPart, -1, PREG_SPLIT_NO_EMPTY);
                    foreach ($resGroups as $rPart) {
                        if (preg_match('/^([^"]+)",download_links:\[(.*)\]\}/s', $rPart, $rMatches)) {
                            $quality = $rMatches[1];
                            $linksPart = $rMatches[2];
                            
                            // Extract links within resolution
                            if (preg_match_all('/\{host:"([^"]+)",url:"([^"]+)"\}/', $linksPart, $linkMatches, PREG_SET_ORDER)) {
                                foreach ($linkMatches as $lm) {
                                    $downloads[] = [
                                        'format' => $format,
                                        'quality' => $quality,
                                        'host' => $lm[1],
                                        'url' => stripslashes($lm[2]),
                                    ];
                                }
                            }
                        }
                    }
                }
            }
        }

        return [
            'videos' => $videos,
            'downloads' => $downloads,
        ];
    }

    /**
     * Parse Indonesian date string to Y-m-d.
     */
    private function parseDate(string $dateString): ?string
    {
        try {
            $months = [
                'Januari' => 'January', 'Februari' => 'February', 'Maret' => 'March',
                'April' => 'April', 'Mei' => 'May', 'Juni' => 'June',
                'Juli' => 'July', 'Agustus' => 'August', 'September' => 'September',
                'Oktober' => 'October', 'November' => 'November', 'Desember' => 'December'
            ];
            $dateString = str_replace(array_keys($months), array_values($months), $dateString);
            return Carbon::parse($dateString)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
}