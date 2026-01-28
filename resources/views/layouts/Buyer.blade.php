<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Crowz Dashboard')</title>

    {{-- Tailwind (CDN fallback – you already have Vite compiling too) --}}
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />

    {{-- Fonts & Icons --}}
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <link rel="stylesheet" href="https://unpkg.com/shepherd.js/dist/css/shepherd.css" />

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

    <script src="https://unpkg.com/shepherd.js/dist/js/shepherd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const onBuyerDashboard = @json(request()->routeIs('buyer.user', 'buyer.dashboard'));
            if (!onBuyerDashboard) {
                return;
            }
            if (!window.Shepherd) {
                return;
            }
            const shouldShowTour = @json((int) (Auth::user()->first_login ?? 1) === 0);
            if (!shouldShowTour) {
                return;
            }

            const steps = [
                { id: 'buyer-welcome', text: 'Welcome to your Buyer Dashboard. This tour explains each sidebar item.' },
                { id: 'buyer-home', attachTo: { element: '#buyer-tour-home', on: 'right' }, text: 'Home takes you back to the public marketplace.' },
                { id: 'buyer-dashboard', attachTo: { element: '#buyer-tour-dashboard', on: 'right' }, text: 'Dashboard is your overview of bids, wins, and activity.' },
                { id: 'buyer-profile', attachTo: { element: '#buyer-tour-profile', on: 'right' }, text: 'Account Profile lets you update your details and password.' },
                { id: 'buyer-purchases', attachTo: { element: '#buyer-tour-purchases', on: 'right' }, text: 'Past Purchases shows invoices and items you won.' },
                { id: 'buyer-messaging', attachTo: { element: '#buyer-tour-messaging', on: 'right' }, text: 'Messaging lets you communicate with sellers and support.' },
                {
                    id: 'buyer-bids',
                    attachTo: { element: '#buyer-tour-bids', on: 'right' },
                    text: 'Bids and Watchlist keeps track of your active bids and saved items.',
                    when: {
                        show: () => {
                            const submenu = document.getElementById('bids-submenu');
                            const navItem = submenu ? submenu.closest('.nav-item') : null;
                            if (submenu && navItem) {
                                navItem.classList.add('expanded');
                                submenu.style.display = 'block';
                            }
                        },
                    },
                },
                { id: 'buyer-escrow', attachTo: { element: '#buyer-tour-escrow', on: 'right' }, text: 'Payment / Escrow tracks payment status for your wins.' },
                { id: 'buyer-notifications', attachTo: { element: '#buyer-tour-notifications', on: 'right' }, text: 'Notifications keeps you updated on bids, invoices, and messages.' },
                { id: 'buyer-logout', attachTo: { element: '#buyer-tour-logout', on: 'right' }, text: 'Log out when you are done.' },
            ];

            const tour = new Shepherd.Tour({
                defaultStepOptions: {
                    scrollTo: { behavior: 'smooth', block: 'center' },
                    cancelIcon: { enabled: true },
                },
                useModalOverlay: true,
            });

            steps.forEach((step, index) => {
                if (step.attachTo && !document.querySelector(step.attachTo.element)) {
                    return;
                }
                tour.addStep({
                    id: step.id,
                    text: step.text,
                    attachTo: step.attachTo,
                    buttons: [
                        ...(index === 0 ? [] : [{ text: 'Back', action: tour.back }]),
                        {
                            text: index === steps.length - 1 ? 'Finish' : 'Next',
                            action: index === steps.length - 1 ? tour.complete : tour.next,
                        },
                        { text: 'Skip', action: tour.cancel },
                    ],
                    when: step.when,
                });
            });

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
                    body: JSON.stringify({ role: 'buyer' }),
                }).catch(() => {});
            };

            tour.on('complete', markFirstLogin);
            tour.on('cancel', markFirstLogin);
            tour.start();
        });
    </script>

    {{-- Optional: Place Shepherd.js tour starter here --}}
    @stack('scripts')
</body>

</html>
