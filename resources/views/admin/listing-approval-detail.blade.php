@extends('layouts.admin')

@section('title', 'Listing Approval Detail - Admin')

@section('content')
<div class="bg-gray-50 min-h-screen py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
            <div class="flex items-center justify-between flex-wrap gap-3">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $listing->status === 'pending' ? 'Listing Approval Detail' : 'Listing Detail' }}</h1>
                    <p class="text-gray-600 mt-2">{{ $listing->status === 'pending' ? 'Review listing details before approval' : 'View listing details' }}</p>
                </div>
                <div class="flex items-center gap-2">
                    @if($listing->status !== 'pending')
                    <a href="{{ route('admin.active-listings') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Active Listings
                    </a>
                    @endif
                    <a href="{{ route('admin.listing-review') }}" class="px-4 py-2 {{ $listing->status === 'pending' ? 'bg-gray-600 text-white' : 'bg-gray-200 text-gray-700' }} rounded-lg hover:opacity-90 transition">
                        <i class="fas fa-list mr-2"></i>{{ $listing->status === 'pending' ? 'Back to Review' : 'Listing Review' }}
                    </a>
                </div>
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
                            <label class="text-sm font-medium text-gray-500">Odometer</label>
                            <p class="text-gray-900 font-semibold">
                                @if($listing->odometer)
                                    {{ number_format($listing->odometer) }} mi
                                    @if($listing->odometer_estimated ?? false)
                                        <span class="text-amber-600 font-medium">(Estimated)</span>
                                        <span class="material-icons text-gray-400 cursor-help align-middle ml-0.5" title="This is an estimated odometer reading and may be subject to change." style="font-size: 16px;">info</span>
                                    @endif
                                @else
                                    N/A
                                @endif
                            </p>
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
                                @if($listing->status === 'pending')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">PENDING REVIEW</span>
                                @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                    {{ $listing->status === 'approved' ? 'bg-blue-100 text-blue-800' : ($listing->status === 'active' ? 'bg-green-100 text-green-800' : ($listing->status === 'sold' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-800')) }}">
                                    {{ strtoupper(str_replace('_', ' ', $listing->status ?? 'N/A')) }}
                                </span>
                                @endif
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

                @if($listing->status === 'pending')
                <!-- Actions: Approve / Reject (only for pending) -->
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Actions</h2>
                    
                    <form method="POST" action="{{ route('admin.listings.approve', $listing) }}" id="approveForm" class="mb-4">
                        @csrf
                        <button type="button" 
                                class="w-full px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-semibold"
                                onclick="openApproveModal()">
                            <i class="fas fa-check-circle mr-2"></i>Approve Listing
                        </button>
                    </form>

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
                            <button type="button" 
                                    class="w-full px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-semibold"
                                    onclick="openRejectModal()">
                                <i class="fas fa-times-circle mr-2"></i>Reject Listing
                            </button>
                        </form>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Approve confirmation modal -->
<div id="approveModal" class="admin-action-modal hidden" aria-hidden="true">
    <div class="admin-action-modal-backdrop" data-dismiss="approveModal"></div>
    <div class="admin-action-modal-card admin-action-modal-success">
        <div class="admin-action-modal-icon-wrap bg-green-100">
            <i class="fas fa-check-circle text-green-600 text-2xl"></i>
        </div>
        <h3 class="admin-action-modal-title">Approve listing</h3>
        <p class="admin-action-modal-message">Are you sure you want to approve this listing? It will go live for buyers.</p>
        <div class="admin-action-modal-actions">
            <button type="button" class="admin-action-btn admin-action-btn-secondary" onclick="closeApproveModal()">Cancel</button>
            <button type="button" class="admin-action-btn admin-action-btn-success" onclick="document.getElementById('approveForm').submit();">
                <i class="fas fa-check mr-2"></i>Approve
            </button>
        </div>
    </div>
</div>

<!-- Reject confirmation modal -->
<div id="rejectModal" class="admin-action-modal hidden" aria-hidden="true">
    <div class="admin-action-modal-backdrop" data-dismiss="rejectModal"></div>
    <div class="admin-action-modal-card admin-action-modal-danger">
        <div class="admin-action-modal-icon-wrap bg-red-100">
            <i class="fas fa-times-circle text-red-600 text-2xl"></i>
        </div>
        <h3 class="admin-action-modal-title">Reject listing</h3>
        <p class="admin-action-modal-message">Are you sure you want to reject this listing? The seller will be notified with your reason.</p>
        <div class="admin-action-modal-actions">
            <button type="button" class="admin-action-btn admin-action-btn-secondary" onclick="closeRejectModal()">Cancel</button>
            <button type="button" class="admin-action-btn admin-action-btn-danger" onclick="document.getElementById('rejectForm').submit();">
                <i class="fas fa-times mr-2"></i>Reject
            </button>
        </div>
    </div>
</div>

<!-- Image preview modal (theme-matched) -->
<div id="imageModal" class="admin-action-modal hidden" aria-hidden="true">
    <div class="admin-action-modal-backdrop" data-dismiss="imageModal"></div>
    <div class="admin-action-modal-image-card" onclick="event.stopPropagation()">
        <button type="button" class="admin-action-modal-close" onclick="closeImageModal()" aria-label="Close">
            <i class="fas fa-times"></i>
        </button>
        <img id="modalImage" src="" alt="Full size" class="admin-action-modal-img">
    </div>
</div>

<style>
    .admin-action-modal {
        position: fixed;
        inset: 0;
        z-index: 50;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1.5rem;
    }
    .admin-action-modal.hidden { display: none !important; }
    .admin-action-modal-backdrop {
        position: absolute;
        inset: 0;
        background: rgba(15, 23, 42, 0.5);
        backdrop-filter: blur(4px);
    }
    .admin-action-modal-card {
        position: relative;
        width: 100%;
        max-width: 420px;
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        padding: 1.75rem;
    }
    .admin-action-modal-icon-wrap {
        width: 56px;
        height: 56px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
    }
    .admin-action-modal-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #0f172a;
        margin: 0 0 0.5rem 0;
    }
    .admin-action-modal-message {
        font-size: 0.9375rem;
        color: #64748b;
        line-height: 1.5;
        margin: 0 0 1.5rem 0;
    }
    .admin-action-modal-actions {
        display: flex;
        gap: 0.75rem;
        justify-content: flex-end;
    }
    .admin-action-btn {
        padding: 0.625rem 1.25rem;
        font-size: 0.9375rem;
        font-weight: 600;
        border-radius: 10px;
        border: none;
        cursor: pointer;
        transition: background 0.2s, color 0.2s;
    }
    .admin-action-btn-secondary {
        background: #f1f5f9;
        color: #475569;
    }
    .admin-action-btn-secondary:hover { background: #e2e8f0; }
    .admin-action-btn-success { background: #16a34a; color: #fff; }
    .admin-action-btn-success:hover { background: #15803d; }
    .admin-action-btn-danger { background: #dc2626; color: #fff; }
    .admin-action-btn-danger:hover { background: #b91c1c; }
    .admin-action-modal-image-card {
        position: relative;
        max-width: min(900px, 95vw);
        background: #fff;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }
    .admin-action-modal-close {
        position: absolute;
        top: 1rem;
        right: 1rem;
        width: 40px;
        height: 40px;
        border: none;
        border-radius: 10px;
        background: rgba(15, 23, 42, 0.7);
        color: #fff;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 2;
        transition: background 0.2s;
    }
    .admin-action-modal-close:hover { background: rgba(15, 23, 42, 0.9); }
    .admin-action-modal-img {
        display: block;
        max-width: 100%;
        max-height: 85vh;
        object-fit: contain;
    }
</style>

<script>
    function openApproveModal() {
        document.getElementById('approveModal').classList.remove('hidden');
        document.getElementById('approveModal').setAttribute('aria-hidden', 'false');
    }
    function closeApproveModal() {
        document.getElementById('approveModal').classList.add('hidden');
        document.getElementById('approveModal').setAttribute('aria-hidden', 'true');
    }
    function openRejectModal() {
        document.getElementById('rejectModal').classList.remove('hidden');
        document.getElementById('rejectModal').setAttribute('aria-hidden', 'false');
    }
    function closeRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
        document.getElementById('rejectModal').setAttribute('aria-hidden', 'true');
    }
    function openImageModal(imageSrc) {
        document.getElementById('modalImage').src = imageSrc;
        document.getElementById('imageModal').classList.remove('hidden');
        document.getElementById('imageModal').setAttribute('aria-hidden', 'false');
    }
    function closeImageModal() {
        document.getElementById('imageModal').classList.add('hidden');
        document.getElementById('imageModal').setAttribute('aria-hidden', 'true');
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('[data-dismiss]').forEach(function(el) {
            el.addEventListener('click', function() {
                var id = this.getAttribute('data-dismiss');
                var modal = document.getElementById(id);
                if (modal) {
                    modal.classList.add('hidden');
                    modal.setAttribute('aria-hidden', 'true');
                    if (id === 'imageModal') document.getElementById('modalImage').src = '';
                }
            });
        });
        document.addEventListener('keydown', function(e) {
            if (e.key !== 'Escape') return;
            closeApproveModal();
            closeRejectModal();
            closeImageModal();
        });
    });
</script>
@endsection
