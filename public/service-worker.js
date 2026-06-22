const CACHE_NAME = 'scheapp-pro-v2';

// Hanya cache asset statis yang pasti ada
const STATIC_ASSETS = [
    '/manifest.json',
    '/icons/icon.svg',
];

self.addEventListener('install', event => {
    self.skipWaiting();
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => {
            // addAll individual agar 1 gagal tidak batalkan semua
            return Promise.allSettled(
                STATIC_ASSETS.map(url => cache.add(url).catch(() => {}))
            );
        })
    );
});

self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(keys =>
            Promise.all(keys
                .filter(k => k !== CACHE_NAME)
                .map(k => caches.delete(k))
            )
        ).then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', event => {
    const { request } = event;
    const url = new URL(request.url);

    // Jangan cache: POST/PATCH/DELETE, API calls, admin routes
    if (request.method !== 'GET') return;
    if (url.pathname.startsWith('/calendar/events')) return;
    if (url.pathname.startsWith('/wallet')) return;

    // Strategi: Network first, fallback ke cache untuk navigasi
    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request).catch(() =>
                caches.match(request).then(r => r || caches.match('/schedules'))
            )
        );
        return;
    }

    // Strategi: Cache first untuk static assets (icons, manifest)
    if (url.pathname.startsWith('/icons/') || url.pathname === '/manifest.json') {
        event.respondWith(
            caches.match(request).then(r => r || fetch(request))
        );
        return;
    }

    // Semua lainnya: network first
    event.respondWith(fetch(request).catch(() => caches.match(request)));
});

// Push notification handler
self.addEventListener('push', event => {
    if (!event.data) return;
    let data;
    try { data = event.data.json(); } catch { return; }

    event.waitUntil(
        self.registration.showNotification(data.title || 'ScheApp Pro', {
            body: data.body || '',
            icon: '/icons/icon.svg',
            badge: '/icons/icon.svg',
            data: { url: data.url || '/schedules' },
            vibrate: [200, 100, 200],
        })
    );
});

self.addEventListener('notificationclick', event => {
    event.notification.close();
    event.waitUntil(
        clients.matchAll({ type: 'window' }).then(windowClients => {
            const url = event.notification.data?.url || '/schedules';
            for (const client of windowClients) {
                if (client.url === url && 'focus' in client) return client.focus();
            }
            if (clients.openWindow) return clients.openWindow(url);
        })
    );
});
