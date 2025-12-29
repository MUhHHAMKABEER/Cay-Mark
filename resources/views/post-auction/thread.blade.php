@extends('layouts.app')
@section('title', 'Pickup Coordination - CayMark')
@section('content')

<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Thread Header -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-4">{{ $listing->year }} {{ $listing->make }} {{ $listing->model }}</h1>
            
            <!-- Thread Intro Panel -->
            <div class="flex items-start space-x-4 p-4 bg-gray-50 rounded-lg">
                @if($listing->images->first())
                    <img src="{{ asset('uploads/listings/' . $listing->images->first()->image_path) }}" 
                         alt="{{ $listing->year }} {{ $listing->make }} {{ $listing->model }}"
                         class="w-24 h-24 object-cover rounded-lg">
                @endif
                <div class="flex-1">
                    <p class="text-sm text-gray-600 mb-1"><strong>SOLD BY:</strong> {{ $seller->name }}</p>
                    <p class="text-sm text-gray-600"><strong>SOLD ON:</strong> {{ $invoice->sale_date->format('F d, Y') }}</p>
                    <p class="text-sm text-gray-600"><strong>ITEM ID:</strong> {{ $invoice->item_id }}</p>
                </div>
            </div>
        </div>

        <!-- Security Notice -->
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded">
            <p class="text-sm text-red-800">
                <strong>Important:</strong> Exchanging phone numbers, meeting outside of CayMark, or attempting any transaction outside the platform is prohibited.
            </p>
        </div>

        <!-- Buyer: Pickup PIN Display -->
        @if($isBuyer && $listing->pickup_pin)
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold text-blue-900 mb-2">Your Pickup PIN</h3>
                <p class="text-3xl font-bold text-blue-600 font-mono mb-2">{{ $listing->pickup_pin }}</p>
                <p class="text-sm text-blue-700">Share this PIN with the seller (or authorized pickup agent) at the time of pickup. The seller will enter this PIN to confirm pickup.</p>
            </div>
        @endif

        <!-- Pickup Details Section -->
        @if($thread->latestPickupDetail)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Pickup Details</h2>
                
                <div class="space-y-3 mb-6">
                    <div>
                        <span class="text-sm font-semibold text-gray-700">DATE:</span>
                        <span class="ml-2 text-gray-900">{{ $thread->latestPickupDetail->pickup_date->format('l, F d, Y') }}</span>
                    </div>
                    <div>
                        <span class="text-sm font-semibold text-gray-700">TIME:</span>
                        <span class="ml-2 text-gray-900">{{ \Carbon\Carbon::parse($thread->latestPickupDetail->pickup_time)->format('g:i A') }}</span>
                    </div>
                    <div>
                        <span class="text-sm font-semibold text-gray-700">ADDRESS:</span>
                        <span class="ml-2 text-gray-900">{{ $thread->latestPickupDetail->street_address }}</span>
                    </div>
                    @if($thread->latestPickupDetail->directions_notes)
                        <div>
                            <span class="text-sm font-semibold text-gray-700">NOTES:</span>
                            <p class="mt-1 text-gray-900">{{ $thread->latestPickupDetail->directions_notes }}</p>
                        </div>
                    @endif
                </div>

                <!-- Buyer Actions -->
                @if($isBuyer && $thread->latestPickupDetail->status === 'pending')
                    <div class="flex items-center space-x-4">
                        <form action="{{ route('post-auction.accept-pickup', $thread->id) }}" method="POST">
                            @csrf
                            <button type="submit" 
                                    class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition duration-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Accept Pickup Details
                            </button>
                        </form>
                        
                        <button onclick="document.getElementById('changeRequestModal').classList.remove('hidden')" 
                                class="inline-flex items-center px-6 py-3 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Request Date / Time Change
                        </button>
                    </div>
                @elseif($isBuyer && $thread->latestPickupDetail->status === 'confirmed')
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <p class="text-green-800 font-semibold">✓ Pickup appointment confirmed</p>
                    </div>
                @endif
            </div>
        @endif

        <!-- Seller: Send Pickup Details Form -->
        @if($isSeller && (!$thread->latestPickupDetail || $thread->latestPickupDetail->status === 'change_requested'))
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Send Pickup Details</h2>
                
                <form action="{{ route('post-auction.send-pickup-details', $thread->id) }}" method="POST">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pickup Date *</label>
                            <input type="date" name="pickup_date" required 
                                   min="{{ date('Y-m-d') }}"
                                   class="w-full rounded-lg border-gray-300">
                            @error('pickup_date')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pickup Time *</label>
                            <input type="time" name="pickup_time" required 
                                   class="w-full rounded-lg border-gray-300">
                            @error('pickup_time')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Street Address *</label>
                        <input type="text" name="street_address" required 
                               placeholder="122 Prince Charles Drive"
                               class="w-full rounded-lg border-gray-300">
                        <p class="text-xs text-gray-500 mt-1">Must start with a number. No phone numbers or emails allowed.</p>
                        @error('street_address')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Directions or Notes (Optional)</label>
                        <textarea name="directions_notes" rows="3" 
                                  placeholder="Enter through blue gate behind Shell station."
                                  class="w-full rounded-lg border-gray-300"></textarea>
                        <p class="text-xs text-gray-500 mt-1">No phone numbers, emails, or external links allowed.</p>
                        @error('directions_notes')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <button type="submit" 
                            class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                        Send Pickup Details
                    </button>
                </form>
            </div>
        @endif

        <!-- Seller: Confirm Pickup with PIN -->
        @if($isSeller && $thread->latestPickupDetail && $thread->latestPickupDetail->status === 'confirmed' && !$thread->pickup_confirmed)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Confirm Pickup</h2>
                <p class="text-gray-600 mb-4">Enter the 4-digit PIN provided by the buyer to confirm pickup and initiate payout.</p>
                
                <form action="{{ route('post-auction.confirm-pickup', $thread->id) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pickup PIN *</label>
                        <input type="text" name="pickup_pin" required maxlength="4" pattern="[0-9]{4}" 
                               placeholder="1234"
                               class="w-full rounded-lg border-gray-300 text-center text-2xl font-mono tracking-widest">
                        @error('pickup_pin')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <button type="submit" 
                            class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Confirm Pickup
                    </button>
                </form>
            </div>
        @endif

        <!-- Third-Party Pickup Authorization (Buyer) -->
        @if($isBuyer)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Authorize Third-Party Pickup</h2>
                <p class="text-gray-600 mb-4">If someone else will pick up the item on your behalf, authorize them here.</p>
                
                @if($thread->activeThirdPartyPickup)
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                        <p class="text-sm font-semibold text-blue-900">Authorized Pickup:</p>
                        <p class="text-blue-800">{{ $thread->activeThirdPartyPickup->authorized_name }}</p>
                        <p class="text-xs text-blue-700 mt-1">Type: {{ ucfirst(str_replace('_', ' ', $thread->activeThirdPartyPickup->pickup_type)) }}</p>
                    </div>
                @endif
                
                <button onclick="document.getElementById('thirdPartyModal').classList.remove('hidden')" 
                        class="inline-flex items-center px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Authorize Third-Party Pickup
                </button>
            </div>
        @endif

        <!-- Support Button -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <a href="{{ $isBuyer ? route('buyer.support') : '#' }}" 
               class="inline-flex items-center px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                Contact CayMark Support
            </a>
        </div>
    </div>
