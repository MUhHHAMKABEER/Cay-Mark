@extends('layouts.admin')

@section('content')
    <title>Admin Listings Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --success: #4cc9f0;
            --danger: #f72585;
            --warning: #f8961e;
            --info: #4895ef;
            --light: #f8f9fa;
            --dark: #212529;
            --gray: #6c757d;
            --light-gray: #e9ecef;
            --sidebar-width: 280px;
            --header-height: 70px;
            --border-radius: 12px;
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f6f8fc;
            color: #333;
            line-height: 1.6;
        }

        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */




        .menu-item:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .menu-item.active {
            background-color: rgba(255, 255, 255, 0.2);
            border-left: 4px solid white;
        }

        .menu-item i {
            margin-right: 12px;
            font-size: 1.2rem;
            width: 24px;
            text-align: center;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            /* margin-left: var(--sidebar-width); */
            padding: 20px;
        }

        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 24px;
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            margin-bottom: 24px;
        }

        .search-bar {
            display: flex;
            align-items: center;
            background-color: var(--light);
            border-radius: 50px;
            padding: 8px 16px;
            width: 300px;
        }

        .search-bar input {
            background: transparent;
            border: none;
            outline: none;
            padding: 8px;
            width: 100%;
            font-size: 0.95rem;
        }

        .user-profile {
            display: flex;
            align-items: center;
        }

        .user-profile img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 12px;
            object-fit: cover;
        }

        /* Dashboard Cards */
        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 24px;
        }

        .card {
            background: white;
            border-radius: var(--border-radius);
            padding: 24px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            transition: var(--transition);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }

        .card-title {
            font-size: 1rem;
            color: var(--gray);
            font-weight: 500;
        }

        .card-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .card-value {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .card-growth {
            display: flex;
            align-items: center;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .growth-up {
            color: #2ecc71;
        }

        .growth-down {
            color: #e74c3c;
        }

        /* Table Styles */
        .table-container {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            margin-bottom: 24px;
        }

        .table-header {
            padding: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--light-gray);
        }

        .table-title {
            font-size: 1.25rem;
            font-weight: 600;
        }

        .table-filters {
            display: flex;
            gap: 12px;
        }

        .filter-btn {
            padding: 8px 16px;
            background: var(--light);
            border: none;
            border-radius: 50px;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
        }

        .filter-btn.active {
            background: var(--primary);
            color: white;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th {
            text-align: left;
            padding: 16px 24px;
            background-color: #f8f9fa;
            font-weight: 600;
            color: var(--gray);
            border-bottom: 1px solid var(--light-gray);
        }

        .table td {
            padding: 16px 24px;
            border-bottom: 1px solid var(--light-gray);
            font-size: 0.95rem;
        }

        .table tr:last-child td {
            border-bottom: none;
        }

        .table tr {
            transition: var(--transition);
        }

        .table tr:hover {
            background-color: #f8f9fa;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 500;
            display: inline-block;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-approved {
            background-color: #d4edda;
            color: #155724;
        }

        .status-rejected {
            background-color: #f8d7da;
            color: #721c24;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 0.85rem;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--secondary);
        }

        .btn-success {
            background-color: #2ecc71;
            color: white;
        }

        .btn-success:hover {
            background-color: #27ae60;
        }

        .btn-danger {
            background-color: #e74c3c;
            color: white;
        }

        .btn-danger:hover {
            background-color: #c0392b;
        }

        .btn i {
            margin-right: 6px;
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: flex-end;
            padding: 16px 24px;
            border-top: 1px solid var(--light-gray);
        }

        .pagination-item {
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            margin: 0 4px;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
        }

        .pagination-item:hover,
        .pagination-item.active {
            background-color: var(--primary);
            color: white;
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .sidebar {
                width: 70px;
                overflow: visible;
            }

            .sidebar-header h2,
            .menu-item span {
                display: none;
            }

            .sidebar-header {
                justify-content: center;
            }

            .menu-item {
                justify-content: center;
                padding: 16px;
            }

            .menu-item i {
                margin-right: 0;
                font-size: 1.4rem;
            }

            .main-content {
                margin-left: 70px;
            }
        }

        @media (max-width: 768px) {
            .dashboard-cards {
                grid-template-columns: 1fr;
            }

            .search-bar {
                width: 200px;
            }

            .table-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .table-filters {
                margin-top: 16px;
                flex-wrap: wrap;
            }
        }

        /* Animation */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in {
            animation: fadeIn 0.5s ease forwards;
        }
    </style>
    </head>

    <body>


        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <div class="header">
                <div class="search-bar">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search listings...">
                </div>
                <div class="user-profile">
                    <img src="https://placehold.co/40x40" alt="User">
                    <div>
                        <div>Admin User</div>
                        <small>Administrator</small>
                    </div>
                </div>
            </div>

            <div class="dashboard-cards">
                <div class="card fade-in" style="animation-delay: 0.1s;">
                    <div class="card-header">
                        <div class="card-title">Total Listings</div>
                        <div class="card-icon" style="background-color: rgba(67, 97, 238, 0.1); color: var(--primary);">
                            <i class="fas fa-car"></i>
                        </div>
                    </div>
                    <div class="card-value">{{ $totalListings }}</div>
                    <div class="card-growth growth-up">
                        <i class="fas fa-arrow-up"></i>
                        <span>{{ $percentageChange }}% from last month</span>
                    </div>
                </div>

                <div class="card fade-in" style="animation-delay: 0.2s;">
                    <div class="card-header">
                        <div class="card-title">Pending Approval</div>
                        <div class="card-icon" style="background-color: rgba(248, 150, 30, 0.1); color: var(--warning);">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                    <div class="card-value">{{ $pendingListings }}</div>
                    <div class="card-growth growth-down">
                        <i class="fas fa-arrow-down"></i>
                        <span>5% from last week</span>
                    </div>
                </div>

                <div class="card fade-in" style="animation-delay: 0.3s;">
                    <div class="card-header">
                        <div class="card-title">Approved</div>
                        <div class="card-icon" style="background-color: rgba(46, 204, 113, 0.1); color: #2ecc71;">
                            <i class="fas fa-check"></i>
                        </div>
                    </div>
                    <div class="card-value">{{ $approvedListings }}</div>
                    <div class="card-growth growth-up">
                        <i class="fas fa-arrow-up"></i>
                        <span>8% from last week</span>
                    </div>
                </div>

                <div class="card fade-in" style="animation-delay: 0.4s;">
                    <div class="card-header">
                        <div class="card-title">Rejected</div>
                        <div class="card-icon" style="background-color: rgba(231, 76, 60, 0.1); color: #e74c3c;">
                            <i class="fas fa-times"></i>
                        </div>
                    </div>
                    <div class="card-value">{{ $rejectedListings }}</div>
                    <div class="card-growth growth-down">
                        <i class="fas fa-arrow-down"></i>
                        <span>3% from last week</span>
                    </div>
                </div>
            </div>


            <div class="table-container fade-in" style="animation-delay: 0.5s;">
                <div class="table-header">
                    <h3 class="table-title">All Listings</h3>
                    <div class="table-filters">
                        <a href="{{ route('admin.show.listing') }}"
                            class="filter-btn {{ request('status') == null ? 'active' : '' }}">
                            All
                        </a>
                        <a href="{{ route('admin.show.listing', ['status' => 'pending']) }}"
                            class="filter-btn {{ request('status') == 'pending' ? 'active' : '' }}">
                            Pending
                        </a>
                        <a href="{{ route('admin.show.listing', ['status' => 'approved']) }}"
                            class="filter-btn {{ request('status') == 'approved' ? 'active' : '' }}">
                            Approved
                        </a>
                        <a href="{{ route('admin.show.listing', ['status' => 'rejected']) }}"
                            class="filter-btn {{ request('status') == 'rejected' ? 'active' : '' }}">
                            Rejected
                        </a>
                    </div>

                </div>

                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Method</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($listings as $listing)
                            <tr>
                                <td>#{{ $listing->id }}</td>
                                <td>{{ $listing->title }}</td>
                                <td>{{ $listing->listing_method }}</td>
                                <td>{{ $listing->listing_type }}</td>
                                <td>
                                    @if ($listing->status === 'pending')
                                        <span class="status-badge status-pending">Pending</span>
                                    @elseif($listing->status === 'approved')
                                        <span class="status-badge status-approved">Approved</span>
                                    @elseif($listing->status === 'rejected')
                                        <span class="status-badge status-rejected">Rejected</span>
                                    @endif
                                </td>
                                <td class="action-buttons">
                                    @if ($listing->status === 'pending')
                                        <form action="{{ route('admin.listing.approve', $listing->id) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            @method('PUT')
                                            <button class="btn btn-success btn-sm"><i class="fas fa-check"></i>
                                                Approve</button>
                                        </form>
                                        <form action="{{ route('admin.listing.disapprove', $listing->id) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            @method('PUT')
                                            <button class="btn btn-danger btn-sm"><i class="fas fa-times"></i>
                                                Reject</button>
                                        </form>
                                    @else
                                        {{-- <a href="{{ route('admin.listing.show', $listing->id) }}" class="btn btn-primary btn-sm"> --}}
                                        <i class="fas fa-eye"></i> View
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Laravel pagination --}}
                <div class="pagination">
                    {{ $listings->links('pagination::bootstrap-5') }}
                </div>
            </div>

        </div>


        <script>
            // Simple animations and interactions
            document.addEventListener('DOMContentLoaded', function() {
                // Filter buttons
                const filterButtons = document.querySelectorAll('.filter-btn');
                filterButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        filterButtons.forEach(btn => btn.classList.remove('active'));
                        this.classList.add('active');
                    });
                });

                // Approve/Reject buttons
                const actionButtons = document.querySelectorAll('.btn-success, .btn-danger');
                actionButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        const row = this.closest('tr');
                        const statusCell = row.querySelector('.status-badge');

                        if (this.classList.contains('btn-success')) {
                            statusCell.textContent = 'Approved';
                            statusCell.className = 'status-badge status-approved';
                            row.querySelector('.action-buttons').innerHTML =
                                '<button class="btn btn-primary btn-sm"><i class="fas fa-eye"></i> View</button>';
                        } else {
                            statusCell.textContent = 'Rejected';
                            statusCell.className = 'status-badge status-rejected';
                            row.querySelector('.action-buttons').innerHTML =
                                '<button class="btn btn-primary btn-sm"><i class="fas fa-eye"></i> View</button>';
                        }
                    });
                });
            });
        </script>
    @endsection
