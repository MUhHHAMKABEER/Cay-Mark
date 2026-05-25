<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('partials.noindex-meta')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
        }

        .main-content {
            padding: 2rem;
            width: calc(100% - var(--cm-sidebar-width, 240px));
            margin-left: var(--cm-sidebar-width, 240px);
            min-height: 100vh;
            background: #f8fafc;
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1), width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body.sidebar-collapsed .main-content {
            width: calc(100% - var(--cm-sidebar-collapsed, 70px));
            margin-left: var(--cm-sidebar-collapsed, 70px);
        }

        @media (max-width: 768px) {
            .main-content,
            body.sidebar-collapsed .main-content {
                margin-left: 0;
                width: 100%;
            }
        }
    </style>
</head>
<body class="flex">
    @include('partials.unifiedSidebar')

    <main class="main-content">
        <div class="max-w-[1600px] mx-auto">
            <x-ui.breadcrumbs class="mb-2" />
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <script src="{{ asset('js/admin-table-filter.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.confirm-action').forEach(function(button) {
                button.addEventListener('click', function(e) {
                    if (!confirm('Are you sure you want to perform this action?')) {
                        e.preventDefault();
                    }
                });
            });
        });
    </script>
    @include('partials.caymark-ui-kit')
    @include('partials.cm-custom-select')
    @stack('scripts')
</body>
</html>