</div>

<!-- Change Request Modal -->
<div id="changeRequestModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl p-6 max-w-md w-full mx-4">
        <h3 class="text-xl font-bold text-gray-900 mb-4">Request Date / Time Change</h3>
        <form action="{{ route('post-auction.request-change', $thread->id) }}" method="POST">
            @csrf
            <input type="hidden" name="pickup_detail_id" value="{{ $thread->latestPickupDetail->id }}">
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">New Pickup Date (Optional)</label>
                <input type="date" name="requested_pickup_date" 
                       min="{{ date('Y-m-d') }}"
                       class="w-full rounded-lg border-gray-300">
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">New Pickup Time (Optional)</label>
                <input type="time" name="requested_pickup_time" 
                       class="w-full rounded-lg border-gray-300">
            </div>
            
            <p class="text-xs text-gray-500 mb-4">Note: Address and notes cannot be changed. Provide at least one new date or time.</p>
            
            <div class="flex items-center space-x-4">
                <button type="submit" 
                        class="flex-1 px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700">
                    Send Request
                </button>
                <button type="button" onclick="document.getElementById('changeRequestModal').classList.add('hidden')" 
                        class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Third-Party Pickup Modal -->
<div id="thirdPartyModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl p-6 max-w-md w-full mx-4">
        <h3 class="text-xl font-bold text-gray-900 mb-4">Authorize Third-Party Pickup</h3>
        <form action="{{ route('post-auction.authorize-third-party', $thread->id) }}" method="POST">
            @csrf
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Authorized Person or Company Name *</label>
                <input type="text" name="authorized_name" required 
                       placeholder="John Doe or ABC Towing Company"
                       class="w-full rounded-lg border-gray-300">
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Pickup Type *</label>
                <select name="pickup_type" required class="w-full rounded-lg border-gray-300">
                    <option value="">Select type...</option>
                    <option value="tow_company">Tow Company</option>
                    <option value="individual">Individual / Authorized Representative</option>
                </select>
            </div>
            
            <p class="text-xs text-gray-500 mb-4">The authorized person must present valid government photo ID matching the registered name at pickup.</p>
            
            <div class="flex items-center space-x-4">
                <button type="submit" 
                        class="flex-1 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                    Authorize
                </button>
                <button type="button" onclick="document.getElementById('thirdPartyModal').classList.add('hidden')" 
                        class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

@if(session('success'))
    <script>
        alert('{{ session('success') }}');
    </script>
@endif

@endsection
