@extends('layouts.welcome')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4 max-w-4xl">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-0">
                <div class="relative h-80 md:h-auto min-h-[320px] bg-gray-200">
                    @if($listing->images->first())
                        <img src="{{ asset('storage/' . $listing->images->first()->image_path) }}" 
                             alt="{{ $listing->year }} {{ $listing->make }} {{ $listing->model }}" 
                             class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-gray-400">
                            <span class="material-icons-round text-8xl">directions_car</span>
                        </div>
                    @endif
                    <span class="absolute top-4 left-4 bg-green-600 text-white text-xs font-bold px-3 py-1 rounded-full">Buy Now</span>
                </div>
                <div class="p-6 md:p-8 flex flex-col justify-center">
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">
                        {{ $listing->year }} {{ $listing->make }} {{ $listing->model }}
                    </h1>
                    <p class="text-gray-600 text-sm mb-4">
                        Item #{{ $listing->item_number ?? 'CM' . str_pad($listing->id, 6, '0', STR_PAD_LEFT) }}
                    </p>
                    <div class="text-3xl font-bold text-blue-600 mb-6">
                        ${{ number_format($listing->price ?? $listing->buy_now_price ?? 0, 2) }}
                    </div>
                    <p class="text-gray-600 text-sm mb-6">Fixed price. Purchase immediately — no bidding.</p>

                    @guest
                        <div class="space-y-3">
                            <a href="{{ route('login') }}?redirect={{ urlencode(request()->url()) }}" 
                               class="block w-full text-center px-6 py-4 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition">
                                Log in to purchase
                            </a>
                            <a href="{{ route('register') }}?redirect={{ urlencode(request()->url()) }}" 
                               class="block w-full text-center px-6 py-4 border-2 border-blue-600 text-blue-600 font-semibold rounded-xl hover:bg-blue-50 transition">
                                Register to purchase
                            </a>
                        </div>
                    @else
                        @if(auth()->user()->role === 'buyer')
                            <form action="{{ route('listing.buy', $listing->id) }}" method="POST" id="buy-now-form">
                                @csrf
                                <button type="submit" class="w-full px-6 py-4 bg-green-600 text-white font-semibold rounded-xl hover:bg-green-700 transition flex items-center justify-center gap-2">
                                    <span class="material-icons-round">shopping_cart</span>
                                    Purchase now
                                </button>
                            </form>
                            <p class="text-xs text-gray-500 mt-3">By purchasing, you agree to CayMark terms. Seller will be notified.</p>
                        @else
                            <p class="text-amber-700 font-medium">Buyer account required to use Buy Now. <a href="{{ route('profile.edit') }}" class="underline">Manage account</a>.</p>
                        @endif
                    @endguest
                </div>
            </div>
        </div>
        <div class="mt-6 text-center">
            <a href="{{ route('marketplace.index') }}" class="text-gray-600 hover:text-blue-600 text-sm">← Back to marketplace</a>
        </div>
    </div>
</div>
@if(auth()->check() && auth()->user()->role === 'buyer')
<script>
document.getElementById('buy-now-form')?.addEventListener('submit', function(e) {
    e.preventDefault();
    var form = this;
    var btn = form.querySelector('button[type="submit"]');
    if (btn) { btn.disabled = true; btn.textContent = 'Processing...'; }
    var fd = new FormData(form);
    fetch(form.action, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || form.querySelector('input[name="_token"]')?.value, 'Accept': 'application/json' },
        body: fd
    }).then(function(r) { return r.json(); }).then(function(data) {
        if (data.success) {
            window.location.href = '{{ route("buyer.messaging-center") }}';
        } else {
            alert(data.message || 'Purchase could not be completed.');
            if (btn) { btn.disabled = false; btn.innerHTML = '<span class="material-icons-round">shopping_cart</span> Purchase now'; }
        }
    }).catch(function() {
        alert('Request failed. Please try again.');
        if (btn) { btn.disabled = false; btn.innerHTML = '<span class="material-icons-round">shopping_cart</span> Purchase now'; }
    });
});
</script>
@endif
@endsection
