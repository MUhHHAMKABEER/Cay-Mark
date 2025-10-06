@extends('layouts.Seller')

@section('content')
<div class="min-h-screen overflow-y-auto">

<div class="container mx-auto px-4 py-6">
    {{-- Breadcrumb --}}
    <nav class="text-sm mb-6 text-gray-600">
        <ol class="list-reset flex">
            <li><a href="" class="text-primary-DEFAULT hover:underline transition-colors">Dashboard</a></li>
            <li><span class="mx-2">/</span></li>
            <li class="text-gray-800 font-semibold">My Marketplace Listings</li>
        </ol>
    </nav>

    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8">
        <h1 class="text-3xl font-bold text-gray-800">My Marketplace Listings</h1>
 <a href="#"
   class="mt-4 md:mt-0 px-5 py-2.5 bg-blue-600 text-white font-medium rounded-lg
          shadow-lg hover:bg-blue-700 hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-300">
    + Add New Product
</a>


    </div>

    {{-- Stats Overview --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-blue-50 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Products</p>
                    {{-- <p class="text-2xl font-bold text-gray-800">{{ $products->total() }}</p> --}}
                </div>
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-green-50 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Active Products</p>
                    <p class="text-2xl font-bold text-gray-800">
                        @php
                            $activeCount = 0;
                            foreach ($products as $product) {
                                if ($product->status === 'active') $activeCount++;
                            }
                        @endphp
                        {{ $activeCount }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-purple-50 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Sales</p>
                    <p class="text-2xl font-bold text-gray-800">
                        @php
                            $totalSales = 0;
                            foreach ($products as $product) {
                                $totalSales += $product->sold_count * $product->price;
                            }
                        @endphp
                        ${{ number_format($totalSales, 2) }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Search and Filters --}}
    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 mb-8">
        <form method="GET" action="" class="flex flex-col md:flex-row gap-4 items-center">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                    </svg>
                </div>
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Search products by name, category, or SKU..."
                       class="pl-10 w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-DEFAULT focus:border-transparent">
            </div>

            {{-- Category Filter --}}
            <select name="category" class="px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-DEFAULT focus:border-transparent">
                <option value="">All Categories</option>
                <option value="electronics" {{ request('category') == 'electronics' ? 'selected' : '' }}>Electronics</option>
                <option value="fashion" {{ request('category') == 'fashion' ? 'selected' : '' }}>Fashion</option>
                <option value="home" {{ request('category') == 'home' ? 'selected' : '' }}>Home & Garden</option>
                <option value="sports" {{ request('category') == 'sports' ? 'selected' : '' }}>Sports</option>
            </select>

            {{-- Status Filter --}}
            <select name="status" class="px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-DEFAULT focus:border-transparent">
                <option value="">All Status</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="out_of_stock" {{ request('status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
            </select>

            <button type="submit"
                    class="w-full md:w-auto px-5 py-2.5 bg-primary-DEFAULT text-white font-medium rounded-lg hover:bg-primary-dark transition-colors flex items-center justify-center">
                <svg class="w-4 h-4 mr-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                </svg>
                Search
            </button>
        </form>
    </div>

    {{-- Product Listings Grid --}}
    @if($products->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">
        @foreach ($products as $product)
            @php
                // Normalize main image
                $mainImage = optional($product->images->first())->image_path
                    ? asset('uploads/listings/' . $product->images->first()->image_path)
                    : asset('images/placeholder-product.png');

                // Status badge styling
                $statusConfig = [
                    'active' => ['color' => 'green', 'text' => 'Active'],
                    'draft' => ['color' => 'gray', 'text' => 'Draft'],
                    'out_of_stock' => ['color' => 'red', 'text' => 'Out of Stock'],
                    'pending' => ['color' => 'yellow', 'text' => 'Pending']
                ];
                $status = $statusConfig[$product->status] ?? $statusConfig['draft'];
            @endphp

            <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 flex flex-col h-full">
                {{-- Product image with status badge --}}
                <div class="relative">
                    <img src="{{ $mainImage }}"
                         alt="{{ $product->name }}"
                         class="w-full h-52 object-cover">

                    <span class="absolute top-3 right-3 bg-{{ $status['color'] }}-600 text-white text-xs font-semibold px-3 py-1 rounded-full shadow-md">
                        {{ $status['text'] }}
                    </span>

                    {{-- Stock indicator --}}
                    @if($product->status === 'active')
                    <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-40 text-white p-2 text-xs">
                        <div class="flex justify-between mb-1">
                            <span>In Stock</span>
                            <span>{{ $product->stock_quantity }} available</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-1.5">
                            @php
                                $stockPercentage = min(100, ($product->stock_quantity / max(1, $product->initial_stock)) * 100);
                                $stockColor = $stockPercentage > 50 ? 'green' : ($stockPercentage > 20 ? 'yellow' : 'red');
                            @endphp
                            <div class="bg-{{ $stockColor }}-500 h-1.5 rounded-full" style="width: {{ $stockPercentage }}%"></div>
                        </div>
                    </div>
                    @endif
                </div>

                <div class="p-5 flex-grow">
                    {{-- Title --}}
                    <h2 class="text-xl font-bold text-gray-800 mb-2 line-clamp-1">
                        {{ $product->name }}
                    </h2>

                    {{-- Product details --}}
                    <div class="flex items-center text-sm text-gray-600 mb-3">
                        <svg class="w-4 h-4 mr-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 1v3m5-3v3m5-3v3M1 7h18M5 11h10M2 3h16a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1Z"/>
                        </svg>
                        <span>Added: {{ $product->created_at->format('M d, Y') }}</span>
                    </div>

                    {{-- Category and SKU --}}
                    <div class="flex items-center text-sm text-gray-600 mb-3">
                        <svg class="w-4 h-4 mr-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 6v13a1 1 0 0 0 1 1h16a1 1 0 0 0 1-1V6M1 6l9-4 9 4M1 6l9 4 9-4m-9-4v16"/>
                        </svg>
                        <span class="mr-3 capitalize">{{ $product->category }}</span>
                        <span class="text-xs bg-gray-100 px-2 py-1 rounded">SKU: {{ $product->sku }}</span>
                    </div>

                    {{-- Price and sales information --}}
                    <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-sm font-medium text-gray-600">Price</span>
                            <span class="text-lg font-bold text-primary-DEFAULT">${{ number_format($product->price, 2) }}</span>
                        </div>
                        <div class="text-xs text-gray-500">
                            {{ $product->sold_count }} {{ Str::plural('sale', $product->sold_count) }} •
                            ${{ number_format($product->sold_count * $product->price, 2) }} revenue
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="px-5 pb-5 mt-auto">
                    <div class="flex space-x-3">
                        <a href=""
                           class="flex-1 px-4 py-2.5 bg-primary-DEFAULT text-white text-center font-medium rounded-lg hover:bg-primary-dark transition-colors">
                            View Details
                        </a>
                        <a href=""
                           class="px-4 py-2.5 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                            <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    @else
    {{-- Empty state --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
        <svg class="w-16 h-16 mx-auto text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
        </svg>
        <h3 class="mt-5 text-xl font-medium text-gray-900">No products found</h3>
        <p class="mt-2 text-gray-500">You haven't listed any products in the marketplace yet.</p>
        <div class="mt-6">
            <a href="#" class="px-5 py-2.5 bg-primary-DEFAULT text-white font-medium rounded-lg hover:bg-primary-dark transition-colors inline-flex items-center">
                <svg class="w-4 h-4 mr-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Add Your First Product
            </a>
        </div>

    </div>
    @endif


</div>
</div>

<style>
    .line-clamp-1 {
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .pagination {
        display: flex;
        justify-content: center;
        list-style-type: none;
        padding: 0;
    }

    .pagination li {
        margin: 0 4px;
    }

    .pagination li a,
    .pagination li span {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        color: #4b5563;
        font-weight: 500;
        transition: all 0.2s;
    }

    .pagination li a:hover {
        background-color: #f3f4f6;
        color: #1f2937;
    }

    .pagination li.active span {
        background-color: #0066ff;
        color: white;
        border-color: #0066ff;
    }

    .pagination li.disabled span {
        color: #9ca3af;
        cursor: not-allowed;
    }
</style>
@endsection
