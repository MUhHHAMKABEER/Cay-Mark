@php
    $user = Auth::user();
    $currentRoute = request()->route()->getName();
    
    $menuItems = [
        ['route' => 'admin.dashboard', 'icon' => 'dashboard', 'label' => 'Dashboard'],
        ['route' => 'admin.users', 'icon' => 'people', 'label' => 'User Management'],
        ['route' => 'admin.memberships', 'icon' => 'card_membership', 'label' => 'Memberships'],
        ['route' => 'admin.listing-review', 'icon' => 'fact_check', 'label' => 'Listing Review'],
        ['route' => 'admin.active-listings', 'icon' => 'directions_car', 'label' => 'Active Listings'],
        ['route' => 'admin.boosts-addons', 'icon' => 'rocket_launch', 'label' => 'Boosts & Add-ons'],
        ['route' => 'admin.payments', 'icon' => 'account_balance_wallet', 'label' => 'Payments'],
        ['route' => 'admin.disputes', 'icon' => 'gavel', 'label' => 'Disputes Center'],
        ['route' => 'admin.notifications', 'icon' => 'notifications', 'label' => 'Notifications'],
        ['route' => 'admin.reports-analytics', 'icon' => 'bar_chart', 'label' => 'Reports & Analytics'],
    ];
@endphp

<link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">

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
        background: var(--light);
    }

    .unified-sidebar .user-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid var(--primary-light);
        margin-bottom: 1rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .unified-sidebar .user-name {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--dark);
        margin: 0 0 0.25rem 0;
    }

    .unified-sidebar .user-role {
        font-size: 0.875rem;
        color: var(--gray);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .unified-sidebar nav {
        flex: 1;
        overflow-y: auto;
        overflow-x: hidden;
        padding: 1rem 0;
        background: white;
    }

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
        padding: 0;
        margin: 0;
    }

    .unified-sidebar nav ul li {
        margin: 0;
    }

    .unified-sidebar .nav-link {
        display: flex;
        align-items: center;
        padding: 0.875rem 1.5rem;
        color: var(--dark);
        text-decoration: none;
        transition: all 0.2s ease;
        position: relative;
        font-size: 0.95rem;
        font-weight: 500;
    }

    .unified-sidebar .nav-link:hover {
        background: var(--primary-light);
        color: var(--primary);
    }

    .unified-sidebar .nav-link.active {
        background: linear-gradient(90deg, var(--primary-light) 0%, rgba(67, 97, 238, 0.05) 100%);
        color: var(--primary);
        border-left: 3px solid var(--primary);
        font-weight: 600;
    }

    .unified-sidebar .nav-link.active::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 3px;
        background: var(--primary);
    }

    .unified-sidebar .nav-icon {
        margin-right: 1rem;
        font-size: 1.5rem;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .unified-sidebar .logout-section {
        padding: 1rem 1.5rem;
        border-top: 1px solid rgba(0, 0, 0, 0.08);
        background: white;
        flex-shrink: 0;
    }

    .unified-sidebar .logout-btn {
        display: flex;
        align-items: center;
        width: 100%;
        padding: 0.875rem 1.5rem;
        background: none;
        border: none;
        color: var(--danger);
        text-decoration: none;
        transition: all 0.2s ease;
        font-size: 0.95rem;
        font-weight: 500;
        cursor: pointer;
        border-radius: 8px;
    }

    .unified-sidebar .logout-btn:hover {
        background: rgba(239, 68, 68, 0.1);
        color: var(--danger);
    }

    .unified-sidebar .logout-icon {
        margin-right: 1rem;
        font-size: 1.5rem;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>

<aside class="unified-sidebar">
    <!-- Logo -->
    <div class="logo">
        <div class="logo-dots">
            <div class="logo-dot blue"></div>
            <div class="logo-dot yellow"></div>
            <div class="logo-dot red"></div>
            <div class="logo-dot green"></div>
        </div>
        <h1>Admin</h1>
    </div>

    <!-- User Profile -->
    <a href="{{ route('profile.edit') }}" class="user-profile">
        <img src="{{ $user->avatar_url ?? 'https://lh3.googleusercontent.com/a/default-user' }}" 
             alt="{{ $user->name }}" 
             class="user-avatar">
        <span class="user-name">{{ Str::ucfirst($user->name) }}</span>
        <span class="user-role">Administrator</span>
    </a>

    <!-- Navigation Menu with Scrollbar -->
    <nav>
        <ul>
            @foreach($menuItems as $item)
                @php
                    $isActive = false;
                    if ($item['route'] !== '#') {
                        $routeName = $item['route'];
                        $isActive = $currentRoute === $routeName;
                        
                        // Check for partial matches for nested routes
                        if (!$isActive) {
                            $routeParts = explode('.', $routeName);
                            $currentParts = explode('.', $currentRoute);
                            if (count($routeParts) >= 2 && count($currentParts) >= 2) {
                                $isActive = $routeParts[0] === $currentParts[0] && 
                                           $routeParts[1] === $currentParts[1];
                            }
                        }
                    }
                    
                    $url = $item['route'] !== '#' ? route($item['route']) : '#';
                @endphp
                <li>
                    <a href="{{ $url }}" class="nav-link {{ $isActive ? 'active' : '' }}">
                        <span class="material-icons-round nav-icon">{{ $item['icon'] }}</span>
                        <span>{{ $item['label'] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </nav>

    <!-- Logout Section -->
    <div class="logout-section">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-btn">
                <span class="material-icons-round logout-icon">logout</span>
                <span>Log out</span>
            </button>
        </form>
    </div>
</aside>
