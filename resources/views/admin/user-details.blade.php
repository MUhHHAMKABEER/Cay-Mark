@extends('layouts.admin')

@section('title', 'User Details - Admin')

@section('content')
<div class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <div class="bg-white shadow-sm mb-6 rounded-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('admin.users') }}" class="text-blue-600 hover:text-blue-800 mb-2 inline-block">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Users
                </a>
                <h1 class="text-3xl font-bold text-gray-900">{{ $user->name }}</h1>
                <p class="text-gray-600 mt-2">{{ $user->email }}</p>
            </div>
            <div class="flex space-x-3">
                <span class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full
                    {{ $user->role === 'seller' ? 'bg-purple-100 text-purple-800' : 
                       ($user->role === 'buyer' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800') }}">
                    {{ ucfirst($user->role ?? 'Guest') }}
                </span>
                @if($user->is_restricted ?? false)
                <span class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                    Restricted
                </span>
                @else
                <span class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                    Active
                </span>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - User Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Basic Information</h2>
                <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                            <input type="text" name="name" value="{{ $user->name }}" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" value="{{ $user->email }}" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                            <input type="text" name="phone" value="{{ $user->phone ?? '' }}" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                            <select name="role" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="buyer" {{ $user->role === 'buyer' ? 'selected' : '' }}>Buyer</option>
                                <option value="seller" {{ $user->role === 'seller' ? 'selected' : '' }}>Seller</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <div class="flex items-center space-x-4">
                                <label class="flex items-center">
                                    <input type="checkbox" name="is_restricted" value="1" {{ $user->is_restricted ?? false ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700">Restricted</span>
                                </label>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Registration Complete</label>
                            <span class="text-sm {{ $user->registration_complete ?? false ? 'text-green-600' : 'text-yellow-600' }}">
                                {{ $user->registration_complete ?? false ? 'Yes' : 'No' }}
                            </span>
                        </div>
                    </div>
                    <div class="mt-6">
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            <i class="fas fa-save mr-2"></i>Update User
                        </button>
                    </div>
                </form>
            </div>

            <!-- Account Statistics -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Account Statistics</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @if($user->role === 'seller')
                    <div class="text-center p-4 bg-gray-50 rounded-lg">
                        <div class="text-2xl font-bold text-gray-900">{{ $user->listings->count() ?? 0 }}</div>
                        <div class="text-sm text-gray-600 mt-1">Listings</div>
                    </div>
                    <div class="text-center p-4 bg-gray-50 rounded-lg">
                        <div class="text-2xl font-bold text-green-600">${{ number_format($user->payouts->sum('net_payout') ?? 0, 2) }}</div>
                        <div class="text-sm text-gray-600 mt-1">Total Payouts</div>
                    </div>
                    @endif
                    @if($user->role === 'buyer')
                    <div class="text-center p-4 bg-gray-50 rounded-lg">
                        <div class="text-2xl font-bold text-gray-900">{{ $user->bids->count() ?? 0 }}</div>
                        <div class="text-sm text-gray-600 mt-1">Bids</div>
                    </div>
                    <div class="text-center p-4 bg-gray-50 rounded-lg">
                        <div class="text-2xl font-bold text-blue-600">{{ $user->invoices->count() ?? 0 }}</div>
                        <div class="text-sm text-gray-600 mt-1">Invoices</div>
                    </div>
                    @endif
                    <div class="text-center p-4 bg-gray-50 rounded-lg">
                        <div class="text-2xl font-bold text-purple-600">{{ $user->payments->count() ?? 0 }}</div>
                        <div class="text-sm text-gray-600 mt-1">Payments</div>
                    </div>
                    <div class="text-center p-4 bg-gray-50 rounded-lg">
                        <div class="text-2xl font-bold text-orange-600">${{ number_format($user->wallet->total_balance ?? 0, 2) }}</div>
                        <div class="text-sm text-gray-600 mt-1">Wallet Balance</div>
                    </div>
                </div>
            </div>

            <!-- Activity Log -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Activity Log</h2>
                @if($activityLog && $activityLog->count() > 0)
                <div class="space-y-4">
                    @foreach($activityLog as $activity)
                    <div class="flex items-start border-b border-gray-100 pb-4">
                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center mr-4">
                            <i class="fas fa-{{ $activity['type'] === 'bid' ? 'gavel' : ($activity['type'] === 'payment' ? 'credit-card' : 'list') }} text-gray-600"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">{{ $activity['message'] }}</p>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ $activity['timestamp']->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-history text-4xl mb-3 text-gray-300"></i>
                    <p>No activity found</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Right Column - Actions & Details -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Quick Actions</h2>
                <div class="space-y-3">
                    <form action="{{ route('admin.users.reset-password', $user->id) }}" method="POST" id="resetPasswordForm">
                        @csrf
                        <button type="button" onclick="showResetPasswordModal()" 
                            class="w-full px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition text-left">
                            <i class="fas fa-key mr-2"></i>Reset Password
                        </button>
                    </form>

                    @if($user->is_restricted ?? false)
                    <form action="{{ route('admin.users.toggle-status', $user->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="action" value="reactivate">
                        <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-left confirm-action">
                            <i class="fas fa-check mr-2"></i>Reactivate Account
                        </button>
                    </form>
                    @else
                    <form action="{{ route('admin.users.toggle-status', $user->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="action" value="suspend">
                        <button type="button" onclick="showSuspendModal()" 
                            class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-left">
                            <i class="fas fa-ban mr-2"></i>Suspend Account
                        </button>
                    </form>
                    @endif
                </div>
            </div>

            <!-- Membership Details -->
            @if($user->subscriptions && $user->subscriptions->count() > 0)
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Memberships</h2>
                <div class="space-y-3">
                    @foreach($user->subscriptions as $subscription)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="font-medium text-gray-900">{{ $subscription->package->title ?? 'N/A' }}</div>
                        <div class="text-sm text-gray-600 mt-1">
                            <span class="px-2 py-1 text-xs rounded {{ $subscription->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($subscription->status) }}
                            </span>
                        </div>
                        @if($subscription->starts_at)
                        <div class="text-xs text-gray-500 mt-2">
                            Started: {{ $subscription->starts_at->format('M j, Y') }}
                        </div>
                        @endif
                        @if($subscription->ends_at)
                        <div class="text-xs text-gray-500">
                            Ends: {{ $subscription->ends_at->format('M j, Y') }}
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Wallet Information -->
            @if($user->wallet)
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Wallet</h2>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Available Balance:</span>
                        <span class="text-sm font-semibold text-gray-900">${{ number_format($user->wallet->available_balance ?? 0, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Locked Balance:</span>
                        <span class="text-sm font-semibold text-gray-900">${{ number_format($user->wallet->locked_balance ?? 0, 2) }}</span>
                    </div>
                    <div class="flex justify-between border-t pt-2">
                        <span class="text-sm font-medium text-gray-900">Total Balance:</span>
                        <span class="text-sm font-bold text-gray-900">${{ number_format($user->wallet->total_balance ?? 0, 2) }}</span>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Reset Password Modal -->
<div id="resetPasswordModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <form action="{{ route('admin.users.reset-password', $user->id) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                <input type="password" name="new_password" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                <input type="password" name="new_password_confirmation" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeResetPasswordModal()" 
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Reset Password
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Suspend Modal -->
<div id="suspendModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <form action="{{ route('admin.users.toggle-status', $user->id) }}" method="POST">
            @csrf
            <input type="hidden" name="action" value="suspend">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Reason</label>
                <textarea name="reason" rows="3" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                    placeholder="Enter reason for suspension..."></textarea>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeSuspendModal()" 
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 confirm-action">
                    Suspend Account
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function showResetPasswordModal() {
    document.getElementById('resetPasswordModal').classList.remove('hidden');
}

function closeResetPasswordModal() {
    document.getElementById('resetPasswordModal').classList.add('hidden');
}

function showSuspendModal() {
    document.getElementById('suspendModal').classList.remove('hidden');
}

function closeSuspendModal() {
    document.getElementById('suspendModal').classList.add('hidden');
}

// Close modals when clicking outside
window.onclick = function(event) {
    const resetModal = document.getElementById('resetPasswordModal');
    const suspendModal = document.getElementById('suspendModal');
    if (event.target == resetModal) {
        closeResetPasswordModal();
    }
    if (event.target == suspendModal) {
        closeSuspendModal();
    }
}
</script>
@endsection
