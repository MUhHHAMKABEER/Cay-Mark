<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>@yield('title', 'Crowz Dashboard')</title>

    {{-- Tailwind (CDN fallback – you already have Vite compiling too) --}}
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />

    {{-- Fonts & Icons --}}
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #FDFBF8;
            color: #333;
        }

        .main-content {
            padding: 2rem;
        }


    </style>



    {{-- Vite compiled assets (includes Shepherd.js, Alpine, etc.) --}}
    {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}
</head>

<body class="flex">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />


    {{-- Sidebar --}}
    @include('partials.buyerSidebar')

    {{-- Main Content --}}
    <main class="main-content" style="margin-left: 230px">
        @yield('content')
    </main>

    {{-- Optional: Place Shepherd.js tour starter here --}}
    @stack('scripts')
</body>

</html>
