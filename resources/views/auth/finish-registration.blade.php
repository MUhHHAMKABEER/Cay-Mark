@extends('layouts.welcome')
@section('title', 'Select Your Membership - CayMark')
@section('content')

<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-12 px-4">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Select Your Membership</h1>
            <p class="text-gray-600 text-lg">Choose how you want to use CayMark</p>
        </div>

        <!-- Flash Messages -->
        @if (session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded">
                <p class="text-red-800">{{ session('error') }}</p>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded">
                <ul class="list-disc list-inside text-red-800">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('finish.registration.membership') }}" id="membership-form">
            @csrf

            <!-- Role Selection -->
            <div class="bg-white rounded-2xl shadow-xl p-8 mb-8 border border-gray-100">
                <label class="block text-lg font-semibold text-gray-700 mb-6">I want to register as a:</label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <label class="relative cursor-pointer group">
                        <input type="radio" name="role" value="buyer" class="sr-only peer" required
                            onchange="loadPackages('buyer')">
                        <div class="p-8 border-2 border-gray-300 rounded-2xl transition-all duration-300
                            group-hover:border-blue-400 peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:shadow-lg">
                            <div class="flex items-center mb-4">
                                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mr-4">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                    </svg>
                                </div>
                                <div class="font-bold text-xl text-gray-900">Buyer</div>
                            </div>
                            <p class="text-gray-600">Browse marketplace and participate in auctions</p>
                        </div>
                    </label>
                    <label class="relative cursor-pointer group">
                        <input type="radio" name="role" value="seller" class="sr-only peer"
                            onchange="loadPackages('seller')">
                        <div class="p-8 border-2 border-gray-300 rounded-2xl transition-all duration-300
                            group-hover:border-blue-400 peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:shadow-lg">
                            <div class="flex items-center mb-4">
                                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mr-4">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                </div>
                                <div class="font-bold text-xl text-gray-900">Seller</div>
                            </div>
                            <p class="text-gray-600">List vehicles for sale or auction</p>
                        </div>
                    </label>
                </div>
                @error('role')
                    <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Package Selection -->
            <div class="bg-white rounded-2xl shadow-xl p-8 mb-8 border border-gray-100">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Choose Your Membership Package</h2>
                <div id="package-selection" class="space-y-4">
                    <p class="text-gray-500 text-center py-8">Please select a role above to see available packages</p>
                </div>
                @error('package_id')
                    <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="text-center">
                <a href="{{ route('dashboard.default') }}" 
                   class="inline-block px-8 py-3 border-2 border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition duration-200 mr-4">
                    Cancel
                </a>
                <button type="submit"
                    class="inline-block bg-gradient-to-r from-blue-500 to-blue-600 text-white px-8 py-4 rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition duration-200">
                    Continue to Verification & Payment
                    <svg class="w-5 h-5 inline ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    async function loadPackages(role) {
        const container = document.getElementById('package-selection');
        container.innerHTML = '<div class="text-center py-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mx-auto"></div><p class="text-gray-500 mt-2">Loading packages...</p></div>';

        try {
            const response = await fetch(`/api/packages/${role}`);
            if (!response.ok) throw new Error('Failed to load packages');
            const packages = await response.json();

            if (!Array.isArray(packages) || packages.length === 0) {
                container.innerHTML = '<p class="text-gray-500 text-center py-8">No packages available for this role.</p>';
                return;
            }

            container.innerHTML = '';
            packages.forEach(pkg => {
                const label = document.createElement('label');
                label.className = 'block border-2 border-gray-300 rounded-2xl p-6 cursor-pointer transition-all duration-300 hover:border-blue-400';

                const input = document.createElement('input');
                input.type = 'radio';
                input.name = 'package_id';
                input.value = pkg.id;
                input.className = 'sr-only';
                input.required = true;

                input.addEventListener('change', () => {
                    container.querySelectorAll('label').forEach(l => {
                        l.classList.remove('border-blue-500', 'bg-blue-50', 'shadow-md');
                    });
                    if (input.checked) {
                        label.classList.add('border-blue-500', 'bg-blue-50', 'shadow-md');
                    }
                });

                const price = typeof pkg.price !== 'undefined' ? '$' + Number(pkg.price).toFixed(2) : 'Free';
                const isIndividualSeller = role === 'seller' && pkg.price == 0;

                label.innerHTML = `
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <div class="font-bold text-xl text-gray-900">${escapeHtml(pkg.title || 'Package')}</div>
                            <div class="text-gray-600 mt-2">${escapeHtml(pkg.description || '')}</div>
                        </div>
                        <div class="text-2xl font-bold text-blue-600">${price}</div>
                    </div>
                    ${isIndividualSeller ? '<div class="bg-green-50 border border-green-200 rounded-lg p-3 mt-4"><p class="text-green-800 text-sm"><strong>No payment required at this time.</strong> You will pay $25 per listing when you submit items.</p></div>' : ''}
                `;

                label.insertBefore(input, label.firstChild);
                container.appendChild(label);
            });
        } catch (error) {
            console.error('Failed to load packages:', error);
            container.innerHTML = '<div class="text-center py-8 text-red-600 bg-red-50 rounded-2xl border border-red-200"><p>Failed to load packages. Please try again.</p></div>';
        }
    }

    function escapeHtml(str) {
        if (str === null || typeof str === 'undefined') return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }
</script>
@endpush

@endsection

