<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    @include('partials.noindex-meta')
    <title>@yield('title', 'CayMark Island Exchange & Auction House')</title>

    {{-- Tailwind CSS with forms + container-queries plugins --}}
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>

    {{-- ── Google Fonts ─────────────────────────────────────── --}}
    {{-- DM Sans + Inter (new design system) --}}
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,600;0,9..40,700;1,9..40,400&family=Inter:wght@400;500;600&display=swap" rel="stylesheet"/>
    {{-- Roboto + Montserrat (legacy pages) --}}
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Montserrat:wght@600;700;800&display=swap" rel="stylesheet"/>

    {{-- ── Icon libraries ──────────────────────────────────── --}}
    {{-- Material Symbols Outlined (new header / footer) --}}
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    {{-- Material Icons (legacy pages) --}}
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons|Material+Icons+Round" rel="stylesheet"/>

    {{-- Animate.css (legacy pages) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    {{-- Alpine.js (some pages use x-data / x-show / x-transition) --}}
    <script src="https://unpkg.com/alpinejs" defer></script>

    {{-- ── Tailwind design-token config ─────────────────────── --}}
    {{-- Adds CayMark brand colors and typography scale used by   --}}
    {{-- public-header and public-footer WITHOUT overriding the   --}}
    {{-- default borderRadius (so legacy rounded-* classes work). --}}
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        "primary":                    "#002452",
                        "primary-container":          "#1b3a6b",
                        "on-primary":                 "#ffffff",
                        "on-primary-container":       "#89a5dd",
                        "primary-fixed":              "#d7e2ff",
                        "primary-fixed-dim":          "#acc7ff",
                        "inverse-primary":            "#acc7ff",
                        "secondary":                  "#745b00",
                        "secondary-container":        "#fdd977",
                        "on-secondary":               "#ffffff",
                        "on-secondary-container":     "#775e00",
                        "secondary-fixed":            "#ffe08c",
                        "secondary-fixed-dim":        "#C8A84B",
                        "on-secondary-fixed":         "#241a00",
                        "on-secondary-fixed-variant": "#584400",
                        "tertiary":                   "#3c1e00",
                        "tertiary-container":         "#5b3000",
                        "on-tertiary":                "#ffffff",
                        "on-tertiary-container":      "#d7985f",
                        "tertiary-fixed":             "#ffdcc1",
                        "tertiary-fixed-dim":         "#fcb87d",
                        "on-tertiary-fixed":          "#2e1500",
                        "on-tertiary-fixed-variant":  "#693c0a",
                        "background":                 "#fcf9f8",
                        "on-background":              "#1c1b1b",
                        "surface":                    "#fcf9f8",
                        "on-surface":                 "#1c1b1b",
                        "surface-variant":            "#e5e2e1",
                        "on-surface-variant":         "#44474f",
                        "surface-dim":                "#dcd9d9",
                        "surface-bright":             "#fcf9f8",
                        "surface-container-lowest":   "#ffffff",
                        "surface-container-low":      "#f6f3f2",
                        "surface-container":          "#f0eded",
                        "surface-container-high":     "#eae7e7",
                        "surface-container-highest":  "#e5e2e1",
                        "inverse-surface":            "#313030",
                        "inverse-on-surface":         "#f3f0ef",
                        "background-surface":         "#FFFFFF",
                        "surface-tint":               "#425e91",
                        "outline":                    "#747780",
                        "outline-variant":            "#c4c6d0",
                        "error":                      "#ba1a1a",
                        "on-error":                   "#ffffff",
                        "error-container":            "#ffdad6",
                        "on-error-container":         "#93000a",
                        "header-dark":                "#122646",
                        "text-secondary":             "#555555",
                        "ui-soft-gray":               "#F5F9FD",
                    },
                    fontFamily: {
                        "body-lg":            ["Inter", "Roboto", "sans-serif"],
                        "body-md":            ["Inter", "Roboto", "sans-serif"],
                        "body-sm":            ["Inter", "Roboto", "sans-serif"],
                        "label-md":           ["Inter", "Roboto", "sans-serif"],
                        "label-sm":           ["Inter", "Roboto", "sans-serif"],
                        "headline-sm":        ["DM Sans", "Montserrat", "sans-serif"],
                        "headline-md":        ["DM Sans", "Montserrat", "sans-serif"],
                        "headline-lg":        ["DM Sans", "Montserrat", "sans-serif"],
                        "headline-lg-mobile": ["DM Sans", "Montserrat", "sans-serif"],
                        "display-lg":         ["DM Sans", "Montserrat", "sans-serif"],
                    },
                    fontSize: {
                        "body-lg":            ["18px", {lineHeight:"28px", fontWeight:"400"}],
                        "body-md":            ["16px", {lineHeight:"24px", fontWeight:"400"}],
                        "body-sm":            ["14px", {lineHeight:"20px", fontWeight:"400"}],
                        "label-md":           ["14px", {lineHeight:"20px", letterSpacing:"0.05em", fontWeight:"600"}],
                        "label-sm":           ["12px", {lineHeight:"16px", fontWeight:"500"}],
                        "headline-sm":        ["20px", {lineHeight:"28px", fontWeight:"600"}],
                        "headline-md":        ["24px", {lineHeight:"32px", fontWeight:"600"}],
                        "headline-lg":        ["32px", {lineHeight:"40px", fontWeight:"700"}],
                        "headline-lg-mobile": ["24px", {lineHeight:"32px", fontWeight:"700"}],
                        "display-lg":         ["48px", {lineHeight:"56px", letterSpacing:"-0.02em", fontWeight:"700"}],
                    },
                    spacing: {
                        "container-max-width": "1280px",
                        "margin-desktop":      "64px",
                        "margin-tablet":       "32px",
                        "margin-mobile":       "16px",
                        "gutter":              "24px",
                        "base-unit":           "4px",
                    },
                    maxWidth: {
                        "container-max-width": "1280px",
                    },
                }
            }
        }
    </script>

    {{-- ── Material Symbols helper ────────────────────────────── --}}
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        /* ── Legacy layout helpers (kept for existing child views) ── */
        body { font-family: 'Inter', 'Roboto', sans-serif; }
        h1, h2, h3, h4, .font-heading { font-family: 'DM Sans', 'Montserrat', sans-serif; }

        .gradient-bg    { background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 50%, #3b82f6 100%); }
        .hero-gradient  { background: linear-gradient(to right, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.3) 100%); }
        .card-hover     { transition: all 0.3s ease; }
        .card-hover:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.1); }

        .countdown-animation { animation: pulse 2s infinite; }
        @keyframes pulse {
            0%   { transform: scale(1); }
            50%  { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .vehicle-icon { transition: all 0.3s ease; }
        .vehicle-icon:hover { transform: scale(1.1); }

        .hero-carousel { position:absolute; width:100%; height:100%; top:0; left:0; }
        .carousel-slide { position:absolute; width:100%; height:100%; opacity:0; transition:opacity 1s ease-in-out; background-size:cover; background-position:center; }
        .carousel-slide.active { opacity:1; }

        .search-bar { border-radius: 50px; padding: 12px 20px; }

        .footer-gradient { background: linear-gradient(135deg, #0a2258 0%, #1e3a8a 50%, #2563eb 100%); }
        .footer-link { transition: all 0.3s ease; }
        .footer-link:hover { color: #93c5fd; transform: translateX(5px); }
        .social-icon { transition: all 0.3s ease; }
        .social-icon:hover { transform: translateY(-3px); }

        .container { max-width:1200px; margin-left:auto; margin-right:auto; padding-left:1rem; padding-right:1rem; }

        /* Hero carousel (used by welcome page) */
        .hero-carousel-track { display:flex; transition:transform 0.7s cubic-bezier(0.4,0,0.2,1); }
        .hero-slide { flex:0 0 100%; width:100%; }
        .carousel-container { display:flex; overflow-x:auto; scroll-snap-type:x mandatory; scrollbar-width:none; -ms-overflow-style:none; gap:24px; }
        .carousel-container::-webkit-scrollbar { display:none; }
        .carousel-item { flex:0 0 auto; scroll-snap-align:start; }
    </style>

    @stack('styles')
</head>

<body class="bg-gray-50 antialiased flex flex-col min-h-screen">

    {{-- ── New CayMark header (auth-aware, with mobile menu) ── --}}
    @include('partials.public-header')

    {{-- ── Page content ──────────────────────────────────────── --}}
    <main class="flex-grow">
        @yield('content')
    </main>

    {{-- ── New CayMark footer ─────────────────────────────────── --}}
    @include('partials.public-footer')

    {{-- ── Global JS vars ─────────────────────────────────────── --}}
    <script>
        window.csrfToken = '{{ csrf_token() }}';
        window.loginUrl  = '{{ route('login') }}';
    </script>

    {{-- ── Legacy carousel helper (for any page that uses .carousel-slide) ── --}}
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        var slides = document.querySelectorAll('.carousel-slide');
        if (slides.length) {
            var idx = 0;
            function showSlide(n) {
                slides.forEach(function (s) { s.classList.remove('active'); });
                idx = (n + slides.length) % slides.length;
                slides[idx].classList.add('active');
            }
            showSlide(0);
            setInterval(function () { showSlide(idx + 1); }, 5000);
        }
    });
    </script>

    {{-- ── Global UI kit + custom select ─────────────────────── --}}
    @include('partials.caymark-ui-kit')
    @include('partials.cm-custom-select')

    @stack('scripts')
</body>
</html>
