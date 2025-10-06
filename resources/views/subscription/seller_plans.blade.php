<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buyer Subscription Plans</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .plan-card {
            transition: all 0.3s ease;
            border-top: 4px solid;
        }

        .plan-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .feature-item {
            position: relative;
            padding-left: 1.75rem;
        }

        .feature-item:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #10B981;
            font-weight: bold;
        }
    </style>
</head>

<body class="bg-gray-50">
    <div class="container mx-auto px-4 py-12 max-w-6xl">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Choose Your Buyer Plan</h1>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Select the subscription that fits your buying needs and enjoy the full marketplace experience.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Basic Buyer Plan -->
            <div class="plan-card bg-white rounded-xl p-8 border-t-blue-500 shadow-md">
                <div class="mb-6">
                    <span
                        class="inline-block px-3 py-1 text-xs font-semibold bg-blue-100 text-blue-800 rounded-full">
                        BASIC BUYER
                    </span>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Basic Buyer</h2>
                <div class="flex items-baseline mb-4">
                    <span class="text-4xl font-extrabold text-gray-900">$49.99</span>
                    <span class="text-lg text-gray-600 ml-1">/ per year</span>
                </div>
                <p class="text-gray-600 mb-6">Access the marketplace and auctions with limited bidding privileges.</p>

                <ul class="space-y-3 mb-8">
                    <li class="feature-item">Access to Marketplace and Auctions</li>
                    <li class="feature-item">Auction bidding limit: $2,000</li>
                    <li class="feature-item">Limited to one bid at a time</li>
                </ul>

                <a href="{{ route('subscription.simulate', ['plan' => 'basic']) }}"
                    class="block w-full bg-blue-600 text-white text-center py-3 rounded-lg hover:bg-blue-700 transition">
                    Get Started
                </a>
            </div>

            <!-- Premium Buyer Plan -->
            <div class="plan-card bg-white rounded-xl p-8 border-t-purple-500 shadow-md relative">
                <div
                    class="absolute top-0 right-0 bg-purple-600 text-white px-4 py-1 text-sm font-semibold rounded-bl-lg">
                    POPULAR
                </div>
                <div class="mb-6">
                    <span
                        class="inline-block px-3 py-1 text-xs font-semibold bg-purple-100 text-purple-800 rounded-full">
                        PREMIUM BUYER
                    </span>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Premium Buyer</h2>
                <div class="flex items-baseline mb-4">
                    <span class="text-4xl font-extrabold text-gray-900">$99.99</span>
                    <span class="text-lg text-gray-600 ml-1">/ per year</span>
                </div>
                <p class="text-gray-600 mb-6">Enjoy full marketplace and auction access with no bidding limits.</p>

                <ul class="space-y-3 mb-8">
                    <li class="feature-item">Access to Marketplace and Auctions</li>
                    <li class="feature-item">No auction bidding limit</li>
                    <li class="feature-item">Unlimited bids</li>
                </ul>

                <a href="{{ route('subscription.simulate', ['plan' => 'premium']) }}"
                    class="block w-full bg-purple-600 text-white text-center py-3 rounded-lg hover:bg-purple-700 transition">
                    Get Started
                </a>
            </div>
        </div>

        <div class="mt-12 text-center text-gray-600">
            <p>Need help choosing? <a href="#" class="text-blue-600 hover:underline">Contact our support team</a>
            </p>
        </div>
    </div>
</body>

</html>
