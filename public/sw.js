const CACHE_NAME = 'animestream-v1';
const urlsToCache = [
    '/',
    '/series',
    '/offline.html'
];

self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => cache.addAll(urlsToCache))
    );
});

self.addEventListener('fetch', event => {
    // Strategi Network First untuk navigasi halaman (HTML)
    // Agar jika ada error di server dan kemudian diperbaiki, user mendapatkan versi terbaru tanpa hard refresh
    if (event.request.mode === 'navigate') {
        event.respondWith(
            fetch(event.request)
                .then(response => {
                    // Simpan copy response ke cache
                    const copy = response.clone();
                    caches.open(CACHE_NAME).then(cache => cache.put(event.request, copy));
                    return response;
                })
                .catch(() => {
                    // Jika network gagal, coba ambil dari cache
                    return caches.match(event.request)
                        .then(response => response || caches.match('/offline.html'));
                })
        );
        return;
    }

    // Strategi Cache First untuk asset statis lainnya
    event.respondWith(
        caches.match(event.request)
            .then(response => {
                if (response) {
                    return response;
                }
                return fetch(event.request);
            }).catch(() => {
                return caches.match('/offline.html');
            })
    );
});