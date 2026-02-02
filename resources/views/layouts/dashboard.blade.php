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
            color: #1e3a8a;
            background: #eef2ff;
            padding: 0.25rem 0.75rem;
            border-radius: 999px;
            display: inline-flex;
            border: 1px solid rgba(67, 97, 238, 0.2);
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
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const onDashboard = @json(request()->routeIs('dashboard.buyer', 'dashboard.seller'));
            if (!onDashboard) {
                return;
            }
            const role = @json(Auth::user()->role ?? 'buyer');
            const shouldShowTour = @json((int) (Auth::user()->first_login ?? 1) === 0);
            if (!shouldShowTour) {
                return;
            }

            const buyerSteps = [
                { id: 'buyer-welcome', text: 'Welcome to your Buyer Dashboard. This tour explains each sidebar item.' },
                { id: 'buyer-home', selector: '[data-tour-id="buyer-home"]', text: 'Home takes you back to the public marketplace.' },
                { id: 'buyer-dashboard', selector: '[data-tour-id="buyer-dashboard"]', text: 'Dashboard is your overview of bids, wins, and activity.' },
                { id: 'buyer-user', selector: '[data-tour-id="buyer-user"]', text: 'User lets you manage your profile details.' },
                { id: 'buyer-auctions', selector: '[data-tour-id="buyer-auctions"]', text: 'Auctions shows live and past auctions you follow.' },
                { id: 'buyer-saved-items', selector: '[data-tour-id="buyer-saved-items"]', text: 'Saved Items holds your watchlist.' },
                { id: 'buyer-notifications', selector: '[data-tour-id="buyer-notifications"]', text: 'Notifications keeps you updated on bids and purchases.' },
                { id: 'buyer-messaging-center', selector: '[data-tour-id="buyer-messaging-center"]', text: 'Messaging Center is your inbox for sellers and support.' },
                { id: 'buyer-customer-support', selector: '[data-tour-id="buyer-customer-support"]', text: 'Customer Support opens help and support tickets.' },
                { id: 'buyer-logout', selector: '[data-tour-id="buyer-logout"]', text: 'Log out when you are done.' },
            ];

            const sellerSteps = [
                { id: 'seller-welcome', text: 'Welcome to your Seller Dashboard. This tour explains each sidebar item.' },
                { id: 'seller-dashboard', selector: '[data-tour-id="seller-dashboard"]', text: 'Dashboard shows your listings and sales summary.' },
                { id: 'seller-user', selector: '[data-tour-id="seller-user"]', text: 'User lets you manage your profile details.' },
                { id: 'seller-submission', selector: '[data-tour-id="seller-submission"]', text: 'Submission is where you create a new listing.' },
                { id: 'seller-auctions', selector: '[data-tour-id="seller-auctions"]', text: 'Auctions tracks active and completed auctions.' },
                { id: 'seller-notifications', selector: '[data-tour-id="seller-notifications"]', text: 'Notifications keeps you updated on bids and sales.' },
                { id: 'seller-messaging-center', selector: '[data-tour-id="seller-messaging-center"]', text: 'Messaging Center is your inbox for buyers.' },
                { id: 'seller-customer-support', selector: '[data-tour-id="seller-customer-support"]', text: 'Customer Support opens help and support tickets.' },
                { id: 'seller-my-listings', selector: '[data-tour-id="seller-my-listings"]', text: 'My Listings shows all your submitted vehicles.' },
                { id: 'seller-logout', selector: '[data-tour-id="seller-logout"]', text: 'Log out when you are done.' },
            ];

            const steps = role === 'seller' ? sellerSteps : buyerSteps;
            const markFirstLogin = () => {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                if (!csrfToken) {
                    return;
                }
                fetch(@json(route('user.markFirstLogin')), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ role }),
                }).catch(() => {});
            };

            const startTour = () => {
                if (!window.Shepherd) {
                    return;
                }
                const tour = new Shepherd.Tour({
                    defaultStepOptions: {
                        scrollTo: { behavior: 'smooth', block: 'center' },
                        cancelIcon: { enabled: true },
                    },
                    useModalOverlay: true,
                });

                steps.forEach((step, index) => {
                    const hasSelector = step.selector && document.querySelector(step.selector);
                    if (step.selector && !hasSelector) {
                        return;
                    }
                    tour.addStep({
                        id: step.id,
                        text: step.text,
                        attachTo: step.selector ? { element: step.selector, on: 'right' } : undefined,
                        buttons: [
                            ...(index === 0 ? [] : [{ text: 'Back', action: tour.back }]),
                            {
                                text: index === steps.length - 1 ? 'Finish' : 'Next',
                                action: index === steps.length - 1 ? tour.complete : tour.next,
                            },
                            { text: 'Skip', action: tour.cancel },
                        ],
                    });
                });

                tour.on('complete', markFirstLogin);
                tour.on('cancel', markFirstLogin);

                tour.start();
            };

            if (window.Shepherd) {
                startTour();
            } else {
                const script = document.createElement('script');
                script.src = 'https://unpkg.com/shepherd.js/dist/js/shepherd.min.js';
                script.onload = startTour;
                document.body.appendChild(script);
            }
        });
    </script>
    @stack('scripts')
</body>

</html>


