@extends('layouts.Buyer')
@section('title', 'Buyer Dashboard')
@section('content')




    <body>


        <div class="sidebar-overlay" id="sidebar-overlay"></div>

        <div class="main-content" id="main-content">
            <header class="header" id="header">
                <button class="mobile-toggle" id="mobile-toggle">
                    <span class="material-icons-round">menu</span>
                </button>

                <div class="search-container">
                    <span class="material-icons-round">search</span>
                    <input type="text" placeholder="Search...">
                </div>

                <div class="header-actions">
                    <button class="header-action-btn">
                        <span class="material-icons-round">notifications</span>
                        <span class="notification-badge">3</span>
                    </button>

                    <div class="user-menu">
                        <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuAlZcArzLDu2ar9LMEKLq0Pc00JlKrypvY4o2sWPG7w2mqW-aQNk4bjM2otcK9rpP0L9Y9D6ufd1Hfl8VqG5QxQ70E70W2J9cIesp7CPnk60kNw55FYTZCqay0QTWJtOhG1fSzhpJ9qlyLUFFNstRlPZb2dYFbdpSXxaPvgx3J5yySMRc6c-OZWtIFKK4nU_k4AqY0bECTu42n9S1JfRLaYSB8-anFeAj3KHcMIrFKs8m09OiQcCxnEKa6nxdnCOjWXAmxG1d3hg68"
                            alt="User Avatar" class="user-menu-avatar">
                        <span class="user-menu-name">{{ Auth::user()->name }}</span>
                        <span class="material-icons-round user-menu-arrow">arrow_drop_down</span>
                    </div>
                </div>
            </header>

            <div class="dashboard-content">
                <div class="dashboard-section">
                    <div class="section-header">
                        <h2 class="section-title">Dashboard Overview</h2>
                        <a href="#" class="view-all">
                            View All
                            <span class="material-icons-round">chevron_right</span>
                        </a>
                    </div>

                    <div class="card-grid">
                        <div class="card" data-intro="See statistics about your account activity and performance."
                            data-step="7">
                            <div class="card-header">
                                <h3 class="card-title">Active Bids</h3>
                                <div class="card-icon blue">
                                    <span class="material-icons-round">local_offer</span>
                                </div>
                            </div>
                            <div class="card-value">5</div>
                            <div class="card-description">You have 5 active bids on vehicles</div>
                            <div class="card-footer">
                                <div class="trend up">
                                    <span class="material-icons-round">arrow_upward</span>
                                    <span>2 new today</span>
                                </div>
                            </div>
                        </div>

                        <div class="card" data-intro="Track your watchlist items and get notified about changes."
                            data-step="8">
                            <div class="card-header">
                                <h3 class="card-title">Watchlist Items</h3>
                                <div class="card-icon green">
                                    <span class="material-icons-round">watch_later</span>
                                </div>
                            </div>
                            <div class="card-value">12</div>
                            <div class="card-description">Vehicles you're keeping an eye on</div>
                            <div class="card-footer">
                                <div class="trend up">
                                    <span class="material-icons-round">arrow_upward</span>
                                    <span>3 new this week</span>
                                </div>
                            </div>
                        </div>

                        <div class="card" data-intro="Monitor your escrow balance and payment status." data-step="9">
                            <div class="card-header">
                                <h3 class="card-title">Escrow Balance</h3>
                                <div class="card-icon orange">
                                    <span class="material-icons-round">account_balance</span>
                                </div>
                            </div>
                            <div class="card-value">$8,500</div>
                            <div class="card-description">Funds held in escrow for your transactions</div>
                            <div class="card-footer">
                                <div class="trend">
                                    <span class="material-icons-round">remove</span>
                                    <span>No change</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="dashboard-section">
                    <div class="section-header">
                        <h2 class="section-title">Recent Activity</h2>
                        <a href="#" class="view-all">
                            View All
                            <span class="material-icons-round">chevron_right</span>
                        </a>
                    </div>

                    <div class="card" data-intro="Check your recent activities and transactions on the platform."
                        data-step="10">
                        <div class="card-header">
                            <h3 class="card-title">Latest Notifications</h3>
                        </div>
                        <div class="card-description">
                            <p>• Your bid on BMW X5 was outbid</p>
                            <p>• New Mercedes-Benz GLE-Class matches your preferences</p>
                            <p>• Payment for Audi Q7 was successfully processed</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tour button -->
        <div class="tour-button" id="start-tour">
            <i class="material-icons-round">explore</i>
        </div>

        <!-- Intro.js for guided tour -->
        <script src="https://unpkg.com/intro.js/minified/intro.min.js"></script>

        <script>
            // Toggle sidebar collapse
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            const header = document.getElementById('header');
            const toggleBtn = document.getElementById('sidebar-toggle');
            const toggleIcon = toggleBtn.querySelector('span');
            const mobileToggle = document.getElementById('mobile-toggle');
            const sidebarOverlay = document.getElementById('sidebar-overlay');

            // Check if there's a saved state in localStorage
            const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            if (isCollapsed) {
                sidebar.classList.add('collapsed');
                mainContent.classList.add('expanded');
                header.classList.add('expanded');
                toggleIcon.textContent = 'chevron_right';
            }

            toggleBtn.addEventListener('click', function() {
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');
                header.classList.toggle('expanded');

                if (sidebar.classList.contains('collapsed')) {
                    toggleIcon.textContent = 'chevron_right';
                    localStorage.setItem('sidebarCollapsed', 'true');
                } else {
                    toggleIcon.textContent = 'chevron_left';
                    localStorage.setItem('sidebarCollapsed', 'false');
                }
            });

            // Mobile sidebar toggle
            mobileToggle.addEventListener('click', function() {
                sidebar.classList.toggle('mobile-open');
                sidebarOverlay.classList.toggle('active');
            });

            sidebarOverlay.addEventListener('click', function() {
                sidebar.classList.remove('mobile-open');
                sidebarOverlay.classList.remove('active');
            });

            // Toggle submenu for Bids & Watchlist
            document.getElementById('bids-toggle').addEventListener('click', function() {
                if (!sidebar.classList.contains('collapsed')) {
                    const submenu = document.getElementById('bids-submenu');
                    const arrow = this.querySelector('.nav-arrow');

                    submenu.classList.toggle('open');
                    arrow.classList.toggle('rotated');
                }
            });

            // Add active class to clicked nav item
            document.querySelectorAll('.nav-link').forEach(link => {
                link.addEventListener('click', function(e) {
                    if (this.getAttribute('href') !== 'javascript:void(0)') {
                        document.querySelectorAll('.nav-link').forEach(item => {
                            item.classList.remove('active');
                        });
                        this.classList.add('active');

                        // Close mobile sidebar after clicking a link
                        if (window.innerWidth <= 1024) {
                            sidebar.classList.remove('mobile-open');
                            sidebarOverlay.classList.remove('active');
                        }
                    }
                });
            });

            // Add ripple effect to buttons
            document.querySelectorAll('.nav-link, .logout-btn, .header-action-btn').forEach(button => {
                button.addEventListener('click', function(e) {
                    const ripple = document.createElement('span');
                    ripple.classList.add('ripple');

                    const rect = this.getBoundingClientRect();
                    const size = Math.max(rect.width, rect.height);
                    const x = e.clientX - rect.left - size / 2;
                    const y = e.clientY - rect.top - size / 2;

                    ripple.style.width = ripple.style.height = `${size}px`;
                    ripple.style.left = `${x}px`;
                    ripple.style.top = `${y}px`;

                    this.appendChild(ripple);

                    setTimeout(() => {
                        ripple.remove();
                    }, 600);
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

            // Start the guided tour
            document.getElementById('start-tour').addEventListener('click', function() {
                // Make sure sidebar is expanded for the tour
                if (sidebar.classList.contains('collapsed')) {
                    sidebar.classList.remove('collapsed');
                    mainContent.classList.remove('expanded');
                    header.classList.remove('expanded');
                    toggleIcon.textContent = 'chevron_left';
                    localStorage.setItem('sidebarCollapsed', 'false');
                }

                // Start the intro.js tour
                introJs().setOptions({
                    steps: [{
                            element: document.querySelector('.nav-item[data-step="1"]'),
                            intro: "This is your main dashboard where you can see an overview of your account, bids, and activities.",
                            position: 'right'
                        },
                        {
                            element: document.querySelector('.nav-item[data-step="2"]'),
                            intro: "Update your personal information, change your password, and manage your account settings.",
                            position: 'right'
                        },
                        {
                            element: document.querySelector('.nav-item[data-step="3"]'),
                            intro: "View your purchase history and details of vehicles you've successfully bought.",
                            position: 'right'
                        },
                        {
                            element: document.querySelector('.nav-item[data-step="4"]'),
                            intro: "Manage your active bids and watchlist items. Click the arrow to expand and see more options.",
                            position: 'right'
                        },
                        {
                            element: document.querySelector('.nav-item[data-step="5"]'),
                            intro: "Monitor your payment status and escrow transactions for secure purchases.",
                            position: 'right'
                        },
                        {
                            element: document.querySelector('.nav-item[data-step="6"]'),
                            intro: "Check your notifications for important updates about your bids, purchases, and account.",
                            position: 'right'
                        },
                        {
                            element: document.querySelector('.card[data-step="7"]'),
                            intro: "See statistics about your account activity and performance.",
                            position: 'left'
                        },
                        {
                            element: document.querySelector('.card[data-step="8"]'),
                            intro: "Track your watchlist items and get notified about changes.",
                            position: 'left'
                        },
                        {
                            element: document.querySelector('.card[data-step="9"]'),
                            intro: "Monitor your escrow balance and payment status.",
                            position: 'left'
                        },
                        {
                            element: document.querySelector('.card[data-step="10"]'),
                            intro: "Check your recent activities and transactions on the platform.",
                            position: 'top'
                        }
                    ],
                    nextLabel: 'Next',
                    prevLabel: 'Back',
                    skipLabel: 'Skip',
                    doneLabel: 'Finish',
                    showProgress: true,
                    overlayOpacity: 0.7,
                    showStepNumbers: true
                }).start();
            });

            // Automatically start the tour for new users
            @if (auth()->check() && auth()->user()->first_login == 0)
                document.addEventListener('DOMContentLoaded', function() {
                    setTimeout(function() {
                        document.getElementById('start-tour').click();

                        // Mark first login as completed
                        fetch('{{ route('user.markFirstLogin') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });
                    }, 1000);
                });
            @endif
        </script>
    </body>
@endsection
