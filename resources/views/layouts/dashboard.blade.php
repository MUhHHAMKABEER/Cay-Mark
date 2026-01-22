<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CayMark Dashboard')</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet" />
    <link rel="stylesheet" href="https://unpkg.com/shepherd.js/dist/css/shepherd.css" />

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #FDFBF8;
            color: #333;
        }

        .main-content {
            padding: 2rem;
            width: calc(100% - 250px);
            margin-left: 250px;
        }

        .sidebar {
            width: 250px;
            background-color: #F9F1EC;
            padding: 2rem;
            height: 100vh;
            display: flex;
            flex-direction: column;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 100;
        }
    </style>
    <style>
        :root {
            --primary-dark: #0a1930;
            --primary-blue: #1a365d;
            --accent-gold: #d4af37;
            --accent-silver: #c0c0c0;
            --light-bg: #f8fafc;
            --text-dark: #1e293b;
            --text-light: #64748b;
            --buyer-bubble: #e3f2fd;
            --seller-bubble: #f0f4ff;
            --shadow-soft: 0 4px 20px rgba(0, 0, 0, 0.08);
            --shadow-medium: 0 8px 30px rgba(0, 0, 0, 0.12);
            --shadow-deep: 0 15px 40px rgba(0, 0, 0, 0.15);
            --border-radius: 12px;
            --transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        /* Dashboard Layout */
        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar (unified design based on seller) */
        .unified-sidebar {
            width: 250px;
            background: linear-gradient(180deg, var(--primary-dark) 0%, var(--primary-blue) 100%);
            color: white;
            padding: 20px 0;
            box-shadow: var(--shadow-deep);
            z-index: 100;
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .unified-sidebar .logo {
            padding: 0 20px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 20px;
        }

        .unified-sidebar .logo h1 {
            font-size: 1.5rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .unified-sidebar .logo h1 i {
            color: var(--accent-gold);
        }

        .unified-sidebar nav ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .unified-sidebar nav li {
            margin-bottom: 5px;
        }

        .unified-sidebar nav a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: var(--transition);
            gap: 10px;
        }

        .unified-sidebar nav a:hover, 
        .unified-sidebar nav a.active {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border-left: 3px solid var(--accent-gold);
        }

        .unified-sidebar nav a i {
            width: 20px;
            text-align: center;
        }

        .unified-sidebar .user-profile {
            padding: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 20px;
            text-align: center;
        }

        .unified-sidebar .user-profile .avatar {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            margin: 0 auto 10px;
            border: 3px solid rgba(255, 255, 255, 0.2);
        }

        .unified-sidebar .user-profile .user-name {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .unified-sidebar .user-profile .user-role {
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.7);
        }

        .unified-sidebar .logout-section {
            margin-top: auto;
            padding: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .unified-sidebar .logout-btn {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: var(--transition);
            gap: 10px;
            background: none;
            border: none;
            width: 100%;
            cursor: pointer;
            text-align: left;
        }

        .unified-sidebar .logout-btn:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .unified-sidebar .logout-btn i {
            width: 20px;
            text-align: center;
        }

        /* Main Content Area */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            margin-left: 250px;
            width: calc(100% - 250px);
            height: 100vh;
            padding: 0;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .unified-sidebar {
                width: 70px;
            }

            .unified-sidebar .logo h1 span,
            .unified-sidebar nav a span,
            .unified-sidebar .user-profile .user-name,
            .unified-sidebar .user-profile .user-role,
            .unified-sidebar .logout-btn span {
                display: none;
            }

            .unified-sidebar .logo h1 {
                justify-content: center;
            }

            .unified-sidebar nav a {
                justify-content: center;
                padding: 15px;
            }

            .main-content {
                margin-left: 70px;
                width: calc(100% - 70px);
            }
        }
    </style>
</head>

<body class="flex">
    {{-- Unified Sidebar --}}
    @include('partials.unifiedSidebar')

    {{-- Main Content --}}
    <main class="main-content">
        @yield('content')
    </main>

    <script src="https://unpkg.com/shepherd.js/dist/js/shepherd.min.js"></script>
    @stack('scripts')
</body>

</html>


