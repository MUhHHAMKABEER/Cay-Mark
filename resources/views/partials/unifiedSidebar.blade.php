@php
    $user = Auth::user();
    $role = $user->role ?? 'buyer';
    $currentRoute = request()->route()?->getName() ?? '';
    
    // Define menu items for each role
    $menuItems = [];
    
    if ($role === 'admin') {
        $menuItems = [
            ['route' => 'admin.dashboard', 'icon' => 'dashboard', 'label' => 'Dashboard'],
            ['route' => 'admin.users', 'icon' => 'people', 'label' => 'User Management'],
            ['route' => 'admin.memberships', 'icon' => 'card_membership', 'label' => 'Memberships'],
            ['route' => 'admin.show.listing', 'icon' => 'fact_check', 'label' => 'Listing Review'],
            ['route' => 'admin.active-listings', 'icon' => 'directions_car', 'label' => 'Active Listings'],
            ['route' => 'admin.boosts-addons', 'icon' => 'rocket_launch', 'label' => 'Boosts & Add-ons'],
            ['route' => 'admin.payments', 'icon' => 'account_balance_wallet', 'label' => 'Payments'],
            ['route' => 'admin.disputes', 'icon' => 'gavel', 'label' => 'Disputes Center'],
            ['route' => 'admin.notifications', 'icon' => 'notifications', 'label' => 'Notifications'],
            ['route' => 'admin.reports-analytics', 'icon' => 'bar_chart', 'label' => 'Reports & Analytics'],
        ];
        $roleLabel = 'Administrator';
        $dashboardRoute = 'admin.dashboard';
    } elseif ($role === 'seller') {
        $menuItems = [
            ['route' => 'welcome', 'icon' => 'home', 'label' => 'Home'],
            ['route' => 'seller.dashboard', 'icon' => 'dashboard', 'label' => 'Dashboard'],
            ['route' => 'seller.account', 'icon' => 'person', 'label' => 'Account settings'],
            ['route' => 'seller.listings.create', 'icon' => 'add_box', 'label' => 'Submission', 'match_routes' => ['seller.listings.edit', 'seller.listings.update', 'seller.listings.success', 'seller.listings.store'], 'prefix_match' => false],
            ['route' => 'seller.auctions', 'icon' => 'gavel', 'label' => 'Auctions', 'match_routes' => ['seller.listings.show']],
            ['route' => 'seller.notifications', 'icon' => 'notifications', 'label' => 'Notifications'],
            ['route' => 'seller.chat', 'icon' => 'mail', 'label' => 'Messaging Center', 'match_routes' => ['seller.chat.show', 'seller.chat.message']],
            ['route' => 'seller.support', 'icon' => 'support_agent', 'label' => 'Customer Support'],
        ];
        $roleLabel = $user->business_license_path ? 'Business Seller' : 'Individual Seller';
        $dashboardRoute = 'seller.dashboard';
    } else {
        $menuItems = [
            ['route' => 'welcome', 'icon' => 'home', 'label' => 'Home'],
            ['route' => 'buyer.dashboard', 'icon' => 'dashboard', 'label' => 'Dashboard'],
            ['route' => 'buyer.user', 'icon' => 'person', 'label' => 'Account settings', 'match_routes' => ['buyer.profile']],
            ['route' => 'buyer.auctions', 'icon' => 'gavel', 'label' => 'Auctions'],
            ['route' => 'buyer.saved-items', 'icon' => 'bookmark', 'label' => 'Saved Items', 'match_routes' => ['buyer.watchlist', 'watchlist.index']],
            ['route' => 'buyer.notifications', 'icon' => 'notifications', 'label' => 'Notifications'],
            ['route' => 'buyer.messaging-center', 'icon' => 'mail', 'label' => 'Messaging Center', 'match_routes' => ['buyer.messages']],
            ['route' => 'buyer.customer-support', 'icon' => 'support_agent', 'label' => 'Customer Support'],
        ];
        $roleLabel = 'Buyer';
        $dashboardRoute = 'buyer.dashboard';
    }
@endphp

