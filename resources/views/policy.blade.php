@extends('layouts.welcome')

@section('content')
<section class="py-20 px-4 bg-white">
    <div class="container mx-auto max-w-4xl">
        <h1 class="text-4xl font-bold mb-8 text-gray-900 border-b pb-4">Policy</h1>
        
        <div class="space-y-12 text-gray-700 leading-relaxed">
            <!-- Rules & Policies Section -->
            <section>
                <h2 class="text-2xl font-bold text-blue-800 mb-4">General Rules & Policies</h2>
                <p class="mb-4">Welcome to CayMark. By using our platform, you agree to the following rules and policies:</p>
                <ul class="list-disc pl-6 space-y-2">
                    <li><strong>All Sales Are Final:</strong> Every vehicle or item sold on CayMark is sold "as is, where is." No refunds or returns are allowed once a bid is accepted or a Buy Now purchase is confirmed.</li>
                    <li><strong>Binding Bids:</strong> Every bid placed is a legally binding contract. If you win an auction, you are obligated to complete the purchase.</li>
                    <li><strong>Bidding Conduct:</strong> Any attempt to manipulate auction results, including shill bidding or colluding with other users, is strictly prohibited and will result in permanent account suspension.</li>
                    <li><strong>Payment Deadline:</strong> Winning bidders must initiate payment within 48 hours of auction end. Failure to do so may result in the forfeiture of your security deposit.</li>
                </ul>
            </section>

            <!-- Auction Policies -->
            <section>
                <h2 class="text-2xl font-bold text-blue-800 mb-4">Auction Policies</h2>
                <div class="space-y-4">
                    <div class="bg-gray-50 p-6 rounded-lg border-l-4 border-blue-500">
                        <h3 class="font-bold text-lg mb-2">Anti-Sniping Policy</h3>
                        <p>To ensure a fair bidding environment, CayMark employs an anti-sniping feature. If a bid is placed within the final 60 seconds of an auction, the countdown will automatically extend by an additional 60 seconds.</p>
                    </div>
                    
                    <div class="bg-gray-50 p-6 rounded-lg border-l-4 border-blue-500">
                        <h3 class="font-bold text-lg mb-2">Reserve Price Policy</h3>
                        <p>Some auctions may have a reserve price. If the final bid does not meet the reserve, the Seller is not obligated to sell the vehicle, although they may choose to accept the highest bid or negotiate with the top bidder.</p>
                    </div>
                </div>
            </section>

            <!-- Dispute Resolution -->
            <section>
                <h2 class="text-2xl font-bold text-blue-800 mb-4">Dispute Resolution</h2>
                <p>While atypical, disputes regarding vehicle condition or delivery may occur. Buyers may file a dispute through their dashboard within 24 hours of pickup if they believe the vehicle was significantly misrepresented. CayMark reserves the right to make final determinations on all disputes.</p>
            </section>
        </div>
    </div>
</section>
@endsection
