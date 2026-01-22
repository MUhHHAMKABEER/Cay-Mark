@extends('layouts.welcome')

@section('content')

<style>
    .help-hero {
        background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 50%, #1e40af 100%);
        position: relative;
        overflow: hidden;
    }
    
    .help-hero::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 50%;
        height: 100%;
        background-image: url('{{ asset('images/help-bg.jpg') }}');
        background-size: cover;
        background-position: center;
        opacity: 0.2;
        filter: blur(20px);
    }
    
    .faq-card {
        background: white;
        border-radius: 1rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        padding: 1.5rem;
        transition: all 0.3s ease;
        border: 1px solid #e5e7eb;
    }
    
    .faq-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
        border-color: #3b82f6;
    }
    
    .faq-icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        margin-bottom: 1rem;
    }
    
    .faq-question {
        color: #1f2937;
        font-weight: 600;
        margin-bottom: 0.5rem;
        padding-left: 1.5rem;
        position: relative;
    }
    
    .faq-question::before {
        content: counter(faq-counter);
        counter-increment: faq-counter;
        position: absolute;
        left: 0;
        top: 0;
        color: #3b82f6;
        font-weight: 700;
    }
    
    .faq-category {
        counter-reset: faq-counter;
    }
    
    .search-box {
        background: white;
        border-radius: 1rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        padding: 1.5rem;
        border: 2px solid #e5e7eb;
    }
    
    .search-input {
        border: 2px solid #e5e7eb;
        border-radius: 0.75rem;
        padding: 0.875rem 1rem;
        transition: all 0.3s ease;
    }
    
    .search-input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    .talk-to-us-card {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        border-radius: 1rem;
        padding: 2rem;
        color: white;
        box-shadow: 0 10px 30px rgba(59, 130, 246, 0.3);
    }
    
    .ad-card {
        background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
        border-radius: 1rem;
        padding: 2rem;
        color: white;
        position: relative;
        overflow: hidden;
    }
    
    .ad-card::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
    }
</style>

<!-- Hero Section -->
<section class="help-hero py-20 px-4 relative">
    <div class="container mx-auto max-w-7xl relative z-10">
        <div class="text-center text-white mb-8">
            <h1 class="text-4xl md:text-6xl font-extrabold mb-4 font-heading drop-shadow-2xl">How can we help you?</h1>
        </div>
        
        <!-- Search Bar -->
        <div class="max-w-3xl mx-auto">
            <div class="search-box">
                <form action="#" method="GET" class="relative">
                    <input 
                        type="text" 
                        name="search"
                        placeholder="Search the Help Center" 
                        class="w-full search-input pr-12"
                        value="{{ request('search') }}"
                    >
                    <button type="submit" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-blue-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Main Content -->
