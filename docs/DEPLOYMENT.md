# Panduan Deployment Scraper Oploverz

Dokumen ini menjelaskan cara mengaktifkan dan menjalankan scraper otomatis untuk mengambil data anime dan episode terbaru dari Oploverz secara berkala.

## 1. Persiapan Environment
Pastikan file `.env` Anda sudah dikonfigurasi dengan benar, terutama bagian database:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_database_anda
DB_USERNAME=root
DB_PASSWORD=
```

## 2. Command Scraper
Terdapat dua jenis scraper yang tersedia:

### A. Scraper Otomatis (Cronjob)
Digunakan untuk mengambil episode terbaru dari homepage secara berkala.
- **Command:** `php artisan scraper:cron`
- **Fungsi:** 
  - Mengecek homepage Oploverz.
  - Jika anime sudah ada, hanya menambah episode baru.
  - Jika anime belum ada, akan melakukan *full crawl* (detail anime + semua episode).

### B. Scraper Manual (Ingest)
Digunakan untuk mengambil data dari URL spesifik (misal daftar semua series).
- **Command:** `php artisan scraper:ingest --url=https://anime.oploverz.ac/series`

## 3. Cara Menjalankan Otomatis (Cronjob)

### Di Server Linux (Ubuntu/CentOS/dll)
Anda perlu mendaftarkan *Scheduler* Laravel ke dalam sistem `crontab`.
1. Masuk ke terminal server.
2. Ketik `crontab -e`.
3. Tambahkan baris berikut di bagian paling bawah:
   ```bash
   * * * * * cd /path-ke-projek-anda && php artisan schedule:run >> /dev/null 2>&1
   ```
   *(Ganti `/path-ke-projek-anda` dengan lokasi folder Laravel Anda)*.

### Di Lokal (Windows / Laragon)
Untuk menjalankan secara otomatis di komputer lokal tanpa crontab:
1. Buka terminal (CMD/Powershell/Git Bash).
2. Jalankan perintah:
   ```bash
   php artisan schedule:work
   ```
   *Terminal ini harus tetap terbuka agar scraper berjalan setiap 30 menit.*

## 4. Log & Pemantauan
Setiap kali scraper berjalan, ia akan mencatat aktivitasnya. Anda bisa memantau jika ada error melalui file log Laravel:
- Lokasi: `storage/logs/laravel.log`

## 5. Troubleshooting
- **Gagal Ambil Data:** Pastikan koneksi internet server stabil dan website `https://anime.oploverz.ac/` bisa diakses.
- **Gambar Tidak Muncul:** Jalankan `php artisan storage:link` untuk memastikan folder poster bisa diakses dari browser.