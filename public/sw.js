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