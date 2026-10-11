@props(['title' => null, 'description' => null])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? "Sharzhukov Go" }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#ff5c1f">
    {{-- Favicon --}}
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" type="image/png" sizes="32x32" href="/img/branding/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/img/branding/favicon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/img/branding/apple-touch-icon.png">
    <link rel="manifest" href="/site.webmanifest">
    @stack('meta')
        <script>
        (function () {
    var V = 'v2-2026-10-11';
    if (localStorage.getItem('theme_version') !== V) {
        localStorage.removeItem('theme');
        localStorage.setItem('theme_version', V);
    }
    var t = localStorage.getItem('theme');
    if (t === 'dark') document.documentElement.classList.add('dark');
})();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- SEO + Open Graph --}}
    <meta name="description" content="{{ $description ?? 'Sharzhukov\'s Laboratory — open-source collective building tools for developers.' }}">
    <meta name="robots" content="index, follow">

    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $title ?? config('app.name') }}">
    <meta property="og:description" content="{{ $description ?? 'Open-source collective building tools for developers. Made in EU.' }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="Sharzhukov's Laboratory">
    <meta property="og:image" content="{{ url('/img/branding/core-white.png') }}">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $title ?? config('app.name') }}">
    <meta name="twitter:description" content="{{ $description ?? 'Open-source collective building tools for developers.' }}">

    <link rel="canonical" href="{{ url()->current() }}">
    <x-lab::analytics />
<script>
(function () {
    if (!('serviceWorker' in navigator)) return;
    if (location.protocol !== 'https:' && location.hostname !== 'localhost') return;

    window.addEventListener('load', function () {
        navigator.serviceWorker.register('/sw.js', { scope: '/' })
            .then(function (reg) {
                // Проверка обновлений
                reg.addEventListener('updatefound', function () {
                    var nw = reg.installing;
                    if (!nw) return;
                    nw.addEventListener('statechange', function () {
                        if (nw.state === 'installed' && navigator.serviceWorker.controller) {
                            console.log('[PWA] New version ready — refresh to apply');
                        }
                    });
                });
            })
            .catch(function (err) { console.warn('[PWA] SW register failed', err); });
    });
})();
</script>


</head>
<body>

<x-lab::site-nav
    brand-title="SHARZHUKOV GO"
    brand-sub="// SHORT LINKS"
    :items="[
        ['label' => 'Create',   'url' => '/'],
        ['label' => 'My links', 'url' => '/my'],
        ['label' => 'Main',   'url' => 'https://sharzhukov.com', 'external' => true],
    ]"
/>

<main>{{ $slot }}</main>

<x-lab::site-footer context="go" />

<script>
    function toggleLabTheme() {
        var d = document.documentElement.classList.toggle('dark');
        localStorage.setItem('theme', d ? 'dark' : 'light');
    }
</script>
<x-lab::cookie-consent />
{{-- PWA install prompt (brutal style) --}}
<div id="pwa-install" data-open="false" style="display:none; position:fixed; bottom:20px; left:20px; right:20px; z-index:90; max-width:480px; margin:0 auto;">
    <div style="background:var(--lab-bg);border:3px solid var(--brutal-border);box-shadow:8px 8px 0 var(--brutal-accent);padding:20px;">
        <div style="display:flex;align-items:flex-start;gap:14px;">
            <img src="/img/branding/android-chrome-192x192.png" alt="" style="width:48px;height:48px;flex-shrink:0;border:2px solid var(--brutal-border);">
            <div style="flex:1;min-width:0;">
                <div style="font-family:var(--font-mono);font-size:10px;font-weight:700;letter-spacing:0.15em;color:var(--brutal-accent);text-transform:uppercase;margin-bottom:4px;">// INSTALL APP</div>
                <div style="font-family:var(--font-sans);font-weight:900;font-size:16px;letter-spacing:-0.01em;color:var(--brutal-border);margin-bottom:6px;">
                    Install <span id="pwa-app-name">Sharzhukov's Lab</span>
                </div>
                <p style="margin:0 0 12px;color:var(--lab-fg-muted);font-size:13px;line-height:1.5;">
                    Faster access, works offline, native window.
                </p>
                <div style="display:flex;gap:8px;flex-wrap:wrap;">
                    <button type="button" id="pwa-install-go" class="lab-btn lab-btn--primary" style="font-size:11px;padding:8px 14px;">INSTALL →</button>
                    <button type="button" id="pwa-install-later" class="lab-btn lab-btn--secondary" style="font-size:11px;padding:8px 14px;">NOT NOW</button>
                </div>
                <div id="pwa-ios-hint" style="display:none;margin-top:10px;font-family:var(--font-mono);font-size:11px;color:var(--lab-fg-muted);line-height:1.5;">
                    On iPhone: tap <strong>Share</strong> → <strong>Add to Home Screen</strong>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    var KEY_DISMISS = 'pwa_install_dismissed_until';
    var el   = document.getElementById('pwa-install');
    var btnI = document.getElementById('pwa-install-go');
    var btnL = document.getElementById('pwa-install-later');
    var iosH = document.getElementById('pwa-ios-hint');
    if (!el) return;

    // Уже установлено (standalone)?
    var isStandalone = window.matchMedia('(display-mode: standalone)').matches
                    || window.navigator.standalone === true;
    if (isStandalone) return;

    // Недавно отклоняли?
    var until = parseInt(localStorage.getItem(KEY_DISMISS) || '0', 10);
    if (Date.now() < until) return;

    // iOS detection (не поддерживает beforeinstallprompt)
    var isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
    var isSafari = /^((?!chrome|android).)*safari/i.test(navigator.userAgent);

    function show(hint) {
        el.style.display = 'block';
        el.setAttribute('data-open', 'true');
        if (hint) { iosH.style.display = 'block'; btnI.style.display = 'none'; }
        else      { iosH.style.display = 'none'; }
    }
    function hide(days) {
        el.style.display = 'none';
        el.setAttribute('data-open', 'false');
        if (days) {
            var until = Date.now() + days * 86400000;
            localStorage.setItem(KEY_DISMISS, String(until));
        }
    }

    // ── Android / Desktop Chrome / Edge: beforeinstallprompt ──
    var deferredPrompt = null;
    window.addEventListener('beforeinstallprompt', function (e) {
        e.preventDefault();
        deferredPrompt = e;
        show(false);
    });

    // ── iOS: инструкция ──
    if (isIOS && isSafari) {
        // показываем через 20 сек, не сразу (не раздражать)
        setTimeout(function () { show(true); }, 20000);
    }

    if (btnI) {
        btnI.addEventListener('click', async function () {
            if (deferredPrompt) {
                deferredPrompt.prompt();
                var choice = await deferredPrompt.userChoice;
                deferredPrompt = null;
                if (choice.outcome === 'accepted') {
                    hide(365);
                } else {
                    hide(7);
                }
            } else {
                hide(7);
            }
        });
    }
    if (btnL) {
        btnL.addEventListener('click', function () { hide(7); });
    }

    // После успешной установки — скрыть
    window.addEventListener('appinstalled', function () {
        hide(365);
    });
})();
</script>

</body>
</html>