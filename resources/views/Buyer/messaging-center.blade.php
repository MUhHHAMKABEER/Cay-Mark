@extends('layouts.dashboard')

@section('title', 'Messaging Center - Buyer Dashboard')

@section('content')
<div class="bg-gray-50 min-h-screen p-6">
    <!-- Header -->
    <div class="bg-white shadow-sm mb-6 rounded-lg p-6">
        <h1 class="text-3xl font-bold text-gray-900">Messaging Center</h1>
        <p class="text-gray-600 mt-2">Post-payment messaging threads for pickup coordination</p>
    </div>

    <!-- Messaging Threads -->
    <div class="bg-white rounded-lg shadow p-6">
        @if(isset($messagingThreads) && $messagingThreads->count() > 0)
            <div class="space-y-4">
                @foreach($messagingThreads as $thread)
                    <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition duration-200">
                        <div class="flex items-center space-x-4">
                            <!-- Vehicle Thumbnail -->
                            <div class="h-20 w-20 bg-gray-200 rounded-lg overflow-hidden flex-shrink-0">
                                @if($thread->listing && $thread->listing->images->first())
                                    <img src="{{ asset('storage/' . $thread->listing->images->first()->image_path) }}" 
                                         alt="{{ $thread->listing->make }} {{ $thread->listing->model }}" 
                                         class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900">
                                    @if($thread->listing)
                                        {{ $thread->listing->year ?? '' }} {{ $thread->listing->make ?? '' }} {{ $thread->listing->model ?? '' }}
                                    @else
                                        Vehicle Listing
                                    @endif
                                </h3>
                                <p class="text-sm text-gray-600">
                                    Seller: {{ $thread->seller->name ?? 'N/A' }}
                                </p>
                                @if($thread->invoice)
                                    <p class="text-xs text-gray-500 mt-1">
                                        Invoice #{{ $thread->invoice->id }}
                                    </p>
                                @endif
                            </div>
                            
                            <div>
                                @if($thread->invoice)
                                    <a href="{{ route('post-auction.thread', $thread->invoice->id) }}" 
                                       class="bg-blue-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-blue-700 transition duration-200">
                                        View Thread
                                    </a>
                                @else
                                    <span class="bg-gray-300 text-gray-600 px-4 py-2 rounded-lg font-medium cursor-not-allowed">
                                        No Thread
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12 bg-gray-50 rounded-lg">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                <p class="text-gray-500 text-lg">No messaging threads available.</p>
                <p class="text-gray-400 text-sm mt-2">Messaging Center unlocks after payment is completed for won auctions.</p>
            </div>
        @endif
    </div>
</div>
@endsection
