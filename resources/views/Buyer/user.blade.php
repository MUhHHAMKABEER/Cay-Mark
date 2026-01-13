@extends('layouts.dashboard')

@section('title', 'User - Buyer Dashboard')

@section('content')
<div class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <div class="bg-white shadow-sm mb-6 rounded-lg p-6">
        <h1 class="text-3xl font-bold text-gray-900">Account Information</h1>
        <p class="text-gray-600 mt-2">Manage your account details and password</p>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
        {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Account Information Card -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Account Information</h2>

        <!-- Full Name -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
            <div class="bg-gray-50 border border-gray-300 rounded-lg px-4 py-3">
                <span class="text-gray-900">{{ $user->name }}</span>
            </div>
        </div>

        <!-- Email Address -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Registered Email Address</label>
            <form method="POST" action="{{ route('buyer.user.update-email') }}" class="flex items-center space-x-3">
                @csrf
                <div class="flex-1">
                    <input type="email" 
                           name="email" 
                           value="{{ $user->email }}" 
                           class="w-full bg-gray-50 border border-gray-300 rounded-lg px-4 py-3 text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           required>
                </div>
                <button type="submit" 
                        class="bg-blue-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-blue-700 transition duration-200">
                    Update Email
                </button>
            </form>
            <p class="text-sm text-gray-500 mt-2">Email is visible only and can be edited.</p>
        </div>

        <!-- Account Type -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Account Type</label>
            <div class="bg-gray-50 border border-gray-300 rounded-lg px-4 py-3">
                <span class="text-gray-900 font-semibold">Buyer</span>
            </div>
        </div>

        <!-- ID -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">ID</label>
            <div class="bg-gray-50 border border-gray-300 rounded-lg px-4 py-3">
                <span class="text-gray-900">{{ $user->id }}</span>
            </div>
        </div>

        <!-- Password Management -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Password Management</label>
            <button onclick="showPasswordModal()" 
                    class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-medium transition duration-200">
                Change Password
            </button>
            <p class="text-sm text-gray-500 mt-2">Password is not displayed.</p>
        </div>
    </div>
</div>

<!-- Password Change Modal -->
<div id="passwordModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl p-6 max-w-md w-full mx-4">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-900">Change Password</h3>
            <button onclick="hidePasswordModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('buyer.user.change-password') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                <input type="password" 
                       name="current_password" 
                       required
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                <input type="password" 
                       name="password" 
                       required
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                <input type="password" 
                       name="password_confirmation" 
                       required
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div class="flex items-center justify-end space-x-3">
                <button type="button" 
                        onclick="hidePasswordModal()" 
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition duration-200">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                    Change Password
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function showPasswordModal() {
    document.getElementById('passwordModal').classList.remove('hidden');
}

function hidePasswordModal() {
    document.getElementById('passwordModal').classList.add('hidden');
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('passwordModal');
    if (event.target == modal) {
        hidePasswordModal();
    }
}
</script>
@endsection
