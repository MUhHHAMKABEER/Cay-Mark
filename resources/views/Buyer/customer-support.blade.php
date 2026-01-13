@extends('layouts.dashboard')

@section('title', 'Customer Support - Buyer Dashboard')

@section('content')
<div class="bg-gray-50 min-h-screen">
    <!-- Header -->
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

    <!-- Submit Support Ticket -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Submit Support Ticket</h2>
        <form method="POST" action="{{ route('buyer.customer-support.submit') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Ticket Title</label>
                <input type="text" 
                       name="title" 
                       required
                       placeholder="Brief description of your issue"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                <textarea name="message" 
                          rows="6" 
                          required
                          placeholder="Please provide detailed information about your issue or inquiry..."
                          class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
            </div>
            <button type="submit" 
                    class="bg-blue-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-blue-700 transition duration-200">
                Submit Ticket
            </button>
        </form>
    </div>

    <!-- Ticket History -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Ticket History</h2>
        <div class="bg-gray-50 rounded-lg p-6 text-center">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <p class="text-gray-500 text-lg">Ticket history will appear here once tickets are submitted.</p>
            <p class="text-gray-400 text-sm mt-2">You'll be able to view all your support tickets and their responses here.</p>
        </div>
    </div>
</div>
@endsection
