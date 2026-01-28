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
            font-weight: 5x1x00;
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
                /* width: 288px;/ */
                background-color: #F9F1EC;
                /* padding: 2rem; */
                /* height: 100vh; */
                /* display: flex;// */
                /* flex-direction: column; */
            }
        }
    </style>
</head>

<body>
    <aside class="sidebar">

<a href="{{ route('profile.edit') }}">
        <div class="user-profile">
            <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuAlZcArzLDu2ar9LMEKLq0Pc00JlKrypvY4o2sWPG7w2mqW-aQNk4bjM2otcK9rpP0L9Y9D6ufd1Hfl8VqG5QxQ70E70W2J9cIesp7CPnk60kNw55FYTZCqay0QTWJtOhG1fSzhpJ9qlyLUFFNstRlPZb2dYFbdpSXxaPvgx3J5yySMRc6c-OZWtIFKK4nU_k4AqY0bECTu42n9S1JfRLaYSB8-anFeAj3KHcMIrFKs8m09OiQcCxnEKa6nxdnCOjWXAmxG1d3hg68"
                alt="User Avatar" class="avatar">
            <h2 class="user-name">{{ Str::ucfirst(Auth::user()->name) }}</h2>

            <span class="user-role">Marketing Director</span>
        </div>
</a>
        <nav class="nav-menu">
            <!-- Seller Sidebar -->
            <ul class="nav flex flex-col space-y-2">

                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="{{ route('dashboard.seller') }}" class="nav-link tooltip" data-tooltip="Dashboard" id="seller-tour-dashboard">
                        <span class="material-icons-round nav-icon">dashboard</span>
                        <span>Dashboard</span>
                    </a>
                </li>

                <!-- Account Settings -->
                <li class="nav-item">
                    <a href="{{ route('profile.edit') }}" class="nav-link tooltip" data-tooltip="Account Settings" id="seller-tour-profile">
                        <span class="material-icons-round nav-icon">manage_accounts</span>
                        <span>Account Settings</span>
                    </a>
                </li>

                <!-- Submit a Listing -->
                <li class="nav-item">
                    <a href="{{ route('seller.listings.create') }}" class="nav-link tooltip" data-tooltip="Submit Listing" id="seller-tour-submit">
                        <span class="material-icons-round nav-icon">add_box</span>
                        <span>Submit a Listing</span>
                    </a>
                </li>

                <!-- My Listings -->
                <li class="nav-item">
                    <a href="{{ route('seller.listings.index') }}" class="nav-link tooltip" data-tooltip="My Listings" id="seller-tour-listings">
                        <span class="material-icons-round nav-icon">directions_car</span>
                        <span>My Listings</span>
                    </a>
                </li>

                <!-- Auctions -->
                <li class="nav-item">
                    <a href="{{ route('seller.Auction.index') }}" class="nav-link tooltip" data-tooltip="Auctions" id="seller-tour-auctions">
                        <span class="material-icons-round nav-icon">gavel</span>
                        <span>Auctions</span>
                    </a>
                </li>

                <!-- Messaging Center -->
                <li class="nav-item">
                    <a href="{{ route('seller.chat') }}" class="nav-link tooltip" data-tooltip="Messages" id="seller-tour-messaging">
                        <span class="material-icons-round nav-icon">mail</span>
                        <span>Messaging Center</span>
                    </a>
                </li>

                <!-- Payout Settings -->
                <li class="nav-item">
                    <a href="" class="nav-link tooltip" data-tooltip="Payout Settings" id="seller-tour-payout">
                        <span class="material-icons-round nav-icon">account_balance</span>
                        <span>Payout Settings</span>
                    </a>
                </li>

            </ul>

        </nav>


        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-btn tooltip" data-tooltip="Logout" id="seller-tour-logout"
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
                // e.preventDefault();
                document.querySelectorAll('.nav-link').forEach(item => {
                    item.classList.remove('active');
                });
                this.classList.add('active');
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
                // e.preventDefault();
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
