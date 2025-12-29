@extends('layouts.app')

@section('title', 'Dashboard - CayMark')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header with CTA Button -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg shadow-lg p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-white mb-2">Welcome to CayMark</h1>
                    <p class="text-blue-100">Complete your registration to unlock all features</p>
                </div>
                <a href="{{ route('finish.registration') }}" 
                   class="bg-white text-blue-600 px-6 py-3 rounded-lg font-semibold hover:bg-blue-50 transition duration-200 shadow-md hover:shadow-lg">
                    FINISH REGISTRATION — SELECT YOUR MEMBERSHIP
                </a>
            </div>
        </div>

        <!-- Tab Navigation -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="border-b border-gray-200">
                <nav class="flex -mb-px">
                    <button onclick="showTab('user')" 
                            id="tab-user" 
                            class="tab-button active px-6 py-4 text-sm font-medium text-blue-600 border-b-2 border-blue-600">
                        USER
                    </button>
                    <button onclick="showTab('notifications')" 
                            id="tab-notifications" 
                            class="tab-button px-6 py-4 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 border-b-2 border-transparent">
                        NOTIFICATIONS
                    </button>
                </nav>
            </div>

            <!-- USER TAB -->
            <div id="content-user" class="tab-content p-6">
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
                    <form method="POST" action="{{ route('basic-dashboard.update-email') }}" class="flex items-center space-x-3">
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
                    <p class="text-sm text-gray-500 mt-2">Email can be changed. You will need to verify your new email address.</p>
                </div>

                <!-- Password Management -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password Management</label>
                    <button onclick="showPasswordModal()" 
                            class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-medium transition duration-200">
                        Change Password
                    </button>
                </div>

                <!-- Document Section -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-4">Documents</label>
                    
                    @if($documents->count() > 0)
                        <div class="space-y-4">
                            @foreach($documents as $document)
                                <div class="bg-gray-50 border border-gray-300 rounded-lg p-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $document->doc_type)) }}</p>
                                            <p class="text-sm text-gray-500 mt-1">
                                                @if($document->filename)
                                                    {{ $document->filename }}
                                                @else
                                                    Document uploaded
                                                @endif
                                            </p>
                                        </div>
                                        <div>
                                            @if($document->path)
                                                <a href="{{ asset('storage/' . $document->path) }}" 
                                                   target="_blank" 
                                                   class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                                    View
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <p class="text-sm text-gray-500 mt-4">Documents are view-only and cannot be uploaded, deleted, or edited from the Basic Dashboard.</p>
                    @else
                        <div class="bg-gray-50 border border-gray-300 rounded-lg p-6 text-center">
                            <p class="text-gray-500">No documents uploaded yet. Documents will appear here once added during registration.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- NOTIFICATIONS TAB -->
            <div id="content-notifications" class="tab-content hidden p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Notifications</h2>

                <!-- Default System Notification -->
                <div class="bg-amber-50 border-l-4 border-amber-400 p-6 rounded-lg mb-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="ml-4 flex-1">
                            <h3 class="text-lg font-semibold text-amber-900 mb-2">YOUR REGISTRATION IS NOT COMPLETE</h3>
                            <p class="text-amber-800 mb-4">SELECT A MEMBERSHIP TO ACTIVATE YOUR ACCOUNT.</p>
                            
                            <!-- CTA Button in Notifications Tab -->
                            <a href="{{ route('finish.registration') }}" 
                               class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition duration-200 shadow-md hover:shadow-lg">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                FINISH REGISTRATION — SELECT YOUR MEMBERSHIP
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Additional Notifications -->
                @if($notifications->count() > 0)
                    <div class="space-y-4">
                        @foreach($notifications as $notification)
                            <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition duration-200">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <p class="text-gray-900 font-medium">{{ $notification->data['message'] ?? 'Notification' }}</p>
                                        <p class="text-sm text-gray-500 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                    </div>
                                    @if(!$notification->read_at)
                                        <span class="ml-4 w-2 h-2 bg-blue-600 rounded-full"></span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 text-center">
                        <p class="text-gray-500">No additional notifications at this time.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- What You Can Do -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">What You Can Do</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex items-center space-x-3 p-3 bg-green-50 rounded-lg border border-green-200">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-green-800 font-medium">View profile details</span>
                </div>
                <div class="flex items-center space-x-3 p-3 bg-green-50 rounded-lg border border-green-200">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-green-800 font-medium">Change password</span>
                </div>
                <div class="flex items-center space-x-3 p-3 bg-green-50 rounded-lg border border-green-200">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-green-800 font-medium">Receive notifications</span>
                </div>
                <div class="flex items-center space-x-3 p-3 bg-green-50 rounded-lg border border-green-200">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-green-800 font-medium">Browse site listings (read-only)</span>
                </div>
            </div>
        </div>

        <!-- What's Restricted -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">What's Restricted</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex items-center space-x-3 p-3 bg-red-50 rounded-lg border border-red-200">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span class="text-red-800 font-medium">Cannot bid</span>
                </div>
                <div class="flex items-center space-x-3 p-3 bg-red-50 rounded-lg border border-red-200">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span class="text-red-800 font-medium">Cannot buy</span>
                </div>
                <div class="flex items-center space-x-3 p-3 bg-red-50 rounded-lg border border-red-200">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span class="text-red-800 font-medium">Cannot list items</span>
                </div>
                <div class="flex items-center space-x-3 p-3 bg-red-50 rounded-lg border border-red-200">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span class="text-red-800 font-medium">Cannot access auctions</span>
                </div>
                <div class="flex items-center space-x-3 p-3 bg-red-50 rounded-lg border border-red-200">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span class="text-red-800 font-medium">Cannot access messaging</span>
                </div>
                <div class="flex items-center space-x-3 p-3 bg-red-50 rounded-lg border border-red-200">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span class="text-red-800 font-medium">Cannot access payouts</span>
                </div>
                <div class="flex items-center space-x-3 p-3 bg-red-50 rounded-lg border border-red-200">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span class="text-red-800 font-medium">Cannot submit documents</span>
                </div>
                <div class="flex items-center space-x-3 p-3 bg-red-50 rounded-lg border border-red-200">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span class="text-red-800 font-medium">Cannot interact with transaction features</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Password Change Modal -->
<div id="passwordModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Change Password</h3>
            <form method="POST" action="{{ route('basic-dashboard.change-password') }}">
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
                           minlength="8"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                    <input type="password" 
                           name="password_confirmation" 
                           required
                           minlength="8"
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
</div>

<script>
function showTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Remove active class from all tabs
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('active', 'text-blue-600', 'border-blue-600');
        button.classList.add('text-gray-500', 'border-transparent');
    });
    
    // Show selected tab content
    document.getElementById('content-' + tabName).classList.remove('hidden');
    
    // Add active class to selected tab
    const activeButton = document.getElementById('tab-' + tabName);
    activeButton.classList.add('active', 'text-blue-600', 'border-blue-600');
    activeButton.classList.remove('text-gray-500', 'border-transparent');
}

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

@if(session('success'))
    <div class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
        {{ session('success') }}
    </div>
    <script>
        setTimeout(() => {
            document.querySelector('.fixed.top-4').remove();
        }, 3000);
    </script>
@endif

@if(session('error'))
    <div class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
        {{ session('error') }}
    </div>
    <script>
        setTimeout(() => {
            document.querySelector('.fixed.top-4').remove();
        }, 3000);
    </script>
@endif

@endsection