<style>
    :root {
        --primary: #4361ee;
        --primary-light: #eef2ff;
        --dark: #1e293b;
        --light: #f8fafc;
        --gray: #94a3b8;
        --danger: #ef4444;
    }

    .unified-sidebar {
        width: 280px;
        height: 100vh;
        background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        box-shadow: 2px 0 20px rgba(0, 0, 0, 0.08);
        padding: 0;
        display: flex;
        flex-direction: column;
        position: fixed;
        left: 0;
        top: 0;
        z-index: 100;
        transition: all 0.3s ease;
        border-right: 1px solid rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }

    .unified-sidebar:hover {
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    }

    .unified-sidebar .logo {
        display: flex;
        align-items: center;
        padding: 2rem 1.5rem;
        margin-bottom: 0;
        border-bottom: 1px solid rgba(0, 0, 0, 0.08);
        background: white;
        flex-shrink: 0;
    }

    .unified-sidebar .logo h1 {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--dark);
        letter-spacing: -0.5px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .unified-sidebar .logo-dots {
        width: 32px;
        height: 32px;
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 4px;
    }

    .unified-sidebar .logo-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
    }

    .unified-sidebar .logo-dot.blue {
        background-color: var(--primary);
    }

    .unified-sidebar .logo-dot.yellow {
        background-color: #f59e0b;
    }

    .unified-sidebar .logo-dot.red {
        background-color: var(--danger);
    }

    .unified-sidebar .logo-dot.green {
        background-color: #10b981;
    }

    .unified-sidebar .user-profile {
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 0.875rem;
        padding: 1.125rem 1.25rem;
        margin-bottom: 0;
        position: relative;
        text-decoration: none;
        color: inherit;
        background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        border-bottom: 1px solid rgba(0, 0, 0, 0.06);
        flex-shrink: 0;
        transition: background 0.2s ease, box-shadow 0.2s ease;
    }

    .unified-sidebar .user-profile:hover {
        background: #f1f5f9;
        text-decoration: none;
    }

    /* Monogram — no profile photo */
    .unified-sidebar .user-profile-initials {
        flex-shrink: 0;
        width: 2.75rem;
        height: 2.75rem;
        border-radius: 0.625rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8125rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        color: #fff;
        background: linear-gradient(145deg, #063466 0%, #1e40af 50%, #4361ee 100%);
        box-shadow: 0 2px 8px rgba(6, 52, 102, 0.22);
    }

    .unified-sidebar .user-profile:hover .user-profile-initials {
        box-shadow: 0 4px 12px rgba(6, 52, 102, 0.28);
    }

    .unified-sidebar .user-profile-meta {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 0.35rem;
        min-width: 0;
        flex: 1;
    }

    .unified-sidebar .user-name {
        font-size: 0.9375rem;
        font-weight: 600;
        color: #0f172a;
        margin: 0;
        letter-spacing: -0.02em;
        line-height: 1.3;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 100%;
    }

    .unified-sidebar .user-role {
        font-size: 0.625rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #64748b;
        background: #e2e8f0;
        padding: 0.2rem 0.45rem;
        border-radius: 0.25rem;
        border: none;
        box-shadow: none;
    }

    .unified-sidebar nav {
        flex: 1;
        overflow-y: auto;
        overflow-x: hidden;
        padding: 1rem 0;
    }
    
    /* Custom Scrollbar */
    .unified-sidebar nav::-webkit-scrollbar {
        width: 6px;
    }
    
    .unified-sidebar nav::-webkit-scrollbar-track {
        background: transparent;
    }
    
    .unified-sidebar nav::-webkit-scrollbar-thumb {
        background: rgba(0, 0, 0, 0.2);
        border-radius: 10px;
    }
    
    .unified-sidebar nav::-webkit-scrollbar-thumb:hover {
        background: rgba(0, 0, 0, 0.3);
    }

    .unified-sidebar nav ul {
        list-style: none;
        padding: 0 1rem;
        margin: 0;
    }

    .unified-sidebar nav li {
        margin-bottom: 0.5rem;
        position: relative;
    }

    .unified-sidebar nav a {
        display: flex;
        align-items: center;
        padding: 0.875rem 1.25rem;
        border-radius: 12px;
        color: #64748b;
        font-weight: 500;
        font-size: 0.95rem;
        text-decoration: none;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        margin: 0 0.5rem;
        border-left: none;
    }

    .unified-sidebar nav a:hover {
        background: linear-gradient(90deg, #eef2ff 0%, #f0f9ff 100%);
        color: var(--primary);
        transform: translateX(4px);
        box-shadow: 0 2px 8px rgba(67, 97, 238, 0.15);
    }

    .unified-sidebar nav a.active {
        background: linear-gradient(90deg, #eef2ff 0%, #f0f9ff 100%);
        color: var(--primary);
        font-weight: 600;
        box-shadow: 0 2px 12px rgba(67, 97, 238, 0.2);
    }

    .unified-sidebar nav a.active::before {
        content: '';
        position: absolute;
        left: 50%;
        bottom: 0.35rem;
        transform: translateX(-50%);
        height: 3px;
        width: min(70%, 8rem);
        background: linear-gradient(90deg, var(--primary) 0%, #3b82f6 100%);
        border-radius: 999px;
        box-shadow: 0 1px 6px rgba(67, 97, 238, 0.35);
    }

    .unified-sidebar nav a i,
    .unified-sidebar nav a .material-icons,
    .unified-sidebar nav a .material-icons-round {
        margin-right: 14px;
        font-size: 1.4rem;
        width: 24px;
        height: 24px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-family: 'Material Icons Round', 'Material Icons', sans-serif;
        font-weight: normal;
        font-style: normal;
        letter-spacing: normal;
        text-rendering: optimizeLegibility;
        -webkit-font-smoothing: antialiased;
    }
    
    .unified-sidebar nav a span:not(.material-icons):not(.material-icons-round):not(.sidebar-notification-badge) {
        flex: 1;
        white-space: nowrap;
    }
    
    .unified-sidebar .sidebar-notification-badge {
        min-width: 20px;
        height: 20px;
        font-size: 0.7rem;
        line-height: 1;
        padding: 2px 6px;
        box-shadow: 0 2px 4px rgba(239, 68, 68, 0.3);
        display: flex;
    }
    
    .unified-sidebar .sidebar-notification-badge[style*="display: none"] {
        display: none !important;
    }

    .unified-sidebar .logout-section {
        margin-top: auto;
        padding: 1.5rem;
        border-top: 1px solid rgba(0, 0, 0, 0.08);
        background: white;
        flex-shrink: 0;
    }

    .unified-sidebar .logout-btn {
        display: flex;
        align-items: center;
        padding: 0.875rem 1.25rem;
        border-radius: 12px;
        color: #64748b;
        font-weight: 500;
        font-size: 0.95rem;
        text-decoration: none;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background: none;
        border: none;
        cursor: pointer;
        width: 100%;
        margin: 0 0.5rem;
    }

    .unified-sidebar .logout-btn:hover {
        background: linear-gradient(90deg, #fef2f2 0%, #fee2e2 100%);
        color: var(--danger);
        transform: translateX(4px);
        box-shadow: 0 2px 8px rgba(239, 68, 68, 0.15);
    }

    .unified-sidebar .logout-btn i,
    .unified-sidebar .logout-btn .material-icons,
    .unified-sidebar .logout-btn .material-icons-round {
        margin-right: 14px;
        font-size: 1.4rem;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .unified-sidebar {
            width: 80px;
            padding: 0;
            align-items: center;
        }

        .unified-sidebar .logo {
            flex-direction: column;
            align-items: center;
            padding-bottom: 1rem;
        }

        .unified-sidebar .logo h1 span {
            display: none;
        }

        .unified-sidebar .user-profile {
            padding: 0.75rem 0.5rem;
            justify-content: center;
        }

        .unified-sidebar .user-name,
        .unified-sidebar .user-role {
            display: none;
        }

        .unified-sidebar .user-profile-initials {
            width: 2.375rem;
            height: 2.375rem;
            font-size: 0.75rem;
        }

        .unified-sidebar nav a span:not(.material-icons):not(.material-icons-round) {
            display: none;
        }

        .unified-sidebar nav a i,
        .unified-sidebar nav a .material-icons,
        .unified-sidebar nav a .material-icons-round {
            margin-right: 0;
            font-size: 1.5rem;
        }

        .unified-sidebar .logout-btn span:last-child {
            display: none;
        }

        .unified-sidebar .logout-btn i,
        .unified-sidebar .logout-btn .material-icons,
        .unified-sidebar .logout-btn .material-icons-round {
            margin-right: 0;
        }
    }
</style>

<aside class="unified-sidebar">
    <a href="{{ route('welcome') }}" class="logo flex items-center justify-center no-underline">
        <img src="{{ asset(config('logos.sidebar', 'Logos/Caymark Logo.png')) }}" alt="CayMark" class="h-14 w-auto max-w-full object-contain" />
    </a>

    @php
        $displayName = trim((string) ($user->name ?? ''));
        $parts = $displayName !== '' ? preg_split('/\s+/u', $displayName, -1, PREG_SPLIT_NO_EMPTY) : [];
        if (count($parts) >= 2) {
            $userInitials = mb_strtoupper(mb_substr($parts[0], 0, 1) . mb_substr($parts[count($parts) - 1], 0, 1));
        } elseif (count($parts) === 1) {
            $userInitials = mb_strtoupper(mb_substr($parts[0], 0, min(2, mb_strlen($parts[0]))));
        } else {
            $userInitials = '?';
        }
    @endphp
    <a href="{{ route('profile.edit') }}" class="user-profile" title="{{ $displayName ?: 'Account' }} · {{ $roleLabel }}">
        <span class="user-profile-initials" aria-hidden="true">{{ $userInitials }}</span>
        <div class="user-profile-meta">
            <span class="user-name">{{ Str::ucfirst($user->name) }}</span>
            <span class="user-role">{{ $roleLabel }}</span>
        </div>
    </a>

    <nav>
        <ul>
            @foreach($menuItems as $item)
                @php
                    $isActive = false;
                    if ($item['route'] !== '#') {
                        $routeName = $item['route'];
                        $matchRoutes = $item['match_routes'] ?? [];

                        if ($currentRoute !== '' && count($matchRoutes) > 0 && in_array($currentRoute, $matchRoutes, true)) {
                            $isActive = true;
                        }

                        if (!$isActive && !isset($item['tab'])) {
                            $isActive = ($currentRoute === $routeName);
                            if (!$isActive && $currentRoute !== '' && ($item['prefix_match'] ?? true)) {
                                $routeParts = explode('.', $routeName);
                                $currentParts = explode('.', $currentRoute);
                                if (count($routeParts) >= 2 && count($currentParts) >= 2) {
                                    $isActive = $routeParts[0] === $currentParts[0] &&
                                               $routeParts[1] === $currentParts[1];
                                }
                            }
                        }
                    }
                    
                    // Build URL with tab parameter if needed
                    $url = '#';
                    if ($item['route'] !== '#') {
                        $url = route($item['route']);
                    }
                @endphp
                @php
                    $tourId = $role . '-' . \Illuminate\Support\Str::slug($item['label']);
                @endphp
                <li>
                    <a href="{{ $url }}" 
                       class="{{ $isActive ? 'active' : '' }} relative"
                       data-tour-id="{{ $tourId }}"
                       @if($item['route'] === '#') onclick="return false;" @endif>
                        <span class="material-icons-round">{{ $item['icon'] }}</span>
                        <span>{{ $item['label'] }}</span>
                        @if($item['icon'] === 'notifications' && isset($user))
                            @php
                                $unreadCount = $user->unreadNotifications()->count();
                            @endphp
                            @if($unreadCount > 0)
                                <span class="sidebar-notification-badge absolute right-3 top-1/2 -translate-y-1/2 min-w-[20px] h-5 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center px-1.5" style="display: flex;">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
                            @else
                                <span class="sidebar-notification-badge absolute right-3 top-1/2 -translate-y-1/2 min-w-[20px] h-5 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center px-1.5" style="display: none;">0</span>
                            @endif
                        @endif
                    </a>
                </li>
            @endforeach
        </ul>
    </nav>

    <div class="logout-section">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-btn" data-tour-id="{{ $role }}-logout" onclick="event.preventDefault(); this.closest('form').submit();">
                <span class="material-icons-round">logout</span>
                <span>Log out</span>
            </button>
        </form>
    </div>
</aside>

<script>
    // Smooth hover effects
    document.querySelectorAll('.unified-sidebar nav li, .unified-sidebar .logout-btn').forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.style.transform = 'translateX(5px)';
        });
        item.addEventListener('mouseleave', function() {
            this.style.transform = 'translateX(0)';
        });
    });
</script>
