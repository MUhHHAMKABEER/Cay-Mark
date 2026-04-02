@extends('layouts.dashboard')

@section('title', 'Customer Support - Buyer Dashboard')

@section('content')
<div class="bg-gray-50 min-h-screen">
    <div class="bg-white shadow-sm mb-6 rounded-lg p-6">
        <h1 class="text-3xl font-bold text-gray-900">Customer Support</h1>
        <p class="text-gray-600 mt-2">Submit support inquiries and view ticket history</p>
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
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Submit Support Ticket</h2>
        <form method="POST" action="{{ route('buyer.customer-support.submit') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Ticket Title</label>
                <select name="title" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Select issue type...</option>
                    @foreach(\App\Models\SupportTicket::TITLE_OPTIONS as $option)
                        <option value="{{ $option }}" {{ old('title') === $option ? 'selected' : '' }}>{{ $option }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                <textarea name="message" rows="6" required
                          placeholder="Please provide detailed information about your issue or inquiry..."
                          class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('message') }}</textarea>
            </div>
            <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-blue-700 transition duration-200">
                Submit Ticket
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
</div>
@endsection
