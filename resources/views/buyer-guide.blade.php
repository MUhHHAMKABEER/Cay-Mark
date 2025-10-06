@extends('layouts.welcome')
@section('content')
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buyer's Guide - CayMark</title>
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
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Buyer's Guide</h1>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">Learn how to find and purchase quality vehicles through CayMark's Marketplace and Auctions</p>
        </div>

        <!-- Buyer Packages -->
        <div class="mb-16">
            <h2 class="text-3xl font-bold text-center mb-12">Buyer Package Options</h2>
            <div class="grid md:grid-cols-2 gap-8 max-w-4xl mx-auto">
                <!-- Basic Buyer -->
                <div class="package-card bg-white rounded-lg shadow-md p-6 border border-gray-200">
                    <div class="text-center mb-6">
                        <h3 class="text-2xl font-bold mb-2">Basic Buyer</h3>
                        <div class="text-3xl font-bold gradient-text">$49.99 <span class="text-lg text-gray-500">/year</span></div>
                    </div>
                    <ul class="space-y-4 mb-6">
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                            <span>Access to Marketplace and Auctions</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                            <span>Auction bidding limit: $2,000</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                            <span>Limited to one bid at a time</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-times text-red-500 mt-1 mr-3"></i>
                            <span>No unlimited bidding</span>
                        </li>
                    </ul>
                    <button class="w-full py-3 bg-gray-800 text-white rounded-lg font-semibold hover:bg-gray-700 transition duration-300">
                        Select Package
                    </button>
                </div>

                <!-- Premium Buyer -->
                <div class="package-card bg-white rounded-lg shadow-md p-6 border-2 border-purple-500 package-popular">
                    <div class="text-center mb-6">
                        <h3 class="text-2xl font-bold mb-2">Premium Buyer</h3>
                        <div class="text-3xl font-bold gradient-text">$99.99 <span class="text-lg text-gray-500">/year</span></div>
                    </div>
                    <ul class="space-y-4 mb-6">
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                            <span>Access to Marketplace and Auctions</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                            <span>No auction bidding limit</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                            <span>Unlimited bids</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                            <span>Priority customer support</span>
                        </li>
                    </ul>
                    <button class="w-full py-3 gradient-bg text-white rounded-lg font-semibold hover:opacity-90 transition duration-300">
                        Select Package
                    </button>
                </div>
            </div>
        </div>

        <!-- Step-by-Step Guide -->
        <div class="mb-16">
            <h2 class="text-3xl font-bold text-center mb-12">How to Buy on CayMark</h2>
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Step 1 -->
                <div class="bg-white rounded-lg shadow-md p-6 text-center">
                    <div class="step-number mx-auto mb-4">1</div>
                    <h3 class="text-xl font-bold mb-3">Register</h3>
                    <p class="text-gray-600">Create your buyer account and select your package (Basic or Premium).</p>
                </div>

                <!-- Step 2 -->
                <div class="bg-white rounded-lg shadow-md p-6 text-center">
                    <div class="step-number mx-auto mb-4">2</div>
                    <h3 class="text-xl font-bold mb-3">Browse</h3>
                    <p class="text-gray-600">Explore the Marketplace for fixed-price listings or Auctions for bidding opportunities.</p>
                </div>

                <!-- Step 3 -->
                <div class="bg-white rounded-lg shadow-md p-6 text-center">
                    <div class="step-number mx-auto mb-4">3</div>
                    <h3 class="text-xl font-bold mb-3">Participate</h3>
                    <p class="text-gray-600">Place bids in auctions or use Buy Now for immediate purchases in the Marketplace.</p>
                </div>

                <!-- Step 4 -->
                <div class="bg-white rounded-lg shadow-md p-6 text-center">
                    <div class="step-number mx-auto mb-4">4</div>
                    <h3 class="text-xl font-bold mb-3">Complete Purchase</h3>
                    <p class="text-gray-600">Follow payment instructions and coordinate pickup/delivery of your vehicle.</p>
                </div>
            </div>
        </div>

        <!-- Bidding Restrictions -->
        <div class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-2xl p-8 mb-16">
            <h2 class="text-3xl font-bold text-center mb-8">Bidding Restrictions</h2>
            <div class="grid md:grid-cols-2 gap-8">
                <div class="bg-white rounded-lg p-6">
                    <h3 class="text-xl font-bold mb-4 text-center">Basic Buyers</h3>
                    <ul class="space-y-3">
                        <li class="flex items-start">
                            <i class="fas fa-exclamation-circle text-yellow-500 mt-1 mr-3"></i>
                            <span>Cannot place bids over $2,000</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-exclamation-circle text-yellow-500 mt-1 mr-3"></i>
                            <span>Cannot bid on items with current bid at or above $2,000</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-exclamation-circle text-yellow-500 mt-1 mr-3"></i>
                            <span>Limited to one active bid at a time</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-exclamation-circle text-yellow-500 mt-1 mr-3"></i>
                            <span>Must wait for auction to end before bidding on another vehicle</span>
                        </li>
                    </ul>
                </div>
                <div class="bg-white rounded-lg p-6">
                    <h3 class="text-xl font-bold mb-4 text-center">Premium Buyers</h3>
                    <ul class="space-y-3">
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                            <span>No bidding limits or restrictions</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                            <span>Unlimited bids on multiple auctions</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                            <span>Can bid on any item regardless of current bid amount</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="gradient-bg text-white rounded-2xl p-8 text-center">
            <h2 class="text-3xl font-bold mb-4">Start Your Vehicle Search Today</h2>
            <p class="text-xl mb-6 max-w-2xl mx-auto">Join thousands of satisfied buyers finding their perfect vehicles on CayMark</p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <button class="px-8 py-3 bg-white text-purple-700 rounded-lg font-bold hover:bg-gray-100 transition duration-300">
                    Register as Buyer
                </button>
                <button class="px-8 py-3 bg-transparent border-2 border-white text-white rounded-lg font-bold hover:bg-white hover:text-purple-700 transition duration-300">
                    Browse Listings
                </button>
            </div>
        </div>
    </main>
</body>



@endsection
