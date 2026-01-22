@extends('layouts.welcome')

@section('content')

<style>
    .calculator-hero {
        background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 50%, #1e40af 100%);
        position: relative;
        overflow: hidden;
        padding: 4rem 2rem;
    }
    
    .calculator-hero::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 50%;
        height: 100%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        opacity: 0.3;
    }
    
    .calculator-card {
        background: white;
        border-radius: 1rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        padding: 2rem;
        transition: all 0.3s ease;
        border: 1px solid #e5e7eb;
    }
    
    .calculator-card:hover {
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
    }
    
    .input-group {
        margin-bottom: 1.5rem;
    }
    
    .input-group label {
        display: block;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
    }
    
    .input-group input,
    .input-group select {
        width: 100%;
        padding: 0.75rem;
        border: 2px solid #e5e7eb;
        border-radius: 0.5rem;
        font-size: 1rem;
        transition: all 0.2s;
    }
    
    .input-group input:focus,
    .input-group select:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    .result-card {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        border-radius: 1rem;
        padding: 2rem;
        margin-top: 2rem;
    }
    
    .result-item {
        display: flex;
        justify-content: space-between;
        padding: 0.75rem 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    }
    
    .result-item:last-child {
        border-bottom: none;
        font-weight: 700;
        font-size: 1.25rem;
        margin-top: 0.5rem;
        padding-top: 1rem;
        border-top: 2px solid rgba(255, 255, 255, 0.3);
    }
    
    .info-box {
        background: #eff6ff;
        border-left: 4px solid #3b82f6;
        padding: 1rem;
        border-radius: 0.5rem;
        margin-top: 1rem;
    }
    
    .tab-button {
        padding: 0.75rem 1.5rem;
        border: none;
        background: #e5e7eb;
        color: #64748b;
        border-radius: 0.5rem 0.5rem 0 0;
        cursor: pointer;
        transition: all 0.2s;
        font-weight: 600;
    }
    
    .tab-button.active {
        background: white;
        color: #3b82f6;
        border-bottom: 3px solid #3b82f6;
    }
</style>

<!-- Hero Section -->
<div class="calculator-hero text-white text-center relative z-10">
    <div class="container mx-auto">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">Fee Calculator</h1>
        <p class="text-xl md:text-2xl text-blue-100 max-w-2xl mx-auto">
            Calculate fees for buyers and sellers on CayMark
        </p>
    </div>
</div>

