@php
    $user = Auth::user();
    $role = $user->role ?? 'buyer';
    $currentRoute = request()->route()->getName();
    
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
            ['route' => 'dashboard.seller', 'icon' => 'dashboard', 'label' => 'Dashboard', 'tab' => 'dashboard'],
            ['route' => 'dashboard.seller', 'icon' => 'person', 'label' => 'User', 'tab' => 'user'],
            ['route' => 'seller.listings.create', 'icon' => 'add_box', 'label' => 'Submission'],
            ['route' => 'dashboard.seller', 'icon' => 'gavel', 'label' => 'Auctions', 'tab' => 'auctions'],
            ['route' => 'dashboard.seller', 'icon' => 'notifications', 'label' => 'Notifications', 'tab' => 'notifications'],
            ['route' => 'seller.chat', 'icon' => 'mail', 'label' => 'Messaging Center'],
            ['route' => 'dashboard.seller', 'icon' => 'support_agent', 'label' => 'Customer Support', 'tab' => 'support'],
            ['route' => 'seller.listings.index', 'icon' => 'directions_car', 'label' => 'My Listings'],
        ];
        $roleLabel = $user->business_license_path ? 'Business Seller' : 'Individual Seller';
        $dashboardRoute = 'dashboard.seller';
    } else {
        // Buyer (unified dashboard like seller - all tabs in one view)
        $menuItems = [
            ['route' => 'welcome', 'icon' => 'home', 'label' => 'Home'],
            ['route' => 'dashboard.buyer', 'icon' => 'dashboard', 'label' => 'Dashboard', 'tab' => 'dashboard'],
            ['route' => 'dashboard.buyer', 'icon' => 'person', 'label' => 'User', 'tab' => 'user'],
            ['route' => 'dashboard.buyer', 'icon' => 'gavel', 'label' => 'Auctions', 'tab' => 'auctions'],
            ['route' => 'dashboard.buyer', 'icon' => 'bookmark', 'label' => 'Saved Items', 'tab' => 'saved'],
            ['route' => 'dashboard.buyer', 'icon' => 'notifications', 'label' => 'Notifications', 'tab' => 'notifications'],
            ['route' => 'dashboard.buyer', 'icon' => 'mail', 'label' => 'Messaging Center', 'tab' => 'messaging'],
            ['route' => 'dashboard.buyer', 'icon' => 'support_agent', 'label' => 'Customer Support', 'tab' => 'support'],
        ];
        $roleLabel = 'Buyer';
        $dashboardRoute = 'dashboard.buyer';
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
        flex-direction: column;
        align-items: center;
        padding: 2rem 1.5rem;
        margin-bottom: 0;
        position: relative;
        text-decoration: none;
        color: inherit;
        background: white;
        border-bottom: 1px solid rgba(0, 0, 0, 0.08);
        flex-shrink: 0;
        transition: all 0.3s ease;
    }
    
    .unified-sidebar .user-profile:hover {
        background: #f8fafc;
    }

    .unified-sidebar .user-profile:hover {
        text-decoration: none;
    }

    .unified-sidebar .avatar {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        object-fit: cover;
        margin-bottom: 1rem;
        border: 4px solid #eef2ff;
        box-shadow: 0 4px 20px rgba(67, 97, 238, 0.15), 0 0 0 4px rgba(255, 255, 255, 0.8);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .unified-sidebar .avatar:hover {
        transform: scale(1.05);
        box-shadow: 0 8px 30px rgba(67, 97, 238, 0.25), 0 0 0 4px rgba(255, 255, 255, 0.9);
        border-color: var(--primary);
    }

    .unified-sidebar .user-name {
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 0.5rem;
        letter-spacing: -0.3px;
    }

    .unified-sidebar .user-role {
        font-size: 0.8rem;
        color: #64748b;
        font-weight: 600;
        background: linear-gradient(135deg, #eef2ff 0%, #f0f9ff 100%);
        padding: 0.4rem 1rem;
        border-radius: 25px;
        border: 1px solid rgba(67, 97, 238, 0.1);
        box-shadow: 0 2px 8px rgba(67, 97, 238, 0.1);
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
        left: -0.5rem;
        top: 50%;
        transform: translateY(-50%);
        height: 60%;
        width: 4px;
        background: linear-gradient(180deg, var(--primary) 0%, #3b82f6 100%);
        border-radius: 0 4px 4px 0;
        box-shadow: 0 0 8px rgba(67, 97, 238, 0.4);
    }

    .unified-sidebar nav a i,
    .unified-sidebar nav a .material-icons,
    .unified-sidebar nav a .material-icons-round {
        margin-right: 14px;
        font-size: 1.4rem;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
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
            padding: 0 0.5rem;
        }

        .unified-sidebar .user-name,
        .unified-sidebar .user-role {
            display: none;
        }

        .unified-sidebar .avatar {
            width: 50px;
            height: 50px;
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
    <div class="logo">
        <div class="logo-dots">
            <div class="logo-dot blue"></div>
            <div class="logo-dot yellow"></div>
            <div class="logo-dot red"></div>
            <div class="logo-dot green"></div>
        </div>
        <h1>
            <span>CayMark</span>
        </h1>
    </div>

    <a href="{{ route('profile.edit') }}" class="user-profile">
        <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuAlZcArzLDu2ar9LMEKLq0Pc00JlKrypvY4o2sWPG7w2mqW-aQNk4bjM2otcK9rpP0L9Y9D6ufd1Hfl8VqG5QxQ70E70W2J9cIesp7CPnk60kNw55FYTZCqay0QTWJtOhG1fSzhpJ9qlyLUFFNstRlPZb2dYFbdpSXxaPvgx3J5yySMRc6c-OZWtIFKK4nU_k4AqY0bECTu42n9S1JfRLaYSB8-anFeAj3KHcMIrFKs8m09OiQcCxnEKa6nxdnCOjWXAmxG1d3hg68"
            alt="User Avatar" class="avatar">
        <h2 class="user-name">{{ Str::ucfirst($user->name) }}</h2>
        <span class="user-role">{{ $roleLabel }}</span>
    </a>

    <nav>
        <ul>
            @foreach($menuItems as $item)
                @php
                    $isActive = false;
                    if ($item['route'] !== '#') {
                        $routeName = $item['route'];
                        // Check if current route matches exactly
                        $isActive = $currentRoute === $routeName;
                        
                        // If tab is specified, check if current tab matches
                        if (isset($item['tab']) && ($currentRoute === 'dashboard.seller' || $currentRoute === 'dashboard.buyer')) {
                            $currentTab = request()->get('tab', 'dashboard');
                            $isActive = $currentTab === $item['tab'];
                        }
                        
                        // Also check for partial matches for nested routes
                        if (!$isActive && !isset($item['tab'])) {
                            $routeParts = explode('.', $routeName);
                            $currentParts = explode('.', $currentRoute);
                            // Check if first parts match (e.g., 'seller.listings' matches 'seller.listings.create')
                            if (count($routeParts) >= 2 && count($currentParts) >= 2) {
                                $isActive = $routeParts[0] === $currentParts[0] && 
                                           $routeParts[1] === $currentParts[1];
                            }
                        }
                    }
                    
                    // Build URL with tab parameter if needed
                    $url = '#';
                    if ($item['route'] !== '#') {
                        $url = route($item['route']);
                        if (isset($item['tab'])) {
                            $url .= '?tab=' . $item['tab'];
                        }
                    }
                @endphp
                <li>
                    <a href="{{ $url }}" 
                       class="{{ $isActive ? 'active' : '' }} relative"
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
            <button type="submit" class="logout-btn" onclick="event.preventDefault(); this.closest('form').submit();">
                <span class="material-icons-round">logout</span>
                <span>Log out</span>
            </button>
        </form>
    </div>
</aside>

<script>
    // Ensure active state persists on page load
    document.addEventListener('DOMContentLoaded', function() {
        const currentRoute = '{{ $currentRoute }}';
        document.querySelectorAll('.unified-sidebar nav a').forEach(link => {
            const href = link.getAttribute('href');
            if (href && href !== '#') {
                // Remove active class from all
                link.classList.remove('active');
            }
        });
        
        // Add active class based on current route
        document.querySelectorAll('.unified-sidebar nav a').forEach(link => {
            if (link.classList.contains('active')) {
                // Already set by PHP, keep it
                return;
            }
        });
    });

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
