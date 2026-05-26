<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('partials.noindex-meta')
    <title>@yield('title', 'CayMark Island Exchange | Premium Vehicle Auctions')</title>

    {{-- Tailwind CSS with forms + container-queries plugins --}}
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>

    {{-- Material Symbols Outlined --}}
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>

    {{-- DM Sans (headings) + Inter (body) --}}
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,600;0,9..40,700;1,9..40,400&family=Inter:wght@400;500;600&display=swap" rel="stylesheet"/>

    {{-- Tailwind design-token config --}}
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "outline":                    "#747780",
                        "primary-fixed":              "#d7e2ff",
                        "background-surface":         "#FFFFFF",
                        "inverse-on-surface":         "#f3f0ef",
                        "surface":                    "#fcf9f8",
                        "tertiary-container":         "#5b3000",
                        "inverse-surface":            "#313030",
                        "on-tertiary":                "#ffffff",
                        "primary-container":          "#1b3a6b",
                        "secondary":                  "#745b00",
                        "error-container":            "#ffdad6",
                        "on-primary-fixed-variant":   "#294678",
                        "on-primary-container":       "#89a5dd",
                        "background":                 "#fcf9f8",
                        "on-error-container":         "#93000a",
                        "on-secondary-fixed":         "#241a00",
                        "tertiary":                   "#3c1e00",
                        "on-primary":                 "#ffffff",
                        "secondary-container":        "#fdd977",
                        "surface-bright":             "#fcf9f8",
                        "on-tertiary-fixed-variant":  "#693c0a",
                        "surface-container-lowest":   "#ffffff",
                        "tertiary-fixed-dim":         "#fcb87d",
                        "on-tertiary-container":      "#d7985f",
                        "surface-tint":               "#425e91",
                        "on-secondary":               "#ffffff",
                        "primary-fixed-dim":          "#acc7ff",
                        "on-secondary-container":     "#775e00",
                        "tertiary-fixed":             "#ffdcc1",
                        "surface-container-high":     "#eae7e7",
                        "surface-container":          "#f0eded",
                        "text-secondary":             "#555555",
                        "on-secondary-fixed-variant": "#584400",
                        "primary":                    "#002452",
                        "ui-soft-gray":               "#F5F9FD",
                        "inverse-primary":            "#acc7ff",
                        "secondary-fixed":            "#ffe08c",
                        "on-tertiary-fixed":          "#2e1500",
                        "on-surface":                 "#1c1b1b",
                        "surface-variant":            "#e5e2e1",
                        "surface-dim":                "#dcd9d9",
                        "secondary-fixed-dim":        "#C8A84B",
                        "surface-container-low":      "#f6f3f2",
                        "header-dark":                "#122646",
                        "surface-container-highest":  "#e5e2e1",
                        "error":                      "#ba1a1a",
                        "on-surface-variant":         "#44474f",
                        "on-error":                   "#ffffff",
                        "outline-variant":            "#c4c6d0",
                        "on-primary-fixed":           "#001a40",
                        "on-background":              "#1c1b1b"
                    },
                    borderRadius: {
                        DEFAULT: "0px",
                        lg:      "0px",
                        xl:      "0px",
                        full:    "9999px"
                    },
                    spacing: {
                        "margin-tablet":        "32px",
                        "margin-mobile":        "16px",
                        "container-max-width":  "1280px",
                        "gutter":               "24px",
                        "base-unit":            "4px",
                        "margin-desktop":       "64px"
                    },
                    fontFamily: {
                        "body-lg":              ["Inter"],
                        "headline-sm":          ["DM Sans"],
                        "headline-lg":          ["DM Sans"],
                        "label-md":             ["Inter"],
                        "display-lg":           ["DM Sans"],
                        "body-md":              ["Inter"],
                        "body-sm":              ["Inter"],
                        "label-sm":             ["Inter"],
                        "headline-lg-mobile":   ["DM Sans"],
                        "headline-md":          ["DM Sans"]
                    },
                    fontSize: {
                        "body-lg":            ["18px", {lineHeight:"28px", fontWeight:"400"}],
                        "headline-sm":        ["20px", {lineHeight:"28px", fontWeight:"600"}],
                        "headline-lg":        ["32px", {lineHeight:"40px", fontWeight:"700"}],
                        "label-md":           ["14px", {lineHeight:"20px", letterSpacing:"0.05em", fontWeight:"600"}],
                        "display-lg":         ["48px", {lineHeight:"56px", letterSpacing:"-0.02em", fontWeight:"700"}],
                        "body-md":            ["16px", {lineHeight:"24px", fontWeight:"400"}],
                        "body-sm":            ["14px", {lineHeight:"20px", fontWeight:"400"}],
                        "label-sm":           ["12px", {lineHeight:"16px", fontWeight:"500"}],
                        "headline-lg-mobile": ["24px", {lineHeight:"32px", fontWeight:"700"}],
                        "headline-md":        ["24px", {lineHeight:"32px", fontWeight:"600"}]
                    },
                    maxWidth: {
                        "container-max-width": "1280px"
                    }
                }
            }
        }
    </script>

    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        /* Hero carousel */
        .hero-carousel-track {
            display: flex;
            transition: transform 0.7s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .hero-slide {
            flex: 0 0 100%;
            width: 100%;
        }
        /* Horizontal scrolling auctions row */
        .carousel-container {
            display: flex;
            overflow-x: auto;
            scroll-snap-type: x mandatory;
            scrollbar-width: none;
            -ms-overflow-style: none;
            gap: 24px;
        }
        .carousel-container::-webkit-scrollbar { display: none; }
        .carousel-item { flex: 0 0 auto; scroll-snap-align: start; }
    </style>

    @stack('styles')
</head>

<body class="bg-surface text-on-surface font-body-md text-body-md antialiased flex flex-col min-h-screen">

    {{-- ── Header (separated into its own partial) ── --}}
    @include('partials.public-header')

    {{-- ── Page content ── --}}
    <main class="flex-grow">
        @yield('content')
    </main>

    {{-- ── Footer (separated into its own partial) ── --}}
    @include('partials.public-footer')

    {{-- Global JS vars --}}
    <script>
        window.csrfToken = '{{ csrf_token() }}';
        window.loginUrl  = '{{ route('login') }}';
    </script>

    @stack('scripts')
</body>
</html>
