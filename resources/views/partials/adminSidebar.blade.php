<!-- Google Fonts & Material Icons -->
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">

<!-- Sidebar Styles -->
<style>
    body {
        font-family: 'Roboto', sans-serif;
        margin: 0;
        background: #f4f6f9;
    }

    .sidebar {
        width: 260px;
        background: #1e293b;
        color: #fff;
        display: flex;
        flex-direction: column;
        height: 100vh;
        position: fixed;
        top: 0;
        left: 0;
        box-shadow: 2px 0 8px rgba(0,0,0,0.2);
        padding: 20px 15px;
    }

    .logo-container {
        display: flex;
        align-items: center;
        margin-bottom: 25px;
    }

    .logo-dots {
        display: flex;
        gap: 5px;
    }

    .logo-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
    }

    .logo-dot.blue { background: #3b82f6; }
    .logo-dot.yellow { background: #facc15; }
    .logo-dot.red { background: #ef4444; }
    .logo-dot.green { background: #22c55e; }

    .logo-text {
        margin-left: 10px;
        font-size: 1.2rem;
        font-weight: bold;
        color: #f1f5f9;
    }

    .user-profile {
        text-align: center;
        margin-bottom: 30px;
    }

    .avatar {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        margin-bottom: 10px;
        border: 2px solid #fff;
    }

    .user-name {
        font-size: 1.1rem;
        font-weight: 600;
        margin: 0;
    }

    .user-role {
        font-size: 0.9rem;
        color: #9ca3af;
    }

    .nav-menu {
        flex-grow: 1;
    }

    .nav-link {
        display: flex;
        align-items: center;
        padding: 10px 12px;
        border-radius: 8px;
        color: #cbd5e1;
        text-decoration: none;
        transition: background 0.2s, color 0.2s;
    }

    .nav-link:hover {
        background: #334155;
        color: #fff;
    }

    .nav-link.active {
        background: #3b82f6;
        color: #fff;
    }

    .nav-icon {
        margin-right: 12px;
        font-size: 22px;
    }

    .logout-btn {
        background: none;
        border: none;
        display: flex;
        align-items: center;
        width: 100%;
        color: #f87171;
        padding: 10px 12px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 1rem;
    }

    .logout-btn:hover {
        background: #991b1b;
        color: #fff;
    }

    .tooltip {
        position: relative;
    }

    .tooltip[data-tooltip]:hover::after {
        content: attr(data-tooltip);
        position: absolute;
        left: 100%;
        top: 50%;
        transform: translateY(-50%);
        background: #111827;
        color: #fff;
        padding: 4px 8px;
        font-size: 0.75rem;
        border-radius: 4px;
        white-space: nowrap;
        margin-left: 8px;
    }
</style>

<!-- Sidebar -->
<aside class="sidebar">
    <!-- Logo -->
    <div class="logo-container">
        <div class="logo-dots">
            <div class="logo-dot blue"></div>
            <div class="logo-dot yellow"></div>
            <div class="logo-dot red"></div>
            <div class="logo-dot green"></div>
        </div>
        <span class="logo-text">Admin</span>
    </div>

    <!-- User Profile -->
    <a href="{{ route('profile.edit') }}">
        <div class="user-profile">
            <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuAlZcArzLDu2ar9LMEKLq0Pc00JlKrypvY4o2sWPG7w2mqW-aQNk4bjM2otcK9rpP0L9Y9D6ufd1Hfl8VqG5QxQ70E70W2J9cIesp7CPnk60kNw55FYTZCqay0QTWJtOhG1fSzhpJ9qlyLUFFNstRlPZb2dYFbdpSXxaPvgx3J5yySMRc6c-OZWtIFKK4nU_k4AqY0bECTu42n9S1JfRLaYSB8-anFeAj3KHcMIrFKs8m09OiQcCxnEKa6nxdnCOjWXAmxG1d3hg68"
                 alt="Admin Avatar" class="avatar">
            <h2 class="user-name">{{ Str::ucfirst(Auth::user()->name) }}</h2>
            <span class="user-role">Administrator</span>
        </div>
    </a>

    <!-- Navigation -->
    <nav class="nav-menu">
        @php $currentRoute = request()->route()->getName(); @endphp
        <ul class="nav flex flex-col space-y-2">
            <li class="nav-item">
                <a href="{{ route('admin.dashboard') }}" 
                   class="nav-link tooltip {{ str_contains($currentRoute, 'dashboard') ? 'active' : '' }}" 
                   data-tooltip="Dashboard">
                    <span class="material-icons-round nav-icon">dashboard</span>
                    <span>Dashboard</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.users') }}" 
                   class="nav-link tooltip {{ str_contains($currentRoute, 'users') ? 'active' : '' }}" 
                   data-tooltip="User Management">
                    <span class="material-icons-round nav-icon">people</span>
                    <span>User Management</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.memberships') }}" 
                   class="nav-link tooltip {{ str_contains($currentRoute, 'memberships') ? 'active' : '' }}" 
                   data-tooltip="Memberships">
                    <span class="material-icons-round nav-icon">card_membership</span>
                    <span>Memberships</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.show.listing') }}" 
                   class="nav-link tooltip {{ str_contains($currentRoute, 'listing-review') ? 'active' : '' }}" 
                   data-tooltip="Listing Review">
                    <span class="material-icons-round nav-icon">fact_check</span>
                    <span>Listing Review</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.active-listings') }}" 
                   class="nav-link tooltip {{ str_contains($currentRoute, 'active-listings') ? 'active' : '' }}" 
                   data-tooltip="Active Listings">
                    <span class="material-icons-round nav-icon">directions_car</span>
                    <span>Active Listings</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.boosts-addons') }}" 
                   class="nav-link tooltip {{ str_contains($currentRoute, 'boosts-addons') ? 'active' : '' }}" 
                   data-tooltip="Boosts & Add-ons">
                    <span class="material-icons-round nav-icon">rocket_launch</span>
                    <span>Boosts & Add-ons</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.payments') }}" 
                   class="nav-link tooltip {{ str_contains($currentRoute, 'payments') ? 'active' : '' }}" 
                   data-tooltip="Payments">
                    <span class="material-icons-round nav-icon">account_balance_wallet</span>
                    <span>Payments</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.disputes') }}" 
                   class="nav-link tooltip {{ str_contains($currentRoute, 'disputes') ? 'active' : '' }}" 
                   data-tooltip="Disputes Center">
                    <span class="material-icons-round nav-icon">gavel</span>
                    <span>Disputes Center</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.notifications') }}" 
                   class="nav-link tooltip {{ str_contains($currentRoute, 'notifications') ? 'active' : '' }}" 
                   data-tooltip="Notifications">
                    <span class="material-icons-round nav-icon">notifications</span>
                    <span>Notifications</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.reports-analytics') }}" 
                   class="nav-link tooltip {{ str_contains($currentRoute, 'reports-analytics') ? 'active' : '' }}" 
                   data-tooltip="Reports & Analytics">
                    <span class="material-icons-round nav-icon">bar_chart</span>
                    <span>Reports & Analytics</span>
                </a>
            </li>
        </ul>
    </nav>

    <!-- Logout -->
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="logout-btn tooltip" data-tooltip="Logout"
                onclick="event.preventDefault(); this.closest('form').submit();">
            <span class="material-icons-round nav-icon">logout</span>
            <span>Log out</span>
        </button>
    </form>
</aside>
