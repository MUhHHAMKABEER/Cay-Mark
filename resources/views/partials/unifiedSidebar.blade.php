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
            ['route' => 'dashboard.seller', 'icon' => 'dashboard', 'label' => 'Dashboard'],
            ['route' => 'profile.edit', 'icon' => 'manage_accounts', 'label' => 'Account Settings'],
            ['route' => 'seller.listings.create', 'icon' => 'add_box', 'label' => 'Submit a Listing'],
            ['route' => 'seller.listings.index', 'icon' => 'directions_car', 'label' => 'My Listings'],
            ['route' => 'seller.Auction.index', 'icon' => 'gavel', 'label' => 'Auctions'],
            ['route' => 'seller.chat', 'icon' => 'mail', 'label' => 'Messaging Center'],
            ['route' => '#', 'icon' => 'account_balance', 'label' => 'Payout Settings'],
        ];
        $roleLabel = $user->business_license_path ? 'Business Seller' : 'Individual Seller';
        $dashboardRoute = 'dashboard.seller';
    } else {
        // Buyer
        $menuItems = [
            ['route' => 'dashboard.buyer', 'icon' => 'dashboard', 'label' => 'Dashboard'],
            ['route' => 'profile.edit', 'icon' => 'person', 'label' => 'Account Profile'],
            ['route' => 'buyer.purchases', 'icon' => 'shopping_cart', 'label' => 'Past Purchases'],
            ['route' => 'buyer.messages', 'icon' => 'chat', 'label' => 'Messaging'],
            ['route' => 'buyer.bids', 'icon' => 'how_to_vote', 'label' => 'Bids & Watchlist'],
            ['route' => 'buyer.escrow', 'icon' => 'payments', 'label' => 'Payment / Escrow'],
            ['route' => 'buyer.notifications', 'icon' => 'notifications', 'label' => 'Notifications'],
        ];
        $roleLabel = 'Buyer';
        $dashboardRoute = 'dashboard.buyer';
    }
@endphp

<aside class="unified-sidebar">
    <div class="logo">
        <h1>
            <i class="material-icons">dashboard</i>
            <span>CayMark</span>
        </h1>
    </div>

    <a href="{{ route('profile.edit') }}">
        <div class="user-profile">
            <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuAlZcArzLDu2ar9LMEKLq0Pc00JlKrypvY4o2sWPG7w2mqW-aQNk4bjM2otcK9rpP0L9Y9D6ufd1Hfl8VqG5QxQ70E70W2J9cIesp7CPnk60kNw55FYTZCqay0QTWJtOhG1fSzhpJ9qlyLUFFNstRlPZb2dYFbdpSXxaPvgx3J5yySMRc6c-OZWtIFKK4nU_k4AqY0bECTu42n9S1JfRLaYSB8-anFeAj3KHcMIrFKs8m09OiQcCxnEKa6nxdnCOjWXAmxG1d3hg68"
                alt="User Avatar" class="avatar">
            <h2 class="user-name">{{ Str::ucfirst($user->name) }}</h2>
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
                        // Check if current route matches exactly or contains the route name
                        $isActive = $currentRoute === $routeName || 
                                   str_contains($currentRoute, str_replace('.', '-', $routeName)) ||
                                   str_contains($currentRoute, str_replace('.', '_', $routeName));
                    }
                @endphp
                <li>
                    <a href="{{ $item['route'] === '#' ? '#' : route($item['route']) }}" 
                       class="{{ $isActive ? 'active' : '' }}"
                       @if($item['route'] === '#') onclick="return false;" @endif>
                        <i class="material-icons">{{ $item['icon'] }}</i>
                        <span>{{ $item['label'] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </nav>

    <div class="logout-section">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-btn" onclick="event.preventDefault(); this.closest('form').submit();">
                <i class="material-icons">logout</i>
                <span>Log out</span>
            </button>
        </form>
    </div>
</aside>

<script>
    // Add active class to clicked nav item
    document.querySelectorAll('.unified-sidebar nav a').forEach(link => {
        link.addEventListener('click', function(e) {
            if (this.getAttribute('href') !== '#') {
                document.querySelectorAll('.unified-sidebar nav a').forEach(item => {
                    item.classList.remove('active');
                });
                this.classList.add('active');
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