<section class="py-12 px-4 bg-gray-50">
    <div class="container mx-auto max-w-7xl">
        <div class="grid lg:grid-cols-3 gap-8">
            <!-- Left and Middle Columns - FAQ Categories -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Registration & Membership FAQ -->
                <div class="faq-card faq-category">
                    <div class="flex items-center mb-4">
                        <div class="faq-icon">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900 ml-3">Registration & Membership</h2>
                    </div>
                    <div class="space-y-3">
                        <div class="faq-question">How do I create a CayMark account?</div>
                        <div class="faq-question">What information do I need to sign up?</div>
                        <div class="faq-question">Is registration on CayMark free?</div>
                        <div class="faq-question">What's the difference between Buyer and Seller accounts?</div>
                        <div class="faq-question">Can I register as both a Buyer and Seller?</div>
                    </div>
                    <a href="#registration-membership" class="text-blue-600 hover:text-blue-800 font-semibold mt-4 inline-block">
                        View All 12 Questions & Answers →
                    </a>
                </div>

                <!-- Payments & Deposits FAQ -->
                <div class="faq-card faq-category">
                    <div class="flex items-center mb-4">
                        <div class="faq-icon">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900 ml-3">Payments & Deposits</h2>
                    </div>
                    <div class="space-y-3">
                        <div class="faq-question">What payment methods does CayMark accept?</div>
                        <div class="faq-question">Do Sellers need to pay a deposit?</div>
                        <div class="faq-question">When do Buyers need to pay a deposit?</div>
                        <div class="faq-question">Can my deposit be applied toward the final payment?</div>
                        <div class="faq-question">How do I place a deposit?</div>
                    </div>
                    <a href="#payments-deposits" class="text-blue-600 hover:text-blue-800 font-semibold mt-4 inline-block">
                        View All 7 Questions & Answers →
                    </a>
                </div>

                <!-- Auction FAQ -->
                <div class="faq-card faq-category">
                    <div class="flex items-center mb-4">
                        <div class="faq-icon">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900 ml-3">Auction</h2>
                    </div>
                    <div class="space-y-3">
                        <div class="faq-question">Who can participate in auctions?</div>
                        <div class="faq-question">What's the difference between an auction and Buy Now?</div>
                        <div class="faq-question">How do I place a bid?</div>
                        <div class="faq-question">What happens if the auction has a reserve price?</div>
                        <div class="faq-question">How will I know if I have been outbid?</div>
                    </div>
                    <a href="#auction" class="text-blue-600 hover:text-blue-800 font-semibold mt-4 inline-block">
                        View All 10 Questions & Answers →
                    </a>
                </div>

                <!-- Vehicle Condition & Damage FAQ -->
                <div class="faq-card faq-category">
                    <div class="flex items-center mb-4">
                        <div class="faq-icon">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900 ml-3">Vehicle Condition & Damage</h2>
                    </div>
                    <div class="space-y-3">
                        <div class="faq-question">Can I inspect the vehicle before bidding?</div>
                        <div class="faq-question">What should I do at pickup?</div>
                        <div class="faq-question">What does mechanical damage mean?</div>
                        <div class="faq-question">Can I buy a vehicle without keys?</div>
                    </div>
                    <a href="#vehicle-condition" class="text-blue-600 hover:text-blue-800 font-semibold mt-4 inline-block">
                        View All 4 Questions & Answers →
                    </a>
                </div>

                <!-- Pickup & Post-Sale FAQ -->
                <div class="faq-card faq-category">
                    <div class="flex items-center mb-4">
                        <div class="faq-icon">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900 ml-3">Pickup & Post-Sale</h2>
                    </div>
                    <div class="space-y-3">
                        <div class="faq-question">When can I pick up my vehicle?</div>
                        <div class="faq-question">Can I pick up early?</div>
                        <div class="faq-question">What if I need to reschedule a pickup?</div>
                        <div class="faq-question">What happens if I do not pick up my vehicle on time?</div>
                        <div class="faq-question">Can I authorize someone else to pick up my vehicle?</div>
                    </div>
                    <a href="#pickup-post-sale" class="text-blue-600 hover:text-blue-800 font-semibold mt-4 inline-block">
                        View All 7 Questions & Answers →
                    </a>
                </div>
            </div>

            <!-- Right Column - Contact and Advertisement -->
            <div class="space-y-6">
                <!-- Talk to Us Card -->
                <div class="talk-to-us-card">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold">Talk to Us</h3>
                    </div>
                    <p class="text-blue-100 mb-4 leading-relaxed">
                        Want to talk to a person? Give us a call at
                    </p>
                    <a href="tel:+12421234567" class="text-2xl font-bold mb-2 block hover:underline">
                        +1 (242) 123-4567
                    </a>
                    <p class="text-blue-100 text-sm">
                        Mon-Fri: 9 AM - 6 PM ET
                    </p>
                </div>

                <!-- Advertisement Card -->
                <div class="ad-card relative">
                    <h3 class="text-3xl font-extrabold mb-4 relative z-10">UNMATCHED VEHICLE VARIETY</h3>
                    <p class="text-blue-100 mb-6 relative z-10">
                        Explore over 700 auctions for the best vehicle selection
                    </p>
                    <div class="mb-6 relative z-10">
                        <div class="text-2xl font-bold mb-2">BUY CARS AT AUCTIONS WITH EASE!</div>
                        <p class="text-blue-100">
                            No bid card or dealer license required with CayMark
                        </p>
                    </div>
                    <a href="{{ route('Auction.index') }}" class="inline-flex items-center bg-white text-blue-600 font-bold py-3 px-6 rounded-lg hover:bg-blue-50 transition-colors relative z-10">
                        Start Bidding Now
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Detailed FAQ Sections (Expandable) -->
<section class="py-12 px-4 bg-white">
    <div class="container mx-auto max-w-7xl">
        <!-- Registration & Membership -->
        <div id="registration-membership" class="mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-6">Registration & Membership FAQ</h2>
            <div class="space-y-6">
                <div class="faq-card">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">1. How do I create a CayMark account?</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Click the Register button and enter your full name, email address, phone number, and password. Once the account is created, you will be guided through the next steps to select your membership and upload your verification documents.
                    </p>
                </div>
                <div class="faq-card">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">2. What information do I need to sign up?</h3>
                    <p class="text-gray-600 leading-relaxed">
                        <strong>Buyers</strong> must provide their name, email, phone number, password, and one government-issued ID.<br>
                        <strong>Casual Sellers</strong> must upload their name, email, phone number, password, and one government-issued ID.<br>
                        <strong>Business Sellers</strong> must upload their name, email, phone number, password, a valid business license, and an ID for the authorized account holder.
                    </p>
                </div>
                <div class="faq-card">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">3. Is registration on CayMark free?</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Yes. Creating an account on CayMark is completely free.
                    </p>
                </div>
                <div class="faq-card">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">4. What's the difference between Buyer and Seller accounts?</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Each role gives different abilities. Buyers can browse, bid, save listings, and purchase vehicles. Sellers can create listings, run auctions, and manage post-sale pickup and payout activities. Buyers and Sellers cannot perform each other's functions within the same account.
                    </p>
                </div>
                <div class="faq-card">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">5. Can I register as both a Buyer and Seller?</h3>
                    <p class="text-gray-600 leading-relaxed">
                        No. Members must choose a single role. Operating multiple accounts is prohibited and may result in penalties. You must select either Buyer or Seller during registration.
                    </p>
                </div>
                <div class="faq-card">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">6. What are the available membership plans?</h3>
                    <p class="text-gray-600 leading-relaxed">
                        <strong>Standard Buyer:</strong> $64.99 per year.<br>
                        <strong>Casual Seller:</strong> $25.00 per listing.<br>
                        <strong>Business Seller:</strong> $599.99 per year.
                    </p>
                </div>
                <div class="faq-card">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">7. What are the differences between the Seller plans?</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Casual Sellers pay $25.00 per listing and can publish multiple listings when needed. This plan is best for occasional sellers.<br>
                        Business Sellers pay an annual fee and receive unlimited listings, advanced seller tools, inventory management features, priority support, and access to a dedicated account manager.
                    </p>
                </div>
                <div class="faq-card">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">8. How long does my membership last?</h3>
                    <p class="text-gray-600 leading-relaxed">
                        All memberships are annual. When your membership expires, your account will return to Guest mode until you renew.
                    </p>
                </div>
                <div class="faq-card">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">9. Can I upgrade my membership?</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Only Casual Sellers are allowed to upgrade to a Business Seller plan at any time. Upgrades take effect immediately after payment.
                    </p>
                </div>
                <div class="faq-card">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">10. What happens if my payment for membership fails?</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Your membership will not activate. You can retry your payment from your dashboard. Until payment is successful and your documents are approved, your account will remain in Guest mode.
                    </p>
                </div>
                <div class="faq-card">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">11. How do I update my personal information?</h3>
                    <p class="text-gray-600 leading-relaxed">
                        You may update your name, phone number, and email address at any time through your Account Settings.
                    </p>
                </div>
                <div class="faq-card">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">12. How do I reset my password?</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Select Forgot Password on the login page. A reset link will be sent to your email.
                    </p>
                </div>
            </div>
        </div>

        <!-- Payments & Deposits -->
        <div id="payments-deposits" class="mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-6">Payments & Deposits FAQ</h2>
            <div class="space-y-6">
                <div class="faq-card">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">1. What payment methods does CayMark accept?</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Credit and debit cards are accepted for deposits, membership payments, and auction payments.
                    </p>
                </div>
                <div class="faq-card">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">2. Do Sellers need to pay a deposit?</h3>
                    <p class="text-gray-600 leading-relaxed">
                        No. Deposits only apply to Buyers placing bids above their deposit-free limit.
                    </p>
                </div>
                <div class="faq-card">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">3. When do Buyers need to pay a deposit?</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Buyers must place a deposit equal to ten percent of the amount they plan to bid once they attempt to bid above two thousand dollars.
                    </p>
                </div>
                <div class="faq-card">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">4. Can my deposit be applied toward the final payment?</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Yes. Deposits are automatically applied toward the final payment amount if you win an auction.
                    </p>
                </div>
                <div class="faq-card">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">5. How do I place a deposit?</h3>
                    <p class="text-gray-600 leading-relaxed">
                        You may add a deposit from the Wallet section in your dashboard.
                    </p>
                </div>
                <div class="faq-card">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">6. Are deposits refundable?</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Yes. Deposits are refundable unless your account violates CayMark policies. Refunds typically take three to five business days after your request or after account closure.
                    </p>
                </div>
                <div class="faq-card">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">7. How fast are payouts for Sellers?</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Payouts are usually processed within three to five business days after the seller has confirmed pickup using the secure PIN system.
                    </p>
                </div>
            </div>
        </div>

        <!-- Auction -->
        <div id="auction" class="mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-6">Auction FAQ</h2>
            <div class="space-y-6">
                <div class="faq-card">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">1. Who can participate in auctions?</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Only members with a verified account and an active Standard Buyer membership may place bids.
                    </p>
                </div>
                <div class="faq-card">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">2. What's the difference between an auction and Buy Now?</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Auction items allow competitive bidding until the deadline. Buy Now items can be purchased immediately at a set price.
                    </p>
                </div>
                <div class="faq-card">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">3. How do I place a bid?</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Enter your bid on the auction page. Your bid must meet the minimum allowed amount. All bids are final.
                    </p>
                </div>
                <div class="faq-card">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">4. What happens if the auction has a reserve price?</h3>
                    <p class="text-gray-600 leading-relaxed">
                        If the highest bid does not meet the reserve price, the seller may decide not to complete the sale.
                    </p>
                </div>
                <div class="faq-card">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">5. How will I know if I have been outbid?</h3>
                    <p class="text-gray-600 leading-relaxed">
                        You will receive a notification through your dashboard and email.
                    </p>
                </div>
                <div class="faq-card">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">6. What happens if I win an auction?</h3>
                    <p class="text-gray-600 leading-relaxed">
                        You will be required to make payment within forty-eight hours. Once your payment is successful, the pickup coordination thread will unlock.
                    </p>
                </div>
                <div class="faq-card">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">7. What happens if I do not pay on time?</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Your deposit may be forfeited, and your account may receive temporary restrictions or review. The seller may relist the item.
                    </p>
                </div>
                <div class="faq-card">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">8. Can I cancel a bid after placing it?</h3>
                    <p class="text-gray-600 leading-relaxed">
                        No. Auction bids cannot be withdrawn or reversed.
                    </p>
                </div>
                <div class="faq-card">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">9. How will I receive the vehicle after winning?</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Pickup is coordinated through the structured messaging portal once payment is confirmed.
                    </p>
                </div>
                <div class="faq-card">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">10. What if the vehicle I receive is not as described?</h3>
                    <p class="text-gray-600 leading-relaxed">
                        You may file a dispute with photos through your CayMark dashboard. CayMark will review the issue. Depending on the severity of the case a refund may not be issued as all sales are final. Any refunds in responses to disputes are released at the discretion of the platform only.
                    </p>
                </div>
            </div>
        </div>

        <!-- Vehicle Condition & Damage -->
        <div id="vehicle-condition" class="mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-6">Vehicle Condition & Damage FAQ</h2>
            <div class="space-y-6">
                <div class="faq-card">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">1. Can I inspect the vehicle before bidding?</h3>
                    <p class="text-gray-600 leading-relaxed">
                        No. Pre-auction inspections are not allowed. All assessments should be based on photos, descriptions, and seller-provided information.
                    </p>
                </div>
                <div class="faq-card">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">2. What should I do at pickup?</h3>
                    <p class="text-gray-600 leading-relaxed">
                        You are encouraged to take clear photos and videos of the vehicle at pickup. You may also check documents such as the bill of sale, keys, or any documents the seller is providing. You may bring a mechanic if you want additional peace of mind. This inspection is for your own documentation and does not affect the sale, which has already been completed.
                    </p>
                </div>
                <div class="faq-card">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">3. What does mechanical damage mean?</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Mechanical damage refers to issues involving the engine, transmission, or internal components affecting the vehicle's operation.
                    </p>
                </div>
                <div class="faq-card">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">4. Can I buy a vehicle without keys?</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Yes, if the listing indicates that keys are not included. It is your responsibility to verify this before bidding.
                    </p>
                </div>
            </div>
        </div>

        <!-- Pickup & Post-Sale -->
        <div id="pickup-post-sale" class="mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-6">Pickup & Post-Sale FAQ</h2>
            <div class="space-y-6">
                <div class="faq-card">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">1. When can I pick up my vehicle?</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Pickup is allowed only after payment is confirmed and the seller submits the pickup details through the structured messaging portal.
                    </p>
                </div>
                <div class="faq-card">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">2. Can I pick up early?</h3>
                    <p class="text-gray-600 leading-relaxed">
                        No. Pickup is only allowed after the auction ends and full payment is successful.
                    </p>
                </div>
                <div class="faq-card">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">3. What if I need to reschedule a pickup?</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Buyers may request a new pickup date or time through the messaging portal. Both parties must approve the updated appointment.
                    </p>
                </div>
                <div class="faq-card">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">4. What happens if I do not pick up my vehicle on time?</h3>
                    <p class="text-gray-600 leading-relaxed">
                        You may request a rescheduled pickup through the messaging portal. In more serious cases, the seller may still receive payout if extended delays occur. Your account may receive temporary restrictions or review depending on the situation.
                    </p>
                </div>
                <div class="faq-card">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">5. Can I authorize someone else to pick up my vehicle?</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Yes. Buyers may authorize a tow company or another individual through the Authorize Third-Party Pickup option inside the messaging thread.
                    </p>
                </div>
                <div class="faq-card">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">6. How does the Pickup PIN work?</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Once payment is successful, a four-digit PIN is generated. The buyer or authorized representative must present this PIN at pickup. The seller enters the PIN inside their dashboard to confirm release of the vehicle and to begin processing seller payment.
                    </p>
                </div>
                <div class="faq-card">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">7. What should I do if there is a problem at pickup?</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Contact a CayMark representative immediately through the Contact Us form, or use the official support phone number listed on the site. Provide photos and details of the issue.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
