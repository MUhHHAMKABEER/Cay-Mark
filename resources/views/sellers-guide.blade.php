@extends('layouts.welcome')

@section('content')


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller's Guide - CayMark</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .gradient-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .step-number {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: bold;
        }
        .feature-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .package-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .package-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        .package-popular {
            position: relative;
            border: 2px solid #667eea;
        }
        .package-popular::before {
            content: "Most Popular";
            position: absolute;
            top: -12px;
            left: 50%;
            transform: translateX(-50%);
            background: #667eea;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: bold;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Main Content -->
    <main class="py-12 px-4 max-w-7xl mx-auto">
        <!-- Hero Section -->
        <div class="text-center mb-16">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Seller's Guide</h1>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">Learn how to easily list and sell your vehicles on CayMark's trusted marketplace</p>
        </div>

        <!-- Step-by-Step Guide -->
        <div class="mb-16">
            <h2 class="text-3xl font-bold text-center mb-12">How to Become a Seller</h2>
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Step 1 -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="step-number mx-auto mb-4">1</div>
                    <h3 class="text-xl font-bold mb-3 text-center">Register & Choose Package</h3>
                    <p class="text-gray-600 mb-4">Create your seller account and select the package that fits your needs.</p>
                    <ul class="text-gray-600 space-y-2">
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-2"></i>
                            <span>Select your seller role</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-2"></i>
                            <span>Choose from Casual, Standard, or Advanced packages</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-2"></i>
                            <span>Complete registration and payment</span>
                        </li>
                    </ul>
                </div>

                <!-- Step 2 -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="step-number mx-auto mb-4">2</div>
                    <h3 class="text-xl font-bold mb-3 text-center">Submit Your Listings</h3>
                    <p class="text-gray-600 mb-4">Access your seller dashboard and submit vehicle listings with all required information.</p>
                    <ul class="text-gray-600 space-y-2">
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-2"></i>
                            <span>Provide vehicle details and specifications</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-2"></i>
                            <span>Upload required photos and video</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-2"></i>
                            <span>Submit ownership documentation</span>
                        </li>
                    </ul>
                </div>

                <!-- Step 3 -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="step-number mx-auto mb-4">3</div>
                    <h3 class="text-xl font-bold mb-3 text-center">Get Approved & Go Live</h3>
                    <p class="text-gray-600 mb-4">Our admin team reviews your listing within 24 hours for approval.</p>
                    <ul class="text-gray-600 space-y-2">
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-2"></i>
                            <span>Admin reviews listing details and documents</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-2"></i>
                            <span>Approved listings go live in Marketplace or Auction</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-2"></i>
                            <span>Start receiving bids or offers</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Package Options -->
        <div class="mb-16">
            <h2 class="text-3xl font-bold text-center mb-12">Seller Package Options</h2>
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Casual Seller -->
                <div class="package-card bg-white rounded-lg shadow-md p-6 border border-gray-200">
                    <div class="text-center mb-6">
                        <h3 class="text-2xl font-bold mb-2">Casual Seller</h3>
                        <div class="text-3xl font-bold gradient-text">$65 <span class="text-lg text-gray-500">per listing</span></div>
                    </div>
                    <ul class="space-y-4 mb-6">
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                            <span>1 active listing per purchase</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                            <span>30-day listing duration</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                            <span>Access to Marketplace only</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-times text-red-500 mt-1 mr-3"></i>
                            <span>No seller dashboard access</span>
                        </li>
                    </ul>
                    <button class="w-full py-3 bg-gray-800 text-white rounded-lg font-semibold hover:bg-gray-700 transition duration-300">
                        Select Package
                    </button>
                </div>

                <!-- Standard Seller -->
                <div class="package-card bg-white rounded-lg shadow-md p-6 border-2 border-purple-500 package-popular">
                    <div class="text-center mb-6">
                        <h3 class="text-2xl font-bold mb-2">Standard Seller</h3>
                        <div class="text-3xl font-bold gradient-text">$150 <span class="text-lg text-gray-500">/year</span></div>
                    </div>
                    <ul class="space-y-4 mb-6">
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                            <span>Up to 2 active listings per month</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                            <span>Marketplace or Auction listings</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                            <span>Full seller dashboard access</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-times text-red-500 mt-1 mr-3"></i>
                            <span>No Buy Now option</span>
                        </li>
                    </ul>
                    <button class="w-full py-3 gradient-bg text-white rounded-lg font-semibold hover:opacity-90 transition duration-300">
                        Select Package
                    </button>
                </div>

                <!-- Advanced Seller -->
                <div class="package-card bg-white rounded-lg shadow-md p-6 border border-gray-200">
                    <div class="text-center mb-6">
                        <h3 class="text-2xl font-bold mb-2">Advanced Seller</h3>
                        <div class="text-3xl font-bold gradient-text">$500 <span class="text-lg text-gray-500">/year</span></div>
                    </div>
                    <ul class="space-y-4 mb-6">
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                            <span>Up to 10 active listings per month</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                            <span>Marketplace and Auction listings</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                            <span>Buy Now feature enabled</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                            <span>Assigned account manager</span>
                        </li>
                    </ul>
                    <button class="w-full py-3 bg-gray-800 text-white rounded-lg font-semibold hover:bg-gray-700 transition duration-300">
                        Select Package
                    </button>
                </div>
            </div>
        </div>

        <!-- Listing Requirements -->
        <div class="bg-white rounded-lg shadow-md p-8 mb-16">
            <h2 class="text-3xl font-bold text-center mb-8">Listing Requirements</h2>
            <div class="grid md:grid-cols-2 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-4">Required Information</h3>
                    <ul class="space-y-3">
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-blue-500 mt-1 mr-3"></i>
                            <span>Title/Name of Vehicle</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-blue-500 mt-1 mr-3"></i>
                            <span>Make and Model</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-blue-500 mt-1 mr-3"></i>
                            <span>Color</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-blue-500 mt-1 mr-3"></i>
                            <span>Year of Manufacture</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-blue-500 mt-1 mr-3"></i>
                            <span>Island (location)</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-blue-500 mt-1 mr-3"></i>
                            <span>Damage Type (if any)</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-blue-500 mt-1 mr-3"></i>
                            <span>Vehicle Condition</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-blue-500 mt-1 mr-3"></i>
                            <span>Vehicle Category</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-blue-500 mt-1 mr-3"></i>
                            <span>Description</span>
                        </li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-xl font-bold mb-4">Required Media & Documentation</h3>
                    <ul class="space-y-3">
                        <li class="flex items-start">
                            <i class="fas fa-images text-blue-500 mt-1 mr-3"></i>
                            <span>Photos: Front, Back, Left Side, Right Side, Under the Hood</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-video text-blue-500 mt-1 mr-3"></i>
                            <span>Video showing vehicle running or startup attempt</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-file-alt text-blue-500 mt-1 mr-3"></i>
                            <span>Copy of title or proof of ownership</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-id-card text-blue-500 mt-1 mr-3"></i>
                            <span>Two forms of seller documentation (ID, business license, etc.)</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="gradient-bg text-white rounded-2xl p-8 text-center">
            <h2 class="text-3xl font-bold mb-4">Ready to Start Selling?</h2>
            <p class="text-xl mb-6 max-w-2xl mx-auto">Join thousands of successful sellers on CayMark's trusted platform</p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <button class="px-8 py-3 bg-white text-purple-700 rounded-lg font-bold hover:bg-gray-100 transition duration-300">
                    Register as Seller
                </button>
                <button class="px-8 py-3 bg-transparent border-2 border-white text-white rounded-lg font-bold hover:bg-white hover:text-purple-700 transition duration-300">
                    View All Packages
                </button>
            </div>
        </div>
    </main>
</body>

@endsection
