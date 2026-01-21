<p align="center">
  <img src="https://capsule-render.vercel.app/render?type=waving&color=eb4034&height=300&section=header&text=Animes%20Project&fontSize=90&animation=fadeIn" alt="Animes Banner" />
</p>

# 🎬 Animes Project

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php)](https://php.net)
[![TailwindCSS](https://img.shields.io/badge/TailwindCSS-3.x-38B2AC?style=for-the-badge&logo=tailwind-css)](https://tailwindcss.com)
[![License](https://img.shields.io/badge/License-MIT-yellow.svg?style=for-the-badge)](LICENSE)

Animes Project adalah platform streaming dan katalog anime modern yang dibangun menggunakan Laravel 12. Proyek ini dilengkapi dengan fitur scraper otomatis untuk mengambil data anime, episode, dan sumber video secara dinamis.

## ✨ Fitur Utama

-   **Scraper Otomatis**: Ingest data anime langsung dari sumber eksternal melalui perintah CLI.
-   **Katalog Lengkap**: Filter berdasarkan genre, popularitas, dan rilis terbaru.
-   **Streaming System**: Dukungan multi-source video dan link download untuk setiap episode.
-   **PWA Ready**: Dilengkapi dengan Service Worker untuk pengalaman offline.
-   **Performa Tinggi**: Menggunakan Laravel Octane dengan RoadRunner untuk responsivitas maksimal.

## 🛠️ Stack Teknologi

-   **Backend**: [Laravel 12](https://laravel.com)
-   **Server**: [Laravel Octane](https://laravel.com/docs/octane) & [RoadRunner](https://roadrunner.dev/)
-   **Frontend**: Blade, TailwindCSS, & Vite
-   **Scraper**: Symfony DomCrawler & Guzzle
-   **Database**: MySQL / SQLite

## 🚀 Instalasi Cepat

1.  **Clone repositori:**
    ```bash
    git clone https://github.com/username/animes.git
    cd animes
    ```

2.  **Setup Proyek:**
    Kami telah menyediakan skrip setup otomatis:
    ```bash
    composer run setup
    ```

3.  **Jalankan Server Pengembangan:**
    ```bash
    composer run dev
    ```

## 🔍 Penggunaan Scraper

Untuk mengambil data anime baru, gunakan perintah artisan berikut:

```bash
# Crawl dari URL list
php artisan scraper:ingest --url=https://situs-sumber.com/series

# Ingest dari file HTML lokal (untuk testing)
php artisan scraper:ingest --file=docs/mock_detail.html
```

## 📂 Struktur Proyek

-   `app/Services/AnimeScraperService.php`: Logika inti scraping.
-   `app/Console/Commands/ScraperIngest.php`: Command untuk menjalankan scraper.
-   `resources/views/`: Template frontend (Home, Detail, Watch, dll).

## 📄 Lisensi

Proyek ini dilisensikan di bawah [MIT license](https://opensource.org/licenses/MIT).