<!-- Calculator Section -->
<div class="container mx-auto px-4 py-12">
    <div class="max-w-4xl mx-auto">
        <!-- Tabs and Calculators -->
        <div x-data="{ 
            activeTab: 'buyer',
            bidAmount: 0,
            hasMembership: true,
            sellerType: 'casual',
            listingCount: 1,
            calculateBuyerFees() {
                let deposit = 0;
                let membership = 0;
                let total = 0;
                
                if (this.bidAmount > 2000) {
                    deposit = this.bidAmount * 0.10;
                }
                
                if (!this.hasMembership) {
                    membership = 64.99;
                }
                
                total = deposit + membership;
                
                return {
                    deposit: deposit.toFixed(2),
                    membership: membership.toFixed(2),
                    total: total.toFixed(2)
                };
            },
            calculateSellerFees() {
                let fee = 0;
                
                if (this.sellerType === 'casual') {
                    fee = this.listingCount * 25;
                } else if (this.sellerType === 'business') {
                    fee = 599.99; // Annual fee
                }
                
                return {
                    fee: fee.toFixed(2),
                    type: this.sellerType === 'casual' ? 'per listing' : 'per year'
                };
            }
        }">
            <!-- Tabs -->
            <div class="flex gap-2 mb-6">
                <button @click="activeTab = 'buyer'" 
                        :class="activeTab === 'buyer' ? 'tab-button active' : 'tab-button'"
                        class="tab-button">
                    Buyer Fees
                </button>
                <button @click="activeTab = 'seller'" 
                        :class="activeTab === 'seller' ? 'tab-button active' : 'tab-button'"
                        class="tab-button">
                    Seller Fees
                </button>
            </div>

            <!-- Buyer Calculator -->
            <div x-show="activeTab === 'buyer'">
            <div class="calculator-card">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Buyer Fee Calculator</h2>
                
                <div class="input-group">
                    <label for="bidAmount">Bid Amount ($)</label>
                    <input type="number" 
                           id="bidAmount" 
                           x-model="bidAmount" 
                           min="0" 
                           step="0.01"
                           placeholder="Enter your bid amount"
                           class="input-group input">
                </div>
                
                <div class="input-group">
                    <label class="flex items-center">
                        <input type="checkbox" 
                               x-model="hasMembership" 
                               class="mr-2 w-5 h-5">
                        <span>I have an active Standard Buyer membership ($64.99/year)</span>
                    </label>
                </div>
                
                <div class="info-box">
                    <p class="text-sm text-gray-700">
                        <strong>Note:</strong> Buyers must place a deposit equal to 10% of the bid amount when bidding above $2,000. 
                        Deposits are automatically applied toward the final payment if you win the auction.
                    </p>
                </div>
                
                <div class="result-card" x-show="bidAmount > 0">
                    <h3 class="text-xl font-bold mb-4">Fee Breakdown</h3>
                    <template x-if="bidAmount > 2000">
                        <div class="result-item">
                            <span>Deposit (10% of bid)</span>
                            <span>$<span x-text="calculateBuyerFees().deposit"></span></span>
                        </div>
                    </template>
                    <template x-if="!hasMembership">
                        <div class="result-item">
                            <span>Standard Buyer Membership</span>
                            <span>$<span x-text="calculateBuyerFees().membership"></span></span>
                        </div>
                    </template>
                    <div class="result-item">
                        <span>Total Fees</span>
                        <span>$<span x-text="calculateBuyerFees().total"></span></span>
                    </div>
                </div>
            </div>
        </div>

            <!-- Seller Calculator -->
            <div x-show="activeTab === 'seller'">
            <div class="calculator-card">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Seller Fee Calculator</h2>
                
                <div class="input-group">
                    <label for="sellerType">Seller Type</label>
                    <select id="sellerType" x-model="sellerType" class="input-group select">
                        <option value="casual">Casual Seller ($25 per listing)</option>
                        <option value="business">Business Seller ($599.99 per year)</option>
                    </select>
                </div>
                
                <div class="input-group" x-show="sellerType === 'casual'">
                    <label for="listingCount">Number of Listings</label>
                    <input type="number" 
                           id="listingCount" 
                           x-model="listingCount" 
                           min="1" 
                           step="1"
                           placeholder="Enter number of listings"
                           class="input-group input">
                </div>
                
                <div class="info-box">
                    <p class="text-sm text-gray-700 mb-2">
                        <strong>Casual Seller:</strong> Pay $25 per listing. Best for occasional sellers.
                    </p>
                    <p class="text-sm text-gray-700">
                        <strong>Business Seller:</strong> Pay $599.99 per year for unlimited listings, advanced tools, 
                        inventory management, priority support, and a dedicated account manager.
                    </p>
                </div>
                
                <div class="result-card">
                    <h3 class="text-xl font-bold mb-4">Fee Breakdown</h3>
                    <div class="result-item">
                        <span x-text="sellerType === 'casual' ? 'Listing Fee' : 'Annual Membership Fee'"></span>
                        <span>$<span x-text="calculateSellerFees().fee"></span> <span x-text="calculateSellerFees().type"></span></span>
                    </div>
                    <div class="result-item">
                        <span>Total Cost</span>
                        <span>$<span x-text="calculateSellerFees().fee"></span></span>
                    </div>
                </div>
            </div>
            </div>
        </div>

        <!-- Additional Information -->
        <div class="calculator-card mt-8">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Important Information</h3>
            <div class="space-y-4 text-gray-700">
                <div>
                    <h4 class="font-semibold mb-2">Deposit Refunds</h4>
                    <p class="text-sm">Deposits are refundable unless your account violates CayMark policies. Refunds typically take 3-5 business days after your request or after account closure.</p>
                </div>
                <div>
                    <h4 class="font-semibold mb-2">Membership Duration</h4>
                    <p class="text-sm">All memberships are annual. When your membership expires, your account will return to Guest mode until you renew.</p>
                </div>
                <div>
                    <h4 class="font-semibold mb-2">Payment Methods</h4>
                    <p class="text-sm">Credit and debit cards are accepted for deposits, membership payments, and auction payments.</p>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
