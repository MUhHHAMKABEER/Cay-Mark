@extends('layouts.dashboard')

@section('title', 'Notifications - Buyer Dashboard')

@section('content')
<div class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <div class="bg-white shadow-sm mb-6 rounded-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Notifications</h1>
                <p class="text-gray-600 mt-2">All system alerts and updates</p>
            </div>
            @if(isset($notifications) && $notifications->count() > 0)
                <div class="text-sm text-gray-500">
                    {{ $notifications->whereNull('read_at')->count() }} unread
                </div>
            @endif
        </div>
    </div>

    <!-- Notifications List -->
    <div class="bg-white rounded-lg shadow p-6">
        @if(isset($notifications) && $notifications->count() > 0)
            <div class="space-y-4">
                @foreach($notifications as $notification)
                    @php
                        $notificationData = is_array($notification->data) ? $notification->data : [];
                        $message = $notificationData['message'] ?? $notificationData['title'] ?? 'Notification';
                        $type = $notificationData['type'] ?? 'info';
                    @endphp
                    <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition duration-200 {{ !$notification->read_at ? 'bg-blue-50 border-blue-200' : '' }}">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-2 mb-1">
                                    @if($type === 'bid')
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    @elseif($type === 'outbid')
                                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                    @elseif($type === 'win')
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                        </svg>
                                    @elseif($type === 'payment')
                                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    @endif
                                    <p class="text-gray-900 font-medium">{{ $message }}</p>
                                </div>
                                <p class="text-sm text-gray-500 mt-1">
                                    <span>{{ $notification->created_at->format('M d, Y h:i A') }}</span>
                                    <span class="mx-2">•</span>
                                    <span>{{ $notification->created_at->diffForHumans() }}</span>
                                </p>
                            </div>
                            <div class="flex items-center space-x-3 ml-4">
                                @if(!$notification->read_at)
                                    <span class="w-3 h-3 bg-blue-600 rounded-full flex-shrink-0" title="Unread"></span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12 bg-gray-50 rounded-lg">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <p class="text-gray-500 text-lg font-medium mb-2">No notifications at this time.</p>
                <p class="text-gray-400 text-sm">You'll receive notifications for:</p>
                <ul class="text-gray-400 text-sm mt-2 space-y-1">
                    <li>• Bid confirmations</li>
                    <li>• Outbid alerts</li>
                    <li>• Auction win notifications</li>
                    <li>• Payment reminders</li>
                    <li>• Payment confirmations</li>
                    <li>• Pickup coordination alerts</li>
                </ul>
            </div>
        @endif
    </div>
</div>
@endsection
