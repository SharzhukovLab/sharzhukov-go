/* Sharzhukov's Lab — Service Worker
   version: 1.0.0
   site: Sharzhukov Go
*/
const VERSION = 'v1.1.0';
const CACHE_NAME     = 'sharzhukov-go-' + VERSION;
const CACHE_STATIC   = 'sharzhukov-go-static-' + VERSION;

const PRECACHE_URLS = [
  '/offline.html',
  '/img/branding/favicon-32x32.png',
  '/img/branding/apple-touch-icon.png',
  '/img/branding/android-chrome-192x192.png',
];

// URL, которые НИКОГДА не кэшируем (dynamic, auth, ops)
const NEVER_CACHE = [
  /^\/admin/,
  /^\/api\//,
  /^\/health/,
  /^\/login/,
  /^\/logout/,
  /^\/register/,
  /^\/password/,
  /^\/user\/confirm-password/,
  /^\/dashboard/,
  /^\/security/,
  /^\/sessions/,
  /^\/profile/,
  /^\/messages/,
  /^\/statistics/,
  /^\/projects\/new/,
  /^\/projects\/\d+\/edit/,
  /^\/newsletter\//,
  /^\/passkeys/,
  /^\/storage\//,   // storage/avatars раздаётся через alias — кэшируем CDN, не SW
];

const NEVER_CACHE_METHODS = ['POST', 'PUT', 'PATCH', 'DELETE'];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_STATIC).then((cache) => cache.addAll(PRECACHE_URLS))
      .then(() => self.skipWaiting())
  );
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) => Promise.all(
      keys.filter((k) => !k.endsWith(VERSION)).map((k) => caches.delete(k))
    )).then(() => self.clients.claim())
  );
});

function shouldSkip(request, url) {
  if (NEVER_CACHE_METHODS.includes(request.method)) return true;
  if (url.origin !== self.location.origin) return true;
  return NEVER_CACHE.some((rx) => rx.test(url.pathname));
}

self.addEventListener('fetch', (event) => {
  const request = event.request;
  const url = new URL(request.url);

  if (shouldSkip(request, url)) return; // default network

  // Navigation (HTML)
  if (request.mode === 'navigate' || request.headers.get('accept')?.includes('text/html')) {
    if (!false) {
      // account/go: HTML не кэшируем, только offline fallback на ошибке
      event.respondWith(
        fetch(request).catch(() => caches.match('/offline.html'))
      );
      return;
    }

    // main/wiki: network-first с fallback на кэш + offline
    event.respondWith(
      fetch(request).then((response) => {
        if (response.ok) {
          const clone = response.clone();
          caches.open(CACHE_NAME).then((cache) => cache.put(request, clone));
        }
        return response;
      }).catch(() => caches.match(request).then((cached) => cached || caches.match('/offline.html')))
    );
    return;
  }

  // Static assets (CSS/JS/fonts/img) — cache-first
  const dest = request.destination;
  if (['style','script','font','image'].includes(dest)) {
    event.respondWith(
      caches.match(request).then((cached) => {
        if (cached) return cached;
        return fetch(request).then((response) => {
          if (response.ok && response.type === 'basic') {
            const clone = response.clone();
            caches.open(CACHE_STATIC).then((cache) => cache.put(request, clone));
          }
          return response;
        }).catch(() => cached);
      })
    );
    return;
  }

  // Default: network
});