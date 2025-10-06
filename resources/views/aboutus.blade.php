@extends('layouts.welcome')

@section('content')
    <title>About Us - CayMark</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#4F46E5',
                        primaryDark: '#4338CA',
                        secondary: '#10B981',
                        dark: '#1F2937',
                        light: '#F9FAFB'
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif']
                    }
                }
            }
        }
    </script>
    <style>
        .floating-card {
            transform-style: preserve-3d;
            transform: perspective(1000px);
        }
        .floating-element {
            transform: translateZ(20px);
        }
        .service-card {
            transition: all 0.3s ease;
        }
        .service-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }
        .parallax-bg {
            background-attachment: fixed;
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
        }
        .gradient-text {
            background: linear-gradient(135deg, #4F46E5 0%, #10B981 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>
<body class="font-sans antialiased text-gray-800 bg-light">
    <!-- Navigation would be included from layout -->

    <main>
        <!-- Hero Section with Parallax -->
        <section class="relative py-28 overflow-hidden">
            <div class="absolute inset-0 parallax-bg" style="background-image: url('https://images.unsplash.com/photo-1519389950473-47ba0277781c?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1770&q=80');"></div>
            <div class="absolute inset-0 bg-black opacity-50"></div>

            <div class="container mx-auto px-4 relative z-10 floating-card">
                <div class="max-w-3xl mx-auto text-center floating-element">
                    <h1 class="text-5xl font-bold text-white mb-6">About <span class="gradient-text">CayMark</span></h1>
                    <h2 class="text-xl text-indigo-200 font-semibold mb-8">All to Know About Our Vision & Mission</h2>
                    <p class="text-gray-200 text-lg leading-relaxed">
                        Learn more about who we are and the exciting opportunities we've created to foster
                        connection, innovation, and growth throughout The Bahamas.
                    </p>
                </div>
            </div>
        </section>

        <!-- How we became CayMark -->
        <section class="py-20 bg-white">
            <div class="container mx-auto px-4">
                <div class="flex flex-col lg:flex-row items-center gap-16">
                    <!-- Text Content -->
                    <div class="lg:w-1/2">
                        <h2 class="text-3xl font-bold text-dark mb-6">Why Choose Cay Mark <span class="text-primary">— Our Story</span></h2>
                        <div class="h-1 w-20 bg-secondary mb-6"></div>
                        <p class="text-gray-700 leading-relaxed mb-6">
                            CayMark began with a vision to make trading vehicles and marine crafts simple,
                            reliable, and accessible across the Bahamas. By combining trusted escrow processes,
                            careful listing verification, and an easy-to-use online auction platform, we built
                            a marketplace that empowers buyers and sellers alike.
                        </p>
                        <p class="text-gray-700 leading-relaxed">
                            Our journey is rooted in transparency and community — we listen to users,
                            improve quickly, and invest in features that bring value and confidence to every transaction.
                        </p>
                    </div>

                    <!-- Image with 3D effect -->
                    <div class="lg:w-1/2 relative">
                        <div class="floating-card">
                            <img src="https://images.unsplash.com/photo-1563014959-7aaa83350992?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1032&q=80"
                                 alt="CayMark story"
                                 class="rounded-xl shadow-2xl floating-element">
                        </div>
                        <div class="absolute -z-10 top-6 left-6 right-6 bottom-6 bg-gradient-to-r from-primary to-secondary rounded-xl opacity-20"></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Services Section -->
        <section class="py-20 bg-gradient-to-br from-gray-50 to-gray-100">
            <div class="container mx-auto px-4">
                <div class="text-center max-w-3xl mx-auto mb-16">
                    <h2 class="text-3xl font-bold text-dark mb-4">Why Choose a Better Bid <span class="text-primary">— Services We Offer</span></h2>
                    <div class="h-1 w-20 bg-secondary mx-auto mb-6"></div>
                    <p class="text-gray-600">Comprehensive solutions designed to make your auction experience seamless and secure</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Service Card 1 -->
                    <div class="service-card bg-white p-8 rounded-xl shadow-md border border-gray-100">
                        <div class="w-14 h-14 bg-indigo-100 rounded-lg flex items-center justify-center mb-6">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-dark mb-4">Safe & Trusted Payments</h3>
                        <p class="text-gray-600">Secure escrow services protect all parties by holding funds safely until transactions are complete.</p>
                    </div>

                    <!-- Service Card 2 -->
                    <div class="service-card bg-white p-8 rounded-xl shadow-md border border-gray-100">
                        <div class="w-14 h-14 bg-indigo-100 rounded-lg flex items-center justify-center mb-6">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-dark mb-4">Verified Listings You Can Trust</h3>
                        <p class="text-gray-600">Every listing undergoes rigorous admin approval to confirm authenticity and quality.</p>
                    </div>

                    <!-- Service Card 3 -->
                    <div class="service-card bg-white p-8 rounded-xl shadow-md border border-gray-100">
                        <div class="w-14 h-14 bg-indigo-100 rounded-lg flex items-center justify-center mb-6">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-dark mb-4">Auction Online, Anytime</h3>
                        <p class="text-gray-600">Join live auctions from anywhere across The Bahamas without leaving your home.</p>
                    </div>

                    <!-- Service Card 4 -->
                    <div class="service-card bg-white p-8 rounded-xl shadow-md border border-gray-100">
                        <div class="w-14 h-14 bg-indigo-100 rounded-lg flex items-center justify-center mb-6">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-dark mb-4">Personalized User Dashboards</h3>
                        <p class="text-gray-600">Centralized place to manage all your buying and selling activities with intuitive interface.</p>
                    </div>

                    <!-- Service Card 5 -->
                    <div class="service-card bg-white p-8 rounded-xl shadow-md border border-gray-100">
                        <div class="w-14 h-14 bg-indigo-100 rounded-lg flex items-center justify-center mb-6">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-dark mb-4">Flexible Membership Plans</h3>
                        <p class="text-gray-600">Variety of membership options designed to meet diverse needs of buyers and sellers.</p>
                    </div>

                    <!-- Service Card 6 -->
                    <div class="service-card bg-white p-8 rounded-xl shadow-md border border-gray-100">
                        <div class="w-14 h-14 bg-indigo-100 rounded-lg flex items-center justify-center mb-6">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-dark mb-4">Dedicated Customer Support</h3>
                        <p class="text-gray-600">Expert support team available via chat, email, or phone during business hours.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Mission Section -->
        <section class="py-20 bg-white">
            <div class="container mx-auto px-4">
                <div class="max-w-4xl mx-auto text-center">
                    <div class="inline-block p-2 bg-indigo-100 rounded-full mb-6">
                        <div class="bg-indigo-200 rounded-full p-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                    </div>
                    <h2 class="text-3xl font-bold text-dark mb-6">Our Mission Today</h2>
                    <p class="text-gray-600 text-lg leading-relaxed mb-10">
                        At CayMark, our mission is to connect buyers and sellers across The Bahamas through a dynamic and innovative platform
                        that makes trading vehicles and marine crafts effortless and reliable. We aim to break down barriers, making access to
                        quality listings simple for everyone—eliminating doubt and uncertainty so you can trade boldly.
                    </p>
                    <a href="#" class="inline-block px-8 py-4 bg-gradient-to-r from-primary to-primaryDark text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                        Join CayMark Today
                    </a>
                </div>
            </div>
        </section>
    </main>



@endsection
