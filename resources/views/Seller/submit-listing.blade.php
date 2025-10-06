@extends('layouts.Seller')

@section('content')

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Listing | Premium Marketplace</title>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --secondary: #64748b;
            --accent: #ef4444;
            --success: #10b981;
            --warning: #f59e0b;
            --light: #f8fafc;
            --dark: #1e293b;
            --card-bg: #ffffff;
            --border: #e2e8f0;
            --border-radius: 12px;
            --border-radius-lg: 16px;
            --box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --box-shadow-hover: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            background-color: #f1f5f9;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: var(--dark);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 2rem 1rem;
        }

        .container {
            max-width: 1200px;
            width: 100%;
            margin: 0 auto;
        }

        .listing-form-container {
            background: var(--card-bg);
            border-radius: var(--border-radius-lg);
            box-shadow: var(--box-shadow);
            padding: 2.5rem;
            margin-bottom: 2rem;
            border: 1px solid var(--border);
            max-height: 90vh;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: var(--primary) #f1f5f9;
        }

        .listing-form-container::-webkit-scrollbar {
            width: 8px;
        }

        .listing-form-container::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }

        .listing-form-container::-webkit-scrollbar-thumb {
            background-color: var(--primary);
            border-radius: 10px;
        }

        .form-header {
            text-align: center;
            margin-bottom: 2.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid var(--border);
        }

        .form-header h1 {
            color: var(--dark);
            font-weight: 700;
            margin-bottom: 0.75rem;
            font-size: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
        }

        .form-header p {
            color: var(--secondary);
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto;
        }

        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3rem;
            position: relative;
            padding: 0 2rem;
        }

        .step-indicator::before {
            content: '';
            position: absolute;
            top: 24px;
            left: 60px;
            right: 60px;
            height: 3px;
            background: linear-gradient(90deg, var(--primary) 0%, var(--primary) 50%, #e2e8f0 50%, #e2e8f0 100%);
            background-size: 200% 100%;
            background-position: 100% 0;
            transition: var(--transition);
            z-index: 1;
        }

        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 2;
        }

        .step-number {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background-color: #e2e8f0;
            color: #64748b;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            margin-bottom: 0.75rem;
            transition: var(--transition);
            border: 3px solid white;
            box-shadow: var(--box-shadow);
            position: relative;
        }

        .step.active .step-number {
            background-color: var(--primary);
            color: white;
            transform: scale(1.1);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.2);
        }

        .step.completed .step-number {
            background-color: var(--success);
            color: white;
        }

        .step.completed .step-number::after {
            content: '✓';
            font-weight: bold;
        }

        .step-label {
            font-size: 0.875rem;
            font-weight: 600;
            color: #94a3b8;
            text-align: center;
            transition: var(--transition);
        }

        .step.active .step-label {
            color: var(--primary);
        }

        .step.completed .step-label {
            color: var(--success);
        }

        .step-content {
            display: none;
            animation: fadeInUp 0.5s ease-out;
        }

        .step-content.active {
            display: block;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .section-title {
            color: var(--dark);
            font-weight: 700;
            margin-bottom: 1.75rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid var(--border);
            position: relative;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .section-title::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: -1px;
            width: 80px;
            height: 3px;
            background: linear-gradient(90deg, var(--primary), transparent);
            border-radius: 3px;
        }

        .form-card {
            background: var(--card-bg);
            border-radius: var(--border-radius);
            padding: 2rem;
            margin-bottom: 1.75rem;
            box-shadow: var(--box-shadow);
            border: 1px solid var(--border);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .form-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--primary);
            opacity: 0;
            transition: var(--transition);
        }

        .form-card:hover {
            box-shadow: var(--box-shadow-hover);
            transform: translateY(-2px);
        }

        .form-card:hover::before {
            opacity: 1;
        }

        .form-card .card-title {
            font-size: 1.25rem;
            color: var(--dark);
            margin-bottom: 1.5rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .form-card .card-title i {
            color: var(--primary);
            width: 24px;
            text-align: center;
        }

        .form-label {
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.95rem;
        }

        .required-field::after {
            content: "*";
            color: var(--accent);
            margin-left: 4px;
            font-weight: bold;
        }

        .form-control, .form-select {
            border-radius: var(--border-radius);
            padding: 0.875rem 1rem;
            border: 1px solid var(--border);
            transition: var(--transition);
            font-size: 0.95rem;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        /* Modern Toggle Buttons */
        .toggle-group {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .toggle-option {
            position: relative;
            flex: 1;
            min-width: 140px;
        }

        .toggle-option input {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-label {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1.5rem 1rem;
            background: var(--light);
            border: 2px solid var(--border);
            border-radius: var(--border-radius);
            cursor: pointer;
            transition: var(--transition);
            text-align: center;
            gap: 0.75rem;
            min-height: 120px;
        }

        .toggle-label:hover {
            border-color: var(--primary);
            transform: translateY(-2px);
        }

        .toggle-option input:checked + .toggle-label {
            background: rgba(37, 99, 235, 0.05);
            border-color: var(--primary);
            color: var(--primary);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.15);
        }

        .toggle-label i {
            font-size: 1.75rem;
            margin-bottom: 0.5rem;
        }

        .toggle-label .toggle-text {
            font-weight: 600;
            font-size: 0.95rem;
        }

        /* Category Cards */
        .category-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1.25rem;
        }

        .category-card {
            position: relative;
        }

        .category-card input {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }

        .category-label {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            background: var(--light);
            border: 2px solid var(--border);
            border-radius: var(--border-radius);
            cursor: pointer;
            transition: var(--transition);
            text-align: center;
            gap: 1rem;
            height: 100%;
        }

        .category-label:hover {
            border-color: var(--primary);
            transform: translateY(-3px);
            box-shadow: var(--box-shadow-hover);
        }

        .category-card input:checked + .category-label {
            background: rgba(37, 99, 235, 0.08);
            border-color: var(--primary);
            color: var(--primary);
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.15);
        }

        .category-label i {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
            color: var(--primary);
            opacity: 0.8;
        }

        .category-label .category-name {
            font-weight: 600;
            font-size: 1rem;
        }

        /* Modern Buttons */
        .btn {
            border-radius: var(--border-radius);
            padding: 0.875rem 2rem;
            font-weight: 600;
            transition: var(--transition);
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            font-size: 0.95rem;
            cursor: pointer;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
            box-shadow: 0 4px 6px rgba(37, 99, 235, 0.2);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(37, 99, 235, 0.25);
        }

        .btn-secondary {
            background: var(--secondary);
            color: white;
        }

        .btn-secondary:hover {
            background: #475569;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(100, 116, 139, 0.2);
        }

        .btn-success {
            background: var(--success);
            color: white;
            box-shadow: 0 4px 6px rgba(16, 185, 129, 0.2);
        }

        .btn-success:hover {
            background: #0da271;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(16, 185, 129, 0.25);
        }

        /* Image Preview */
        .image-preview-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 1.25rem;
            margin-top: 1.25rem;
        }

        .image-preview {
            position: relative;
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--box-shadow);
            transition: var(--transition);
            aspect-ratio: 1/1;
        }

        .image-preview:hover {
            transform: translateY(-3px);
            box-shadow: var(--box-shadow-hover);
        }

        .image-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .image-preview .btn-remove {
            position: absolute;
            top: 8px;
            right: 8px;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background-color: var(--accent);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            padding: 0;
            border: none;
            cursor: pointer;
            opacity: 0;
            transition: var(--transition);
        }

        .image-preview:hover .btn-remove {
            opacity: 1;
        }

        .nav-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 2.5rem;
            padding-top: 2rem;
            border-top: 1px solid var(--border);
        }

        /* Enhanced Select2 Customization */
        .select2-container {
            width: 100% !important;
        }

        .select2-container--default .select2-selection--single {
            height: 52px;
            border: 1px solid var(--border);
            border-radius: var(--border-radius);
            transition: var(--transition);
            background-color: white;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 50px;
            padding-left: 1rem;
            color: var(--dark);
            font-size: 0.95rem;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 50px;
            width: 40px;
            right: 5px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow b {
            border-color: var(--secondary) transparent transparent transparent;
            border-width: 6px 6px 0 6px;
        }

        .select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow b {
            border-color: transparent transparent var(--secondary) transparent;
            border-width: 0 6px 6px 6px;
        }

        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: rgba(37, 99, 235, 0.1);
            color: var(--primary);
        }

        .select2-container--default .select2-results__option[aria-selected=true] {
            background-color: rgba(37, 99, 235, 0.2);
            color: var(--primary-dark);
        }

        .select2-container--default .select2-search--dropdown .select2-search__field {
            border: 1px solid var(--border);
            border-radius: var(--border-radius);
            padding: 0.5rem 0.75rem;
        }

        .select2-dropdown {
            border: 1px solid var(--border);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
        }

        .select2-container--open .select2-dropdown--above {
            border-bottom: 1px solid var(--border);
            border-bottom-left-radius: var(--border-radius);
            border-bottom-right-radius: var(--border-radius);
        }

        .select2-container--open .select2-dropdown--below {
            border-top: 1px solid var(--border);
            border-top-left-radius: var(--border-radius);
            border-top-right-radius: var(--border-radius);
        }

        .select2-results__option {
            padding: 0.75rem 1rem;
            transition: var(--transition);
        }

        .select2-container--default .select2-selection--single:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .custom-other-field {
            display: none;
            margin-top: 1rem;
            animation: fadeIn 0.3s ease;
        }

        .price-input-group {
            position: relative;
        }

        .price-input-group .input-group-text {
            background-color: var(--light);
            border: 1px solid var(--border);
            border-right: none;
            font-weight: 600;
            color: var(--dark);
        }

        .price-input-group .form-control {
            border-left: none;
            padding-left: 0;
        }

        /* Alert Styling */
        .alert {
            border-radius: var(--border-radius);
            border: none;
            padding: 1rem 1.5rem;
            box-shadow: var(--box-shadow);
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success);
            border-left: 4px solid var(--success);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            body {
                padding: 1rem 0.5rem;
                justify-content: flex-start;
            }

            .listing-form-container {
                padding: 1.5rem;
                max-height: none;
                overflow-y: visible;
            }

            .step-indicator {
                flex-direction: column;
                align-items: flex-start;
                gap: 2rem;
                padding: 0;
            }

            .step-indicator::before {
                display: none;
            }

            .step {
                flex-direction: row;
                gap: 1rem;
                width: 100%;
            }

            .step-number {
                margin-bottom: 0;
            }

            .nav-buttons {
                flex-direction: column;
                gap: 1rem;
            }

            .nav-buttons button {
                width: 100%;
            }

            .category-grid {
                grid-template-columns: 1fr;
            }

            .toggle-group {
                flex-direction: column;
            }

            .toggle-option {
                min-width: 100%;
            }
        }

        @media (max-width: 576px) {
            .form-card {
                padding: 1.5rem;
            }

            .section-title {
                font-size: 1.25rem;
            }

            .form-header h1 {
                font-size: 1.75rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="listing-form-container">
            <div class="form-header">
                <h1><i class="fas fa-plus-circle"></i>Create New Listing</h1>
                <p>List your item for sale in our premium marketplace with a few simple steps</p>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Step Indicator -->
            <div class="step-indicator">
                <div class="step active" id="indicator-step1">
                    <div class="step-number">1</div>
                    <div class="step-label">Listing Method</div>
                </div>
                <div class="step" id="indicator-step2">
                    <div class="step-number">2</div>
                    <div class="step-label">Category</div>
                </div>
                <div class="step" id="indicator-step3">
                    <div class="step-number">3</div>
                    <div class="step-label">Item Details</div>
                </div>
            </div>

            <form id="listingForm" method="POST" action="{{ route('seller.listings.store') }}" enctype="multipart/form-data">
                @csrf

                <!-- Step 1: Listing Method -->
                <div class="step-content active" id="step1">
                    <h3 class="section-title"><i class="fas fa-tag"></i>Listing Method</h3>

                    <div class="form-card">
                        <h5 class="card-title"><i class="fas fa-store"></i>Select Listing Method</h5>
                        <div class="toggle-group">
                            <div class="toggle-option">
                                <input class="form-check-input" type="radio" name="listing_method" id="buyNow" value="buy_now" checked>
                                <label class="toggle-label" for="buyNow">
                                    <i class="fas fa-shopping-cart"></i>
                                    <span class="toggle-text">Buy Now</span>
                                    <small>Fixed Price</small>
                                </label>
                            </div>
                            <div class="toggle-option">
                                <input class="form-check-input" type="radio" name="listing_method" id="auction" value="auction">
                                <label class="toggle-label" for="auction">
                                    <i class="fas fa-gavel"></i>
                                    <span class="toggle-text">Auction</span>
                                    <small>Competitive Bidding</small>
                                </label>
                            </div>
                        </div>

                        <div id="auctionDurationField" class="mt-4" style="display: none;">
                            <label for="auction_duration" class="form-label required-field">Auction Duration</label>
                            <select class="form-select" id="auction_duration" name="auction_duration">
                                <option value="">Select Duration</option>
                                <option value="7">7-day</option>
                                <option value="14">14-day</option>
                            </select>
                        </div>
                    </div>

                    <div class="nav-buttons">
                        <div></div>
                        <button type="button" class="btn btn-primary next-step" data-next="step2">
                            Next Step <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>

                <!-- Step 2: Listing Type -->
                <div class="step-content" id="step2">
                    <h3 class="section-title"><i class="fas fa-layer-group"></i>Category Selection</h3>

                    <div class="form-card">
                        <h5 class="card-title"><i class="fas fa-folder"></i>Select Category</h5>
                        <div class="mb-4">
                            <label class="form-label required-field">Major Category</label>
                            <div class="category-grid">
                                <div class="category-card">
                                    <input class="form-check-input category-type" type="radio" name="major_category" id="vehicles" value="vehicles" checked>
                                    <label class="category-label" for="vehicles">
                                        <i class="fas fa-car"></i>
                                        <span class="category-name">Vehicles</span>
                                    </label>
                                </div>
                                <div class="category-card">
                                    <input class="form-check-input category-type" type="radio" name="major_category" id="marine" value="marine">
                                    <label class="category-label" for="marine">
                                        <i class="fas fa-ship"></i>
                                        <span class="category-name">Marine Vessels</span>
                                    </label>
                                </div>
                                <div class="category-card">
                                    <input class="form-check-input category-type" type="radio" name="major_category" id="equipment" value="equipment">
                                    <label class="category-label" for="equipment">
                                        <i class="fas fa-tools"></i>
                                        <span class="category-name">Equipment & Machinery</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3" id="subcategoryContainer">
                            <label for="subcategory" class="form-label required-field">Subcategory</label>
                            <select class="form-select" id="subcategory" name="subcategory">
                                <option value="">Select Subcategory</option>
                                <!-- Vehicle Subcategories -->
                                <option value="cars" class="vehicle-option">Cars</option>
                                <option value="trucks" class="vehicle-option">Trucks</option>
                                <option value="jeeps" class="vehicle-option">Jeeps</option>
                                <option value="suvs" class="vehicle-option">SUVs</option>
                                <option value="motorcycles" class="vehicle-option">Motorcycles</option>
                                <option value="golf_carts" class="vehicle-option">Golf Carts</option>
                                <option value="atvs" class="vehicle-option">ATVs</option>
                                <option value="vans" class="vehicle-option">Vans</option>
                                <option value="specialty" class="vehicle-option">Specialty</option>
                                <option value="other_vehicle" class="vehicle-option">Other (Vehicle)</option>

                                <!-- Marine Subcategories -->
                                <option value="boats" class="marine-option" style="display:none;">Boats</option>
                                <option value="jet_skis" class="marine-option" style="display:none;">Jet Skis</option>
                                <option value="other_marine" class="marine-option" style="display:none;">Other (Marine)</option>

                                <!-- Equipment Subcategories -->
                                <option value="industrial" class="equipment-option" style="display:none;">Industrial</option>
                                <option value="farming" class="equipment-option" style="display:none;">Farming</option>
                                <option value="construction" class="equipment-option" style="display:none;">Construction</option>
                                <option value="other_equipment" class="equipment-option" style="display:none;">Other (Equipment)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="make" class="form-label required-field">Make</label>
                            <select id="make" name="make" class="form-control">
                                <option value="">Select Make</option>
                                <option value="Acura">Acura</option>
                                <option value="Audi">Audi</option>
                                <option value="BMW">BMW</option>
                                <option value="Ford">Ford</option>
                                <option value="Honda">Honda</option>
                                <option value="Toyota">Toyota</option>
                                <!-- Add all makes -->
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="model" class="form-label required-field">Model</label>
                            <select id="model" name="model" class="form-control">
                                <option value="">Select Model</option>
                            </select>
                        </div>
                    </div>

                    <div class="nav-buttons">
                        <button type="button" class="btn btn-secondary prev-step" data-prev="step1">
                            <i class="fas fa-arrow-left"></i> Previous
                        </button>
                        <button type="button" class="btn btn-primary next-step" data-next="step3">
                            Next Step <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>

                <!-- Step 3: Item Details -->
                <div class="step-content" id="step3">
                    <h3 class="section-title"><i class="fas fa-info-circle"></i>Item Details</h3>

                    <div class="form-card">
                        <h5 class="card-title"><i class="fas fa-cog"></i>Basic Information</h5>

                        <!-- Condition Field -->
                        <div class="mb-4">
                            <label class="form-label required-field">Condition</label>
                            <div class="toggle-group">
                                <div class="toggle-option">
                                    <input class="form-check-input" type="radio" name="condition" id="new" value="new" required>
                                    <label class="toggle-label" for="new">
                                        <i class="fas fa-certificate"></i>
                                        <span class="toggle-text">New / Like New</span>
                                    </label>
                                </div>
                                <div class="toggle-option">
                                    <input class="form-check-input" type="radio" name="condition" id="used" value="used">
                                    <label class="toggle-label" for="used">
                                        <i class="fas fa-history"></i>
                                        <span class="toggle-text">Used</span>
                                    </label>
                                </div>
                                <div class="toggle-option">
                                    <input class="form-check-input" type="radio" name="condition" id="salvaged" value="salvaged">
                                    <label class="toggle-label" for="salvaged">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        <span class="toggle-text">Salvaged</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Odometer and Year -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="odometer" class="form-label required-field">Odometer</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="odometer" name="odometer" min="0" step="1" required>
                                    <span class="input-group-text">miles</span>
                                </div>
                                <div class="form-text">Enter the current mileage on the vehicle</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="year" class="form-label required-field">Year</label>
                                <select class="form-select" id="year" name="year" required>
                                    <option value="">Select Year</option>
                                    @for($i = date('Y') + 1; $i >= 1900; $i--)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                    <option value="other">Other</option>
                                </select>
                                <div id="yearOtherField" class="custom-other-field">
                                    <input type="text" class="form-control" id="year_other" name="year_other" placeholder="Enter Year">
                                </div>
                            </div>
                        </div>

                        <!-- Price and Color -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="price" class="form-label required-field" id="priceLabel">Price</label>
                                <div class="input-group price-input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" id="price" name="price" min="0" step="0.01" required>
                                </div>
                                <div class="form-text" id="priceHelpText">Set your asking price</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="color" class="form-label required-field">Color</label>
                                <select class="form-select" id="color" name="color" required>
                                    <option value="">Select Color</option>
                                    <option value="white">White</option>
                                    <option value="black">Black</option>
                                    <option value="silver">Silver</option>
                                    <option value="gray">Gray</option>
                                    <option value="red">Red</option>
                                    <option value="blue">Blue</option>
                                    <option value="green">Green</option>
                                    <option value="yellow">Yellow</option>
                                    <option value="orange">Orange</option>
                                    <option value="brown">Brown</option>
                                    <option value="purple">Purple</option>
                                    <option value="gold">Gold</option>
                                    <option value="beige">Beige</option>
                                    <option value="other">Other</option>
                                </select>
                                <div id="colorOtherField" class="custom-other-field">
                                    <input type="text" class="form-control" id="color_other" name="color_other" placeholder="Enter Color">
                                </div>
                            </div>
                        </div>

                        <!-- Fuel Type and Transmission -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="fuel_type" class="form-label required-field">Fuel Type</label>
                                <select class="form-select" id="fuel_type" name="fuel_type" required>
                                    <option value="">Select Fuel Type</option>
                                    <option value="gas">Gas</option>
                                    <option value="diesel">Diesel</option>
                                    <option value="hybrid">Hybrid</option>
                                    <option value="electric">Electric</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label required-field">Transmission</label>
                                <div class="toggle-group">
                                    <div class="toggle-option">
                                        <input class="form-check-input" type="radio" name="transmission" id="automatic" value="automatic" required>
                                        <label class="toggle-label" for="automatic">
                                            <i class="fas fa-cogs"></i>
                                            <span class="toggle-text">Automatic</span>
                                        </label>
                                    </div>
                                    <div class="toggle-option">
                                        <input class="form-check-input" type="radio" name="transmission" id="manual" value="manual">
                                        <label class="toggle-label" for="manual">
                                            <i class="fas fa-hand-paper"></i>
                                            <span class="toggle-text">Manual</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Title Status and Primary Damage -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="title_status" class="form-label required-field">Title Status</label>
                                <select class="form-select" id="title_status" name="title_status" required>
                                    <option value="">Select Title Status</option>
                                    <option value="available">Available</option>
                                    <option value="salvaged">Salvaged</option>
                                    <option value="none">None</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="primary_damage" class="form-label required-field">Primary Damage</label>
                                <select class="form-select" id="primary_damage" name="primary_damage" required>
                                    <option value="">Select Primary Damage</option>
                                    <option value="no_damage">No Damage</option>
                                    <option value="front_end">Front End</option>
                                    <option value="rear_end">Rear End</option>
                                    <option value="side">Side</option>
                                    <option value="roof">Roof</option>
                                    <option value="mechanical">Mechanical</option>
                                    <option value="transmission">Transmission</option>
                                    <option value="engine">Engine</option>
                                    <option value="water_flood">Water / Flood</option>
                                    <option value="fire">Fire</option>
                                    <option value="vandalism">Vandalism</option>
                                    <option value="interior_damage">Interior Damage</option>
                                    <option value="all_over">All Over</option>
                                    <option value="normal">Normal</option>
                                    <option value="unknown">Unknown</option>
                                </select>
                            </div>
                        </div>

                        <!-- Secondary Damage and Keys Available -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="secondary_damage" class="form-label required-field">Secondary Damage</label>
                                <select class="form-select" id="secondary_damage" name="secondary_damage" required>
                                    <option value="">Select Secondary Damage</option>
                                    <option value="none">None</option>
                                    <option value="front_end">Front End</option>
                                    <option value="rear_end">Rear End</option>
                                    <option value="side">Side</option>
                                    <option value="roof">Roof</option>
                                    <option value="mechanical">Mechanical</option>
                                    <option value="transmission">Transmission</option>
                                    <option value="engine">Engine</option>
                                    <option value="water_flood">Water / Flood</option>
                                    <option value="fire">Fire</option>
                                    <option value="vandalism">Vandalism</option>
                                    <option value="interior_damage">Interior Damage</option>
                                    <option value="all_over">All Over</option>
                                    <option value="normal">Normal</option>
                                    <option value="unknown">Unknown</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label required-field">Keys Available</label>
                                <div class="toggle-group">
                                    <div class="toggle-option">
                                        <input class="form-check-input" type="radio" name="keys_available" id="keys_yes" value="yes" required>
                                        <label class="toggle-label" for="keys_yes">
                                            <i class="fas fa-key"></i>
                                            <span class="toggle-text">Yes</span>
                                        </label>
                                    </div>
                                    <div class="toggle-option">
                                        <input class="form-check-input" type="radio" name="keys_available" id="keys_no" value="no">
                                        <label class="toggle-label" for="keys_no">
                                            <i class="fas fa-times-circle"></i>
                                            <span class="toggle-text">No</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-card">
                        <h5 class="card-title"><i class="fas fa-images"></i>Upload Images</h5>
                        <p class="text-muted">Upload at least one image of your item. The first image will be used as the primary photo.</p>

                        <div class="mb-3">
                            <label for="listing_images" class="form-label required-field">Select Images</label>
                            <input class="form-control" type="file" id="listing_images" name="images[]" multiple accept="image/*" required>
                            <div class="form-text">You can select multiple images (JPEG, PNG, GIF). Max 10 images.</div>
                        </div>

                        <div class="image-preview-container" id="imagePreviewContainer">
                            <!-- Preview images will appear here -->
                        </div>
                    </div>

                    <!-- Additional Fields for Specific Categories -->
                    <div id="additionalFields">
                        <!-- These will be populated dynamically based on category selection -->
                    </div>

                    <div class="nav-buttons">
                        <button type="button" class="btn btn-secondary prev-step" data-prev="step2">
                            <i class="fas fa-arrow-left"></i> Previous
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check-circle"></i> Submit Listing
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize Select2 with enhanced styling
            function initializeSelect2() {
                $('.form-select').select2({
                    theme: 'default',
                    width: '100%',
                    dropdownAutoWidth: true,
                    allowClear: true,
                    placeholder: function() {
                        return $(this).data('placeholder') || 'Select an option';
                    }
                });

                // Add custom classes to Select2 elements for styling
                $('.select2-container').addClass('enhanced-select');
            }

            // Initialize Select2 on page load
            initializeSelect2();

            // Reinitialize Select2 when navigating between steps
            $(document).on('stepChanged', function() {
                setTimeout(initializeSelect2, 100);
            });

            // Handle make selection change to load models
            $('#make').change(function() {
                var make = $(this).val();
                var modelSelect = $('#model');

                // Clear current models and show loading
                modelSelect.empty().append('<option value="">Loading models...</option>');

                if (make) {
                    // Make AJAX request to get models
                    $.ajax({
                        url: '/listings/models/' + encodeURIComponent(make),
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            modelSelect.empty().append('<option value="">Select Model</option>');

                            if (data.models && data.models.length > 0) {
                                $.each(data.models, function(index, model) {
                                    modelSelect.append($('<option>', {
                                        value: model,
                                        text: model
                                    }));
                                });
                            } else {
                                modelSelect.append('<option value="">No models found</option>');
                            }

                            // Reinitialize Select2 after updating options
                            modelSelect.select2();
                        },
                        error: function(xhr, status, error) {
                            modelSelect.empty().append('<option value="">Error loading models</option>');
                            console.error('Error loading models:', error);
                            console.log('Response:', xhr.responseText);
                            modelSelect.select2();
                        }
                    });
                } else {
                    modelSelect.empty().append('<option value="">Select Model</option>');
                    modelSelect.select2();
                }
            });

            // Show/hide auction duration based on listing method
            $('input[name="listing_method"]').change(function() {
                if ($(this).val() === 'auction') {
                    $('#auctionDurationField').show();
                    $('#auction_duration').prop('required', true);
                    // Update price label for auction
                    $('#priceLabel').text('Starting Bid');
                    $('#priceHelpText').text('Set your starting bid price');
                } else {
                    $('#auctionDurationField').hide();
                    $('#auction_duration').prop('required', false);
                    // Update price label for buy now
                    $('#priceLabel').text('Price');
                    $('#priceHelpText').text('Set your asking price');
                }
            });

            // Handle category type selection
            $('.category-type').change(function() {
                const category = $(this).val();

                // Hide all options first
                $('#subcategory option').hide();

                // Show relevant options
                if (category === 'vehicles') {
                    $('.vehicle-option').show();
                    $('.marine-option, .equipment-option').hide();
                } else if (category === 'marine') {
                    $('.marine-option').show();
                    $('.vehicle-option, .equipment-option').hide();
                } else if (category === 'equipment') {
                    $('.equipment-option').show();
                    $('.vehicle-option, .marine-option').hide();
                }

                // Reset subcategory selection
                $('#subcategory').val('').trigger('change');
                $('#otherCategoryFields').hide();
            });

            // Handle subcategory selection
            $('#subcategory').change(function() {
                const subcategory = $(this).val();

                // Show other fields if "Other" is selected
                if (subcategory.includes('other')) {
                    $('#otherCategoryFields').show();
                    $('#other_make').prop('required', true);
                    $('#other_model').prop('required', true);
                } else {
                    $('#otherCategoryFields').hide();
                    $('#other_make').prop('required', false);
                    $('#other_model').prop('required', false);
                }

                // Update additional fields based on subcategory
                updateAdditionalFields(subcategory);
            });

            // Handle "Other" selection for various fields
            $('#make, #model, #year, #color').change(function() {
                const fieldId = $(this).attr('id');
                if ($(this).val() === 'other') {
                    $(`#${fieldId}OtherField`).show();
                    $(`#${fieldId}_other`).prop('required', true);
                } else {
                    $(`#${fieldId}OtherField`).hide();
                    $(`#${fieldId}_other`).prop('required', false);
                }
            });

            // Step navigation
            $('.next-step').click(function() {
                const currentStep = $(this).closest('.step-content').attr('id');
                const nextStep = $(this).data('next');

                // Validate current step before proceeding
                if (validateStep(currentStep)) {
                    navigateToStep(nextStep);
                }
            });

            $('.prev-step').click(function() {
                const prevStep = $(this).data('prev');
                navigateToStep(prevStep);
            });

            function navigateToStep(stepId) {
                // Update step contents
                $('.step-content').removeClass('active');
                $(`#${stepId}`).addClass('active');

                // Update step indicators
                $('.step').removeClass('active completed');

                if (stepId === 'step1') {
                    $('#indicator-step1').addClass('active');
                    $('#indicator-step2').removeClass('completed');
                } else if (stepId === 'step2') {
                    $('#indicator-step1').addClass('completed');
                    $('#indicator-step2').addClass('active');
                } else if (stepId === 'step3') {
                    $('#indicator-step1').addClass('completed');
                    $('#indicator-step2').addClass('completed');
                    $('#indicator-step3').addClass('active');
                }

                // Trigger custom event for step change
                $(document).trigger('stepChanged');

                // Scroll to top of form container
                $('.listing-form-container').animate({ scrollTop: 0 }, 300);
            }

            function updateAdditionalFields(subcategory) {
                let additionalFieldsHtml = '';

                // Add fields based on subcategory
                if (subcategory === 'boats') {
                    additionalFieldsHtml = `
                        <div class="form-card">
                            <h5 class="card-title"><i class="fas fa-water me-2"></i>Boat Specifications</h5>
                            <div class="mb-3">
                                <label for="engine_type" class="form-label required-field">Engine Type</label>
                                <select class="form-select" id="engine_type" name="engine_type" required>
                                    <option value="">Select Engine Type</option>
                                    <option value="inboard">Inboard</option>
                                    <option value="outboard">Outboard</option>
                                    <option value="sterndrive">Sterndrive</option>
                                    <option value="jet_drive">Jet Drive</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="hull_material" class="form-label">Hull Material (Optional)</label>
                                <select class="form-select" id="hull_material" name="hull_material">
                                    <option value="">Select Hull Material</option>
                                    <option value="fiberglass">Fiberglass</option>
                                    <option value="aluminum">Aluminum</option>
                                    <option value="wood">Wood</option>
                                    <option value="steel">Steel</option>
                                    <option value="composite">Composite</option>
                                </select>
                            </div>
                        </div>
                    `;
                } else if (subcategory === 'jet_skis') {
                    additionalFieldsHtml = `
                        <div class="form-card">
                            <h5 class="card-title"><i class="fas fa-water me-2"></i>Jet Ski Specifications</h5>
                            <div class="mb-3">
                                <label for="engine_type" class="form-label required-field">Engine Type</label>
                                <select class="form-select" id="engine_type" name="engine_type" required>
                                    <option value="">Select Engine Type</option>
                                    <option value="2_stroke">2-Stroke</option>
                                    <option value="4_stroke">4-Stroke</option>
                                </select>
                            </div>
                        </div>
                    `;
                } else if (['industrial', 'farming', 'construction'].includes(subcategory)) {
                    additionalFieldsHtml = `
                        <div class="form-card">
                            <h5 class="card-title"><i class="fas fa-cogs me-2"></i>Equipment Specifications</h5>
                            <div class="mb-3">
                                <label for="category_type" class="form-label required-field">Category Type</label>
                                <select class="form-select" id="category_type" name="category_type" required>
                                    <option value="">Select Category Type</option>
                                    <option value="excavator">Excavator</option>
                                    <option value="tractor">Tractor</option>
                                    <option value="loader">Loader</option>
                                    <option value="crane">Crane</option>
                                    <option value="bulldozer">Bulldozer</option>
                                    <option value="harvester">Harvester</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>
                    `;
                }

                $('#additionalFields').html(additionalFieldsHtml);

                // Reinitialize Select2 for new dropdowns
                setTimeout(initializeSelect2, 100);
            }

            // Form validation for each step
            function validateStep(stepId) {
                let isValid = true;
                const currentStep = $(`#${stepId}`);

                // Check all required fields in the current step
                currentStep.find('[required]').each(function() {
                    if (!$(this).val()) {
                        isValid = false;
                        // Highlight the empty field
                        $(this).addClass('is-invalid');

                        // Add error message
                        if (!$(this).next('.invalid-feedback').length) {
                            $(this).after('<div class="invalid-feedback">This field is required.</div>');
                        }
                    } else {
                        $(this).removeClass('is-invalid');
                        $(this).next('.invalid-feedback').remove();
                    }
                });

                return isValid;
            }

            // Remove validation styles when user starts typing
            $('input, select').on('input change', function() {
                $(this).removeClass('is-invalid');
                $(this).next('.invalid-feedback').remove();
            });
        });

        // Image preview functionality
        $('#listing_images').change(function() {
            const files = this.files;
            const previewContainer = $('#imagePreviewContainer');
            previewContainer.empty();

            if (files.length > 10) {
                alert('Maximum 10 images allowed. Please select fewer images.');
                this.value = '';
                return;
            }

            for (let i = 0; i < files.length; i++) {
                const file = files[i];

                if (!file.type.match('image.*')) {
                    alert('Please select only image files.');
                    this.value = '';
                    previewContainer.empty();
                    return;
                }

                const reader = new FileReader();

                reader.onload = (function(file) {
                    return function(e) {
                        const preview = $('<div class="image-preview">')
                            .append($('<img>').attr('src', e.target.result))
                            .append($('<button type="button" class="btn-remove">').html('&times;').on('click', function() {
                                $(this).parent().remove();
                                // Create a new DataTransfer object to remove the file from input
                                const dataTransfer = new DataTransfer();
                                const currentFiles = $('#listing_images')[0].files;

                                for (let j = 0; j < currentFiles.length; j++) {
                                    if (currentFiles[j] !== file) {
                                        dataTransfer.items.add(currentFiles[j]);
                                    }
                                }

                                $('#listing_images')[0].files = dataTransfer.files;
                            }));

                        previewContainer.append(preview);
                    };
                })(file);

                reader.readAsDataURL(file);
            }
        });
    </script>
</body>

@endsection
