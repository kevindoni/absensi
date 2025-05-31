// Service Worker for Attendance System
const CACHE_NAME = 'attendance-system-v1';
const urlsToCache = [
  '/',
  '/css/welcome.css',
  '/css/fallback-fonts.css', 
  '/js/ultra-modern-nav.js',
  '/sbadmin2/css/sb-admin-2.min.css',
  '/sbadmin2/vendor/fontawesome-free/css/all.min.css'
];

self.addEventListener('install', function(event) {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(function(cache) {
        return cache.addAll(urlsToCache);
      })
  );
});

self.addEventListener('fetch', function(event) {
  event.respondWith(
    caches.match(event.request)
      .then(function(response) {
        if (response) {
          return response;
        }
        return fetch(event.request);
      }
    )
  );
});
