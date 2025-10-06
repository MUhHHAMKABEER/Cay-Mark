@extends('layouts.welcome')

@section('content')


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - CayMark</title>
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
        .form-input {
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        .form-input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Main Content -->
    <main class="py-12 px-4 max-w-7xl mx-auto">
        <!-- Hero Section -->
        <div class="text-center mb-16">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Contact Us</h1>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">Get in touch with our team for assistance, inquiries, or partnership opportunities</p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            <!-- Contact Information -->
            <div class="md:col-span-1">
                <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                    <h3 class="text-xl font-bold mb-4">Contact Information</h3>
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-envelope text-blue-500"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold">Email</h4>
                                <p class="text-gray-600">support@caymark.com</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-phone text-blue-500"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold">Phone</h4>
                                <p class="text-gray-600">+1 (242) 123-4567</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-map-marker-alt text-blue-500"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold">Office Address</h4>
                                <p class="text-gray-600">123 Bay Street<br>Nassau, Bahamas</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-xl shadow-md p-6">
                    <h3 class="text-xl font-bold mb-4">Business Hours</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span>Monday - Friday</span>
                            <span>9:00 AM - 6:00 PM</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Saturday</span>
                            <span>10:00 AM - 4:00 PM</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Sunday</span>
                            <span>Closed</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="md:col-span-2">
                <div class="bg-white rounded-xl shadow-md p-6">
                    <h3 class="text-xl font-bold mb-6">Send Us a Message</h3>
                    <form class="space-y-6">
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-gray-700 font-medium mb-2">Full Name</label>
                                <input type="text" id="name" class="w-full px-4 py-3 border border-gray-300 rounded-lg form-input focus:outline-none focus:border-blue-500" placeholder="Enter your full name">
                            </div>
                            <div>
                                <label for="email" class="block text-gray-700 font-medium mb-2">Email Address</label>
                                <input type="email" id="email" class="w-full px-4 py-3 border border-gray-300 rounded-lg form-input focus:outline-none focus:border-blue-500" placeholder="Enter your email address">
                            </div>
                        </div>
                        <div>
                            <label for="subject" class="block text-gray-700 font-medium mb-2">Subject</label>
                            <input type="text" id="subject" class="w-full px-4 py-3 border border-gray-300 rounded-lg form-input focus:outline-none focus:border-blue-500" placeholder="What is this regarding?">
                        </div>
                        <div>
                            <label for="message" class="block text-gray-700 font-medium mb-2">Message</label>
                            <textarea id="message" rows="6" class="w-full px-4 py-3 border border-gray-300 rounded-lg form-input focus:outline-none focus:border-blue-500" placeholder="How can we help you?"></textarea>
                        </div>
                        <button type="submit" class="w-full py-3 gradient-bg text-white rounded-lg font-bold hover:opacity-90 transition duration-300">
                            Send Message
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- FAQ Section -->
        <div class="mt-16">
            <h2 class="text-3xl font-bold text-center mb-8">Frequently Asked Questions</h2>
            <div class="grid md:grid-cols-2 gap-8">
                <div class="bg-white rounded-xl shadow-md p-6">
                    <h3 class="text-xl font-bold mb-4">How long does listing approval take?</h3>
                    <p class="text-gray-600">Our admin team reviews all listings within 24 hours of submission. You'll receive a notification once your listing is approved or if any changes are needed.</p>
                </div>
                <div class="bg-white rounded-xl shadow-md p-6">
                    <h3 class="text-xl font-bold mb-4">Can I change my package later?</h3>
                    <p class="text-gray-600">Yes, you can upgrade your package at any time. Downgrades will take effect at the end of your current billing cycle.</p>
                </div>
                <div class="bg-white rounded-xl shadow-md p-6">
                    <h3 class="text-xl font-bold mb-4">What payment methods do you accept?</h3>
                    <p class="text-gray-600">We accept all major credit cards through our secure Stripe integration. For enterprise clients, we also offer invoice payment options.</p>
                </div>
                <div class="bg-white rounded-xl shadow-md p-6">
                    <h3 class="text-xl font-bold mb-4">How does the escrow system work?</h3>
                    <p class="text-gray-600">When a purchase is made, funds are held in escrow until the buyer confirms receipt of the vehicle. The seller then receives payment after admin approval.</p>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
@endsection
