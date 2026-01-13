@extends('layouts.admin')

@section('title', 'Notifications & Message Log - Admin')

@section('content')
<div class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <div class="bg-white shadow-sm mb-6 rounded-lg p-6">
        <h1 class="text-3xl font-bold text-gray-900">Notifications & Message Log</h1>
        <p class="text-gray-600 mt-2">View all platform notifications and messages</p>
    </div>

    <!-- Tabs -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <button onclick="showTab('notifications')" id="tab-notifications" 
                    class="tab-button active px-6 py-4 text-sm font-medium text-blue-600 border-b-2 border-blue-600">
                    <i class="fas fa-bell mr-2"></i>Notifications ({{ $notifications->total() ?? 0 }})
                </button>
                <button onclick="showTab('messages')" id="tab-messages" 
                    class="tab-button px-6 py-4 text-sm font-medium text-gray-500 border-b-2 border-transparent hover:text-gray-700 hover:border-gray-300">
                    <i class="fas fa-comments mr-2"></i>Messages ({{ $messages->total() ?? 0 }})
                </button>
            </nav>
        </div>
    </div>

    <!-- Notifications Tab -->
    <div id="notifications-tab" class="tab-content">
        <!-- Filters -->
        <div class="bg-white rounded-lg shadow mb-6 p-6">
            <form method="GET" action="{{ route('admin.notifications') }}" class="flex flex-wrap gap-4">
                <input type="hidden" name="tab" value="notifications">
                <div class="flex-1 min-w-[200px]">
                    <input type="text" name="user_id" value="{{ request('user_id') }}" placeholder="User ID..." 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <input type="text" name="type" value="{{ request('type') }}" placeholder="Notification type..." 
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-search mr-2"></i>Filter
                    </button>
                </div>
                @if(request('user_id') || request('type'))
                <div>
                    <a href="{{ route('admin.notifications') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                        Clear
                    </a>
                </div>
                @endif
            </form>
        </div>

        <!-- Notifications Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">All Notifications</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Message</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Read</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($notifications as $notification)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                #{{ $notification->id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $notification->notifiable->name ?? 'N/A' }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $notification->notifiable->email ?? 'N/A' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ ucfirst($notification->data['type'] ?? 'notification') }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 max-w-md truncate">
                                    {{ $notification->data['message'] ?? $notification->data['body'] ?? 'N/A' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($notification->read_at)
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Read
                                </span>
                                @else
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Unread
                                </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $notification->created_at->format('M j, Y') }}<br>
                                <span class="text-xs">{{ $notification->created_at->format('g:i A') }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <form action="{{ route('admin.notifications.resend', $notification->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-900" title="Resend">
                                            <i class="fas fa-paper-plane"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-bell text-4xl mb-3 text-gray-300"></i>
                                <p>No notifications found</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($notifications->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $notifications->links() }}
            </div>
            @endif
        </div>
    </div>

    <!-- Messages Tab -->
    <div id="messages-tab" class="tab-content hidden">
        <!-- Filters -->
        <div class="bg-white rounded-lg shadow mb-6 p-6">
            <form method="GET" action="{{ route('admin.notifications') }}" class="flex flex-wrap gap-4">
                <input type="hidden" name="tab" value="messages">
                <div class="flex-1 min-w-[200px]">
                    <input type="text" name="chat_id" value="{{ request('chat_id') }}" placeholder="Chat ID..." 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-search mr-2"></i>Filter
                    </button>
                </div>
                @if(request('chat_id'))
                <div>
                    <a href="{{ route('admin.notifications') }}?tab=messages" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                        Clear
                    </a>
                </div>
                @endif
            </form>
        </div>

        <!-- Messages Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">All Messages</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Chat</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sender</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Message</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($messages as $message)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                #{{ $message->id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    Chat #{{ $message->chat_id ?? 'N/A' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $message->user->name ?? 'N/A' }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $message->user->email ?? 'N/A' }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 max-w-md">
                                    {{ Str::limit($message->message ?? 'N/A', 100) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $message->created_at->format('M j, Y') }}<br>
                                <span class="text-xs">{{ $message->created_at->format('g:i A') }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-comments text-4xl mb-3 text-gray-300"></i>
                                <p>No messages found</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($messages->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $messages->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<style>
.tab-button {
    transition: all 0.2s ease;
}

.tab-button.active {
    color: #2563eb;
    border-bottom-color: #2563eb;
}

.tab-content {
    display: block;
}

.tab-content.hidden {
    display: none;
}
</style>

<script>
function showTab(tab) {
    // Hide all tabs
    document.getElementById('notifications-tab').classList.add('hidden');
    document.getElementById('messages-tab').classList.add('hidden');
    
    // Remove active class from all buttons
    document.getElementById('tab-notifications').classList.remove('active', 'text-blue-600', 'border-blue-600');
    document.getElementById('tab-notifications').classList.add('text-gray-500', 'border-transparent');
    document.getElementById('tab-messages').classList.remove('active', 'text-blue-600', 'border-blue-600');
    document.getElementById('tab-messages').classList.add('text-gray-500', 'border-transparent');
    
    // Show selected tab
    if (tab === 'notifications') {
        document.getElementById('notifications-tab').classList.remove('hidden');
        document.getElementById('tab-notifications').classList.add('active', 'text-blue-600', 'border-blue-600');
        document.getElementById('tab-notifications').classList.remove('text-gray-500', 'border-transparent');
    } else if (tab === 'messages') {
        document.getElementById('messages-tab').classList.remove('hidden');
        document.getElementById('tab-messages').classList.add('active', 'text-blue-600', 'border-blue-600');
        document.getElementById('tab-messages').classList.remove('text-gray-500', 'border-transparent');
    }
}

// Show correct tab on page load
@if(request('tab') === 'messages')
    showTab('messages');
@else
    showTab('notifications');
@endif
</script>
@endsection
