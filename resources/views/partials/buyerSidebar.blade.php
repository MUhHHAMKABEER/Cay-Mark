<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Professional Dashboard</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4361ee;
            --primary-light: #eef2ff;
            --dark: #1e293b;
            --light: #f8fafc;
            --gray: #94a3b8;
            --danger: #ef4444;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
            font-size: 12px;
        }

        body {
            background-color: #f1f5f9;
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 220px;
            height: 100vh;
            background: white;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05);
            padding: 2rem 1.5rem;
            display: flex;
            flex-direction: column;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 100;
            transition: all 0.3s ease;
            border-right: 1px solid rgba(0, 0, 0, 0.05);
        }

        .sidebar:hover {
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }

        .logo-container {
            display: flex;
            align-items: center;
            margin-bottom: 2.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .logo-dots {
            width: 32px;
            height: 32px;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 4px;
            margin-right: 12px;
        }

        .logo-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }

        .logo-dot.blue {
            background-color: var(--primary);
        }

        .logo-dot.yellow {
            background-color: #f59e0b;
        }

        .logo-dot.red {
            background-color: var(--danger);
        }

        .logo-dot.green {
            background-color: #10b981;
        }

        .logo-text {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--dark);
            letter-spacing: -0.5px;
        }

        .user-profile {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 2.5rem;
            position: relative;
        }

        .avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 1rem;
            border: 3px solid white;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .avatar:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .user-name {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 0.25rem;
        }

        .user-role {
            font-size: 0.85rem;
            color: var(--gray);
            font-weight: 500;
            background: var(--primary-light);
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
        }

        .nav-menu {
            flex-grow: 1;
        }

        .nav-item {
            margin-bottom: 0.75rem;
            position: relative;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            color: var(--gray);
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            background: var(--primary-light);
            color: var(--primary);
        }

        .nav-link.active {
            background: var(--primary-light);
            color: var(--primary);
            font-weight: 600;
        }

        .nav-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 3px;
            background: var(--primary);
            border-radius: 0 3px 3px 0;
        }

        .nav-icon {
            margin-right: 12px;
            font-size: 1.25rem;
        }

        .badge {
            margin-left: auto;
            background: var(--danger);
            color: white;
            font-size: 0.7rem;
            font-weight: 600;
            width: 22px;
            height: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        .logout-btn {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            color: var(--gray);
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
            background: none;
            border: none;
            cursor: pointer;
            width: 100%;
        }

        .logout-btn:hover {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
        }

        /* Submenu styles */
        .submenu {
            display: none;
            list-style: none;
            padding-left: 16px;
            margin: 0;
        }

        .submenu-item {
            margin-bottom: 0.25rem;
        }

        .submenu-link {
            display: block;
            padding: 0.5rem 0.75rem;
            text-decoration: none;
            color: var(--gray);
            border-radius: 6px;
            transition: all 0.3s ease;
            font-size: 0.85rem;
        }

        .submenu-link:hover {
            background: rgba(67, 97, 238, 0.05);
            color: var(--primary);
        }

        .nav-arrow {
            transition: transform 0.3s ease;
            margin-left: auto;
        }

        .nav-item.expanded .nav-arrow {
            transform: rotate(90deg);
        }

        /* Tooltip for menu items */
        .tooltip {
            position: relative;
        }

        .tooltip:hover::after {
            content: attr(data-tooltip);
            position: absolute;
            left: 100%;
            top: 50%;
            transform: translateY(-50%);
            background: var(--dark);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-size: 0.85rem;
            white-space: nowrap;
            z-index: 1000;
            margin-left: 10px;
            opacity: 0;
            animation: fadeIn 0.2s forwards;
        }

        @keyframes fadeIn {
            to {
                opacity: 1;
                margin-left: 15px;
            }
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .sidebar {
                width: 80px;
                padding: 1.5rem 0.5rem;
                align-items: center;
            }

            .logo-container {
                flex-direction: column;
                align-items: center;
                padding-bottom: 1rem;
            }

            .logo-dots {
                margin-right: 0;
                margin-bottom: 0.5rem;
            }

            .logo-text {
                display: none;
            }

            .user-profile {
                padding: 0 0.5rem;
            }

            .user-name,
            .user-role {
                display: none;
            }

            .avatar {
                width: 50px;
                height: 50px;
            }

            .nav-link span:not(.nav-icon) {
                display: none;
            }

            .nav-icon {
                margin-right: 0;
                font-size: 1.5rem;
            }

            .badge {
                position: absolute;
                top: 5px;
                right: 5px;
            }

            .logout-btn span:last-child {
                display: none;
            }

            .logout-btn .nav-icon {
                margin-right: 0;
            }

            .sidebar {
                background-color: #F9F1EC;
            }
        }
    </style>
</head>

<body>
    <aside class="sidebar">
        <div class="logo-container">
            <div class="logo-dots">
                <div class="logo-dot blue"></div>
                <div class="logo-dot yellow"></div>
                <div class="logo-dot red"></div>
                <div class="logo-dot green"></div>
            </div>
            <span class="logo-text">Dashboard</span>
        </div>

        <a href="{{ route('profile.edit') }}">
            <div class="user-profile">
                <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuAlZcArzLDu2ar9LMEKLq0Pc00JlKrypvY4o2sWPG7w2mqW-aQNk4bjM2otcK9rpP0L9Y9D6ufd1Hfl8VqG5QxQ70E70W2J9cIesp7CPnk60kNw55FYTZCqay0QTWJtOhG1fSzhpJ9qlyLUFFNstRlPZb2dYFbdpSXxaPvgx3J5yySMRc6c-OZWtIFKK4nU_k4AqY0bECTu42n9S1JfRLaYSB8-anFeAj3KHcMIrFKs8m09OiQcCxnEKa6nxdnCOjWXAmxG1d3hg68"
                    alt="User Avatar" class="avatar">
                <h2 class="user-name">{{ Str::ucfirst(Auth::user()->name) }}</h2>
                <span class="user-role">Marketing Director</span>
            </div>
        </a>

        <nav class="nav-menu">
            <ul>
                <!-- Dashboard -->
                <li class="nav-item"
                    data-intro="This is your main dashboard where you can see an overview of your account, bids, and activities."
                    data-step="1">
                    <a href="{{ route('dashboard.buyer') }}" class="nav-link tooltip active" data-tooltip="Dashboard">
                        <span class="material-icons-round nav-icon">dashboard</span>
                        <span class="nav-text">Dashboard</span>
                    </a>
                </li>

                <!-- Account Profile -->
                <li class="nav-item"
                    data-intro="Update your personal information, change your password, and manage your account settings."
                    data-step="2">
                    <a href="{{ route('profile.edit') }}" class="nav-link tooltip" data-tooltip="Account Profile">
                        <span class="material-icons-round nav-icon">person</span>
                        <span class="nav-text">Account Profile</span>
                    </a>
                </li>

                <!-- Past Purchases -->
                <li class="nav-item"
                    data-intro="View your purchase history and details of vehicles you've successfully bought."
                    data-step="3">
                    <a href="{{ route('buyer.purchases') }}" class="nav-link tooltip" data-tooltip="Past Purchases">
                        <span class="material-icons-round nav-icon">shopping_cart</span>
                        <span class="nav-text">Past Purchases</span>
                    </a>
                </li>

                <li class="nav-item"
                    data-intro="View and manage your messages with sellers and buyers."
                    data-step="4">
                    <a href="{{ route('buyer.messages') }}" class="nav-link tooltip" data-tooltip="Messaging">
                        <span class="material-icons-round nav-icon">chat</span>
                        <span class="nav-text">Messaging</span>
                    </a>
                </li>

                <!-- Track Bids & Watchlist -->
                <li class="nav-item"
                    data-intro="Manage your active bids and watchlist items. Click the arrow to expand and see more options."
                    data-step="5">
                    <a href="javascript:void(0)" class="nav-link tooltip" id="bids-toggle" data-tooltip="Bids & Watchlist">
                        <span class="material-icons-round nav-icon">how_to_vote</span>
                        <span class="nav-text">Bids & Watchlist</span>
                        <span class="material-icons-round nav-arrow">arrow_right</span>
                    </a>

                    <!-- Dropdown -->
                    <ul class="submenu" id="bids-submenu">
                        <li class="submenu-item">
                            <a href="{{ route('buyer.bids') }}" class="submenu-link">Bids</a>
                        </li>
                        <li class="submenu-item">
                            <a href="{{ route('buyer.watchlist') }}" class="submenu-link">Watchlist</a>
                        </li>
                    </ul>
                </li>

                <!-- Payment / Escrow Status -->
                <li class="nav-item"
                    data-intro="Monitor your payment status and escrow transactions for secure purchases."
                    data-step="6">
                    <a href="{{ route('buyer.escrow') }}" class="nav-link tooltip"
                        data-tooltip="Payment / Escrow Status">
                        <span class="material-icons-round nav-icon">payments</span>
                        <span class="nav-text">Payment / Escrow</span>
                    </a>
                </li>

                <!-- Notifications -->
                <li class="nav-item"
                    data-intro="Check your notifications for important updates about your bids, purchases, and account."
                    data-step="7">
                    <a href="{{ route('buyer.notifications') }}" class="nav-link tooltip" data-tooltip="Notifications">
                        <span class="material-icons-round nav-icon">notifications</span>
                        <span class="nav-text">Notifications</span>
                        <span class="badge">3</span>
                    </a>
                </li>
            </ul>
        </nav>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-btn tooltip" data-tooltip="Logout"
                onclick="event.preventDefault(); this.closest('form').submit();">
                <span class="material-icons-round nav-icon">logout</span>
                <span>Log out</span>
            </button>
        </form>
    </aside>

    <script>
        // Add active class to clicked nav item
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function(e) {
                if (!this.id || this.id !== 'bids-toggle') {
                    document.querySelectorAll('.nav-link').forEach(item => {
                        item.classList.remove('active');
                    });
                    this.classList.add('active');
                }
            });
        });

        // Toggle submenu for Bids & Watchlist
        document.addEventListener('DOMContentLoaded', function() {
            const toggle = document.getElementById('bids-toggle');
            const submenu = document.getElementById('bids-submenu');
            const navItem = toggle.closest('.nav-item');

            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                navItem.classList.toggle('expanded');
                if (submenu.style.display === 'block') {
                    submenu.style.display = 'none';
                } else {
                    submenu.style.display = 'block';
                }
            });
        });

        // Smooth hover effects
        document.querySelectorAll('.nav-item, .logout-btn').forEach(item => {
            item.addEventListener('mouseenter', function() {
                this.style.transform = 'translateX(5px)';
            });
            item.addEventListener('mouseleave', function() {
                this.style.transform = 'translateX(0)';
            });
        });

        // Add ripple effect to buttons
        document.querySelectorAll('.nav-link, .logout-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                if (this.id && this.id === 'bids-toggle') return;

                const x = e.clientX - e.target.getBoundingClientRect().left;
                const y = e.clientY - e.target.getBoundingClientRect().top;

                const ripple = document.createElement('span');
                ripple.className = 'ripple';
                ripple.style.left = `${x}px`;
                ripple.style.top = `${y}px`;

                this.appendChild(ripple);

                setTimeout(() => {
                    ripple.remove();
                }, 1000);
            });
        });
    </script>
</body>

</html>
