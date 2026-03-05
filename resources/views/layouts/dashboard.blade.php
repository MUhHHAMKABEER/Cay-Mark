<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    @include('partials.noindex-meta')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CayMark Dashboard')</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet" />
    <link rel="stylesheet" href="https://unpkg.com/shepherd.js/dist/css/shepherd.css" />
    <style>
        /* CayMark First-Login Guided Tour — professional UI */
        .shepherd-element.caymark-tour { max-width: 420px; border-radius: 20px; box-shadow: 0 25px 80px rgba(0,0,0,0.18), 0 0 0 1px rgba(255,255,255,0.5); overflow: hidden; }
        .shepherd-element.caymark-tour .shepherd-content { padding: 0; border-radius: 20px; }
        .shepherd-element.caymark-tour .shepherd-header { padding: 20px 24px 12px; background: linear-gradient(135deg, #1e3a8a 0%, #3730a3 100%); color: #fff; }
        .shepherd-element.caymark-tour .shepherd-cancel-icon { color: rgba(255,255,255,0.9); font-size: 1.5rem; }
        .shepherd-element.caymark-tour .shepherd-cancel-icon:hover { color: #fff; }
        .shepherd-element.caymark-tour .caymark-tour-progress { height: 4px; background: rgba(255,255,255,0.3); margin-top: 12px; border-radius: 2px; overflow: hidden; }
        .shepherd-element.caymark-tour .caymark-tour-progress-bar { height: 100%; background: linear-gradient(90deg, #a5b4fc, #c7d2fe); border-radius: 2px; transition: width 0.35s ease; }
        .shepherd-element.caymark-tour .shepherd-text { padding: 24px 24px 20px; font-size: 1.05rem; line-height: 1.6; color: #334155; }
        .shepherd-element.caymark-tour .shepherd-footer { padding: 0 24px 24px; display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
        .shepherd-element.caymark-tour .shepherd-footer .shepherd-buttons { display: flex; gap: 10px; margin-left: auto; }
        .shepherd-element.caymark-tour .shepherd-button { padding: 10px 20px; border-radius: 10px; font-weight: 600; font-size: 0.95rem; transition: transform 0.2s, box-shadow 0.2s; }
        .shepherd-element.caymark-tour .shepherd-button:not(:disabled):hover { transform: translateY(-1px); }
        .shepherd-element.caymark-tour .shepherd-button-primary { background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); color: #fff; border: none; box-shadow: 0 4px 14px rgba(37,99,235,0.4); }
        .shepherd-element.caymark-tour .shepherd-button-secondary { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
        .shepherd-element.caymark-tour .shepherd-modal-overlay { background: rgba(15,23,42,0.6); }
        .caymark-tour-step-label { font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.08em; opacity: 0.9; margin-bottom: 4px; }
    </style>
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
                { id: 'buyer-welcome', title: 'Welcome to CayMark', text: 'Your dashboard is the command center for bidding, purchases, and messages. This short tour will show you where everything is.' },
                { id: 'buyer-home', selector: '[data-tour-id="buyer-home"]', title: 'Home', text: 'Return to the public marketplace to browse and search all auctions.' },
                { id: 'buyer-dashboard', selector: '[data-tour-id="buyer-dashboard"]', title: 'Dashboard', text: 'Your overview of active bids, won items, watchlist, and pending payments at a glance.' },
                { id: 'buyer-account-settings', selector: '[data-tour-id="buyer-account-settings"]', title: 'Account settings', text: 'Update your profile, password, and payment preferences.' },
                { id: 'buyer-auctions', selector: '[data-tour-id="buyer-auctions"]', title: 'Auctions', text: 'View live and past auctions you\'re following or have bid on.' },
                { id: 'buyer-saved-items', selector: '[data-tour-id="buyer-saved-items"]', title: 'Saved Items', text: 'Your watchlist — save listings to bid on later.' },
                { id: 'buyer-notifications', selector: '[data-tour-id="buyer-notifications"]', title: 'Notifications', text: 'Alerts for outbid, wins, payment reminders, and pickup updates.' },
                { id: 'buyer-messaging-center', selector: '[data-tour-id="buyer-messaging-center"]', title: 'Messaging Center', text: 'Chat with sellers and support. Use this after winning an item.' },
                { id: 'buyer-customer-support', selector: '[data-tour-id="buyer-customer-support"]', title: 'Customer Support', text: 'Get help, open tickets, and access FAQs.' },
                { id: 'buyer-logout', selector: '[data-tour-id="buyer-logout"]', title: 'You\'re all set', text: 'Log out when you\'re done. You can revisit your dashboard anytime from the menu.' },
            ];

            const sellerSteps = [
                { id: 'seller-welcome', title: 'Welcome to CayMark', text: 'Your seller dashboard is where you manage listings, auctions, payouts, and buyer communication. This short guide walks you through each part of the sidebar so you can get started quickly.' },
                { id: 'seller-dashboard', selector: '[data-tour-id="seller-dashboard"]', title: 'Dashboard', text: 'Your sales summary, active listings, and payout status in one place.' },
                { id: 'seller-account-settings', selector: '[data-tour-id="seller-account-settings"]', title: 'Account settings', text: 'Manage your profile, payout method, and business details.' },
                { id: 'seller-submission', selector: '[data-tour-id="seller-submission"]', title: 'Submission', text: 'Create a new listing — add vehicle details, photos, and auction settings.' },
                { id: 'seller-auctions', selector: '[data-tour-id="seller-auctions"]', title: 'Auctions', text: 'Track active and completed auctions and view bids.' },
                { id: 'seller-notifications', selector: '[data-tour-id="seller-notifications"]', title: 'Notifications', text: 'Alerts for new bids, sales, and buyer messages.' },
                { id: 'seller-messaging-center', selector: '[data-tour-id="seller-messaging-center"]', title: 'Messaging Center', text: 'Communicate with buyers and coordinate pickup after a sale.' },
                { id: 'seller-customer-support', selector: '[data-tour-id="seller-customer-support"]', title: 'Customer Support', text: 'Get help and submit support tickets.' },
                { id: 'seller-logout', selector: '[data-tour-id="seller-logout"]', title: 'You\'re all set', text: 'Log out when you\'re done. Your listings and sales are always available from the dashboard.' },
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
                const totalSteps = steps.length;
                const tour = new Shepherd.Tour({
                    defaultStepOptions: {
                        scrollTo: { behavior: 'smooth', block: 'center' },
                        cancelIcon: { enabled: true },
                        classes: 'caymark-tour',
                    },
                    useModalOverlay: true,
                });

                steps.forEach((step, index) => {
                    const hasSelector = !step.selector || document.querySelector(step.selector);
                    if (step.selector && !document.querySelector(step.selector)) {
                        return;
                    }
                    const stepNumber = index + 1;
                    const stepLabel = step.title ? (step.title + ' · ' + stepNumber + ' of ' + totalSteps) : (stepNumber + ' of ' + totalSteps);
                    tour.addStep({
                        id: step.id,
                        title: stepLabel,
                        text: step.text,
                        attachTo: step.selector ? { element: step.selector, on: 'right' } : undefined,
                        classes: 'caymark-tour',
                        buttons: [
                            ...(index === 0 ? [] : [{ text: 'Back', classes: 'shepherd-button-secondary', action: tour.back }]),
                            {
                                text: index === totalSteps - 1 ? 'Finish tour' : 'Next',
                                classes: 'shepherd-button-primary',
                                action: index === totalSteps - 1 ? tour.complete : tour.next,
                            },
                            { text: 'Skip tour', classes: 'shepherd-button-secondary', action: tour.cancel },
                        ],
                        when: {
                            show: function() {
                                const el = document.querySelector('.shepherd-element.caymark-tour');
                                if (!el) return;
                                let progressWrap = el.querySelector('.caymark-tour-progress');
                                if (!progressWrap) {
                                    progressWrap = document.createElement('div');
                                    progressWrap.className = 'caymark-tour-progress';
                                    const bar = document.createElement('div');
                                    bar.className = 'caymark-tour-progress-bar';
                                    progressWrap.appendChild(bar);
                                    const header = el.querySelector('.shepherd-header');
                                    if (header) header.appendChild(progressWrap);
                                }
                                const bar = progressWrap.querySelector('.caymark-tour-progress-bar');
                                if (bar) bar.style.width = (stepNumber / totalSteps * 100) + '%';
                            }
                        }
                    });
                });

                tour.on('complete', markFirstLogin);
                tour.on('cancel', markFirstLogin);

                setTimeout(function() { tour.start(); }, 400);
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


