@extends('layouts.dashboard')

@section('title', 'Support Center - Buyer Dashboard')

@section('content')
<div class="bg-gray-50 min-h-screen">
    <div class="bg-white shadow-sm mb-6 rounded-lg p-6">
        <h1 class="text-3xl font-bold text-gray-900">Support Center</h1>
        <p class="text-gray-600 mt-2">Submit a request and our team will respond as quickly as possible.</p>
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

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Submit a Request</h2>
        <form method="POST" action="{{ route('buyer.customer-support.submit') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">What is this request about?</label>
                <select name="title" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Select a category...</option>
                    @foreach($supportCategories as $option)
                        <option value="{{ $option }}" {{ old('title') === $option ? 'selected' : '' }}>{{ $option }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                <textarea name="message" rows="6" required maxlength="800"
                          placeholder="Write your message here..."
                          class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('message') }}</textarea>
                <p class="text-xs text-gray-500 mt-1">Message limit: 800 characters.</p>
            </div>
            <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-blue-700 transition duration-200">
                Submit Request
            </button>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Ticket History</h2>
        @php
            $tickets = \App\Models\SupportTicket::where('user_id', auth()->id())->latest()->get();
        @endphp
        @if($tickets->count() > 0)
            <div class="space-y-4">
                @foreach($tickets as $ticket)
                <div class="border border-gray-200 rounded-lg p-4 {{ $ticket->status === 'open' ? 'bg-blue-50 border-blue-200' : '' }}">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="font-semibold text-gray-900">{{ $ticket->title }}</h3>
                        <span class="px-3 py-1 text-xs font-semibold rounded-full
                            {{ $ticket->status === 'open' ? 'bg-blue-100 text-blue-800' : '' }}
                            {{ $ticket->status === 'in_progress' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $ticket->status === 'resolved' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $ticket->status === 'closed' ? 'bg-gray-100 text-gray-800' : '' }}">
                            {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                        </span>
                    </div>
                    <p class="text-gray-600 text-sm mb-2">{{ $ticket->message }}</p>
                    <p class="text-xs text-gray-400">Submitted {{ $ticket->created_at->diffForHumans() }}</p>
                    @if($ticket->admin_reply)
                    <div class="mt-3 p-3 bg-gray-50 border border-gray-200 rounded-lg">
                        <p class="text-xs font-semibold text-gray-500 mb-1">Admin Reply:</p>
                        <p class="text-sm text-gray-700">{{ $ticket->admin_reply }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ $ticket->replied_at?->diffForHumans() }}</p>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        @else
            <div class="bg-gray-50 rounded-lg p-6 text-center">
                <p class="text-gray-500 text-lg">No tickets submitted yet.</p>
                <p class="text-gray-400 text-sm mt-2">Submit a ticket above and it will appear here.</p>
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-3">Quick Help</h3>
            <ul class="space-y-2 text-sm text-blue-700">
                <li><a href="{{ route('help-center') }}" class="hover:underline">View FAQ</a></li>
                <li><a href="{{ route('video-guide') }}" class="hover:underline">Auction Guide</a></li>
                <li><a href="{{ route('buyer-guide') }}" class="hover:underline">Buyer Guide</a></li>
                <li><a href="{{ route('video-guide') }}" class="hover:underline">How Auctions Work</a></li>
            </ul>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-3">Contact Us</h3>
            <p class="text-sm text-gray-700 mb-1">support@caymark.com</p>
            <p class="text-sm text-gray-700">For urgent matters call or WhatsApp us at +1 (242) 806-6275</p>
        </div>
    </div>
</div>
@endsection
