@extends('layouts.welcome')
@section('content')


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enterprise Seller - CayMark</title>
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
    </style>
</head>
<body class="bg-gray-50">
    <!-- Main Content -->
    <main class="py-12 px-4 max-w-7xl mx-auto">
        <!-- Hero Section -->
        <div class="text-center mb-16">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Enterprise Seller Program</h1>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">Tailored solutions for large businesses, dealerships, banks, and insurance companies</p>
        </div>

        <!-- Features Section -->
        <div class="mb-16">
            <h2 class="text-3xl font-bold text-center mb-12">Premium Enterprise Features</h2>
            <div class="grid md:grid-cols-2 gap-8">
                <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
                    <div class="feature-icon mx-auto mb-4">
                        <i class="fas fa-infinity text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-center">Unlimited Listings</h3>
                    <p class="text-gray-600 text-center">List as many vehicles as you need with no monthly limits or restrictions.</p>
                </div>

                <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
                    <div class="feature-icon mx-auto mb-4">
                        <i class="fas fa-user-tie text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-center">Dedicated Account Manager</h3>
                    <p class="text-gray-600 text-center">Your personal contact for support, strategy, and platform optimization.</p>
                </div>

                <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
                    <div class="feature-icon mx-auto mb-4">
                        <i class="fas fa-tachometer-alt text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-center">Full Dashboard Access</h3>
                    <p class="text-gray-600 text-center">Complete control with advanced analytics, reporting, and management tools.</p>
                </div>

                <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
                    <div class="feature-icon mx-auto mb-4">
                        <i class="fas fa-headset text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-center">Premium Support</h3>
                    <p class="text-gray-600 text-center">Priority assistance with 24/7 access to our enterprise support team.</p>
                </div>
            </div>
        </div>

        <!-- Benefits Section -->
        <div class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-2xl p-8 mb-16">
            <h2 class="text-3xl font-bold text-center mb-8">Enterprise Benefits</h2>
            <div class="grid md:grid-cols-2 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-4">For Large Businesses</h3>
                    <ul class="space-y-3">
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                            <span>Bulk listing capabilities</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                            <span>Custom API integration options</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                            <span>White-label solutions available</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                            <span>Advanced reporting and analytics</span>
                        </li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-xl font-bold mb-4">For Dealerships</h3>
                    <ul class="space-y-3">
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                            <span>Inventory management tools</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                            <span>CRM integration capabilities</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                            <span>Team member access controls</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                            <span>Priority placement in search results</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Case Studies -->
        <div class="mb-16">
            <h2 class="text-3xl font-bold text-center mb-12">Enterprise Success Stories</h2>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white rounded-xl shadow-md p-6">
                    <div class="text-center mb-4">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-car text-blue-500 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold">Island Auto Group</h3>
                        <p class="text-gray-500">Car Dealership</p>
                    </div>
                    <p class="text-gray-600 italic text-center">"CayMark's Enterprise program helped us increase our online sales by 45% in just three months. The dedicated account manager made the transition seamless."</p>
                </div>

                <div class="bg-white rounded-xl shadow-md p-6">
                    <div class="text-center mb-4">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-university text-blue-500 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold">Bahamas Financial</h3>
                        <p class="text-gray-500">Banking Institution</p>
                    </div>
                    <p class="text-gray-600 italic text-center">"The platform's escrow system and verification process gave us confidence to list repossessed vehicles securely. Our recovery rates improved significantly."</p>
                </div>

                <div class="bg-white rounded-xl shadow-md p-6">
                    <div class="text-center mb-4">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-shield-alt text-blue-500 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold">Caribbean Insurers</h3>
                        <p class="text-gray-500">Insurance Company</p>
                    </div>
                    <p class="text-gray-600 italic text-center">"Listing salvage vehicles through CayMark's Enterprise program has streamlined our claims process and maximized returns on totaled assets."</p>
                </div>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="bg-gray-900 text-white rounded-2xl p-8 text-center">
            <h2 class="text-3xl font-bold mb-4">Custom Enterprise Solutions</h2>
            <p class="text-xl mb-6 max-w-2xl mx-auto">Contact our sales team to discuss a tailored plan for your business</p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <button class="px-8 py-3 bg-blue-600 text-white rounded-lg font-bold hover:bg-blue-700 transition duration-300">
                    Contact Sales Team
                </button>
                <button class="px-8 py-3 bg-transparent border-2 border-white text-white rounded-lg font-bold hover:bg-white hover:text-gray-900 transition duration-300">
                    Request Custom Plan
                </button>
            </div>
        </div>
    </main>
</body>

@endsection
