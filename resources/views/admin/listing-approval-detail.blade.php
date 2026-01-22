@extends('layouts.admin')

@section('title', 'Listing Approval Detail - Admin')

@section('content')
<div class="bg-gray-50 min-h-screen py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Listing Approval Detail</h1>
                    <p class="text-gray-600 mt-2">Review listing details before approval</p>
                </div>
                <a href="{{ route('admin.listing-review') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Review
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6 rounded">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6 rounded">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Vehicle Information -->
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Vehicle Information</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Year</label>
                            <p class="text-gray-900 font-semibold">{{ $listing->year ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Make</label>
                            <p class="text-gray-900 font-semibold">{{ $listing->make ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Model</label>
                            <p class="text-gray-900 font-semibold">{{ $listing->model ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Trim</label>
                            <p class="text-gray-900 font-semibold">{{ $listing->trim ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">VIN/HIN</label>
                            <p class="text-gray-900 font-semibold font-mono">{{ $listing->vin ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Color</label>
                            <p class="text-gray-900 font-semibold">{{ $listing->color ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Interior Color</label>
                            <p class="text-gray-900 font-semibold">{{ $listing->interior_color ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Island</label>
                            <p class="text-gray-900 font-semibold">{{ $listing->island ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Title Status</label>
                            <p class="text-gray-900 font-semibold">{{ $listing->title_status ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Primary Damage</label>
                            <p class="text-gray-900 font-semibold">{{ $listing->primary_damage ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Secondary Damage</label>
                            <p class="text-gray-900 font-semibold">{{ $listing->secondary_damage ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Keys Available</label>
                            <p class="text-gray-900 font-semibold">{{ $listing->keys_available ? 'Yes' : 'No' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Fuel Type</label>
                            <p class="text-gray-900 font-semibold">{{ $listing->fuel_type ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Transmission</label>
                            <p class="text-gray-900 font-semibold">{{ $listing->transmission ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Engine Type</label>
                            <p class="text-gray-900 font-semibold">{{ $listing->engine_type ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Auction Settings -->
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Auction Settings</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Auction Duration</label>
                            <p class="text-gray-900 font-semibold">{{ $listing->auction_duration ?? 'N/A' }} days</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Starting Price</label>
                            <p class="text-gray-900 font-semibold">${{ number_format($listing->starting_price ?? 0, 2) }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Reserve Price</label>
                            <p class="text-gray-900 font-semibold">${{ number_format($listing->reserve_price ?? 0, 2) }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Buy Now Price</label>
                            <p class="text-gray-900 font-semibold">${{ number_format($listing->buy_now_price ?? 0, 2) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Photos -->
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Photos</h2>
                    @if($listing->images && $listing->images->count() > 0)
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            @foreach($listing->images as $image)
                                <div class="relative">
                                    <img src="{{ asset('uploads/listings/' . $image->image_path) }}" 
                                         alt="Listing Image" 
                                         class="w-full h-48 object-cover rounded-lg border border-gray-200 cursor-pointer hover:opacity-90 transition"
                                         onclick="openImageModal('{{ asset('uploads/listings/' . $image->image_path) }}')">
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-8">No photos uploaded</p>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Seller Information -->
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Seller Information</h2>
                    <div class="space-y-3">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Name</label>
                            <p class="text-gray-900 font-semibold">{{ $listing->seller->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Email</label>
                            <p class="text-gray-900 font-semibold">{{ $listing->seller->email ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Phone</label>
                            <p class="text-gray-900 font-semibold">{{ $listing->seller->phone ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Listing Details -->
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Listing Details</h2>
                    <div class="space-y-3">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Listing ID</label>
                            <p class="text-gray-900 font-semibold">#{{ $listing->id }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Status</label>
                            <p>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                    PENDING REVIEW
                                </span>
                            </p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Submitted</label>
                            <p class="text-gray-900 font-semibold">{{ $listing->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                        @if($listing->duplicate_vin_flag)
                        <div>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                <i class="fas fa-exclamation-triangle mr-1"></i>Duplicate VIN Detected
                            </span>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Actions -->
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Actions</h2>
                    
                    <!-- Approve Button -->
                    <form method="POST" action="{{ route('admin.listings.approve', $listing) }}" class="mb-4">
                        @csrf
                        <button type="submit" 
                                class="w-full px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-semibold"
                                onclick="return confirm('Are you sure you want to approve this listing?')">
                            <i class="fas fa-check-circle mr-2"></i>Approve Listing
                        </button>
                    </form>

                    <!-- Reject Form -->
                    <div class="border-t pt-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Reject Listing</h3>
                        <form method="POST" action="{{ route('admin.listings.reject', $listing) }}" id="rejectForm">
                            @csrf
                            <div class="mb-3">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Rejection Reason *</label>
                                <select name="rejection_reason" required 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                                    <option value="">Select reason...</option>
                                    <option value="Poor quality photos">Poor quality photos</option>
                                    <option value="Missing required information">Missing required information</option>
                                    <option value="Invalid VIN/HIN">Invalid VIN/HIN</option>
                                    <option value="Duplicate listing">Duplicate listing</option>
                                    <option value="Prohibited item">Prohibited item</option>
                                    <option value="Incorrect category">Incorrect category</option>
                                    <option value="Other (specify in notes)">Other (specify in notes)</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Rejection Notes</label>
                                <textarea name="rejection_notes" rows="3" 
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                          placeholder="Optional additional notes..."></textarea>
                            </div>
                            <button type="submit" 
                                    class="w-full px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-semibold"
                                    onclick="return confirm('Are you sure you want to reject this listing?')">
                                <i class="fas fa-times-circle mr-2"></i>Reject Listing
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div id="imageModal" class="hidden fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4" onclick="closeImageModal()">
    <div class="max-w-4xl w-full">
        <img id="modalImage" src="" alt="Full size image" class="max-w-full max-h-[90vh] mx-auto rounded-lg">
        <button onclick="closeImageModal()" class="absolute top-4 right-4 text-white hover:text-gray-300 text-3xl">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>

<script>
    function openImageModal(imageSrc) {
        document.getElementById('modalImage').src = imageSrc;
        document.getElementById('imageModal').classList.remove('hidden');
    }

    function closeImageModal() {
        document.getElementById('imageModal').classList.add('hidden');
    }

    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeImageModal();
        }
    });
</script>
@endsection
