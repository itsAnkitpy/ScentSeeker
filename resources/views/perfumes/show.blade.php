@extends('layouts.app')

@section('title', $perfume->name)

@section('content')
<div class="min-h-screen bg-gradient-to-br from-rose-50 via-pink-50 to-purple-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Main Product Section -->
        <div class="bg-white/80 backdrop-blur-lg rounded-3xl shadow-xl border border-white/50 overflow-hidden mb-8">
            <div class="lg:flex">
                <!-- Product Image -->
                <div class="lg:w-1/2 relative">
                    <div class="aspect-square bg-gray-100 relative overflow-hidden">
                        <img class="w-full h-full object-cover" 
                             src="{{ $perfume->image_url ?: 'https://images.unsplash.com/photo-1541643600914-78b084683601?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80' }}" 
                             alt="{{ $perfume->name }}">
                        
                        <!-- Wishlist Button -->
                        <button class="absolute top-4 right-4 w-12 h-12 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center hover:bg-pink-500 hover:text-white transition-all group">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <!-- Product Details -->
                <div class="lg:w-1/2 p-8">
                    <!-- Season Badge and Brand -->
                    <div class="flex items-center gap-3 mb-4">
                        <span class="px-3 py-1 bg-gradient-to-r from-pink-500 to-purple-600 text-white text-sm font-medium rounded-full">
                            {{ $perfume->season ?? 'Spring' }}
                        </span>
                        <span class="text-gray-600 font-medium">{{ $perfume->brand }}</span>
                    </div>
                    
                    <!-- Product Name -->
                    <h1 class="text-3xl lg:text-4xl font-bold font-playfair text-gray-900 mb-4">{{ $perfume->name }}</h1>
                    
                    <!-- Rating -->
                    <div class="flex items-center gap-2 mb-6" x-data="{ rating: 4.7, reviewCount: 3 }">
                        <div class="flex text-yellow-400">
                            <template x-for="i in 5">
                                <svg class="w-5 h-5 fill-current" :class="i <= rating ? 'text-yellow-400' : 'text-gray-300'" viewBox="0 0 20 20">
                                    <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                </svg>
                            </template>
                        </div>
                        <span class="text-gray-600" x-text="`${rating} (${reviewCount} reviews)`"></span>
                    </div>
                    
                    <!-- Price Section -->
                    <div class="mb-6" x-data="{ 
                            minPrice: 4999, 
                            sellerCount: 5, 
                            bestSeller: 'Luxury Scents' 
                        }">
                        <div class="flex items-baseline gap-2 mb-2">
                            <span class="text-3xl font-bold text-gray-900">₹<span x-text="minPrice.toLocaleString()"></span></span>
                            <span class="text-gray-600">lowest price from <span x-text="sellerCount"></span> sellers</span>
                        </div>
                        <div class="flex items-center gap-2 text-green-600">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>Best price at <span x-text="bestSeller" class="font-semibold"></span></span>
                        </div>
                    </div>
                    
                    <!-- Description -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Description</h3>
                        <p class="text-gray-600 leading-relaxed">
                            {{ $perfume->description ?: 'A delicate floral fragrance with notes of rose, jasmine, and lily of the valley. Perfect for spring days and evenings, this elegant scent combines the freshness of morning dew with the sophistication of timeless florals.' }}
                        </p>
                    </div>
                    
                    <!-- Size Selector -->
                    <div class="mb-8" x-data="{ selectedSize: '50ml' }">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3">Select Size</h3>
                        <div class="flex gap-3">
                            <template x-for="size in ['30ml', '50ml', '100ml']">
                                <button @click="selectedSize = size"
                                        :class="{
                                            'border-pink-500 bg-pink-50 text-pink-700': selectedSize === size,
                                            'border-gray-200 bg-white text-gray-700 hover:border-pink-300': selectedSize !== size
                                        }"
                                        class="px-4 py-2 border rounded-xl font-medium transition-all"
                                        x-text="size">
                                </button>
                            </template>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-3">
                        <button class="flex-1 bg-gradient-to-r from-pink-500 to-purple-600 text-white py-4 px-6 rounded-2xl font-semibold hover:from-pink-600 hover:to-purple-700 transition-all transform hover:scale-105 shadow-lg flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            Compare Prices
                        </button>
                        <button class="bg-white/90 border border-gray-200 text-gray-700 py-4 px-6 rounded-2xl font-semibold hover:bg-white hover:shadow-lg transition-all flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Read Reviews
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabbed Content Section -->
        <div class="bg-white/80 backdrop-blur-lg rounded-3xl shadow-xl border border-white/50 overflow-hidden"
             x-data="{ 
                activeTab: 'sellers',
                prices: [], 
                isLoading: true, 
                error: null,
                fetchPrices() {
                    this.isLoading = true;
                    this.error = null;
                    fetch(`/api/v1/perfumes/{{ $perfume->id }}/prices`)
                        .then(response => {
                            if (!response.ok) {
                                // Fallback to mock data for demo
                                return {
                                    data: [
                                        { id: 1, seller: { name: 'Luxury Scents' }, price: 4999, currency: 'INR', rating: 4.8, stock_status: 'In Stock', product_url: '#' },
                                        { id: 2, seller: { name: 'Perfume World' }, price: 5299, currency: 'INR', rating: 4.6, stock_status: 'In Stock', product_url: '#' },
                                        { id: 3, seller: { name: 'Fragrance Hub' }, price: 5499, currency: 'INR', rating: 4.5, stock_status: 'In Stock', product_url: '#' },
                                        { id: 4, seller: { name: 'Scent Studio' }, price: 5599, currency: 'INR', rating: 4.3, stock_status: 'In Stock', product_url: '#' },
                                        { id: 5, seller: { name: 'Aroma Avenue' }, price: 5799, currency: 'INR', rating: 4.2, stock_status: 'Out of Stock', product_url: '#' }
                                    ]
                                };
                            }
                            return response.json();
                        })
                        .then(data => {
                            this.prices = data.data || data;
                            this.isLoading = false;
                        })
                        .catch(err => {
                            // Fallback to mock data
                            this.prices = [
                                { id: 1, seller: { name: 'Luxury Scents' }, price: 4999, currency: 'INR', rating: 4.8, stock_status: 'In Stock', product_url: '#' },
                                { id: 2, seller: { name: 'Perfume World' }, price: 5299, currency: 'INR', rating: 4.6, stock_status: 'In Stock', product_url: '#' },
                                { id: 3, seller: { name: 'Fragrance Hub' }, price: 5499, currency: 'INR', rating: 4.5, stock_status: 'In Stock', product_url: '#' }
                            ];
                            this.isLoading = false;
                        });
                }
            }"
            x-init="fetchPrices()">
            
            <!-- Tab Navigation -->
            <div class="border-b border-gray-200">
                <nav class="flex px-8">
                    <button @click="activeTab = 'sellers'"
                            :class="{
                                'border-pink-500 text-pink-600': activeTab === 'sellers',
                                'border-transparent text-gray-500 hover:text-gray-700': activeTab !== 'sellers'
                            }"
                            class="py-4 px-6 border-b-2 font-medium text-sm transition-colors">
                        All Sellers
                    </button>
                    <button @click="activeTab = 'details'"
                            :class="{
                                'border-pink-500 text-pink-600': activeTab === 'details',
                                'border-transparent text-gray-500 hover:text-gray-700': activeTab !== 'details'
                            }"
                            class="py-4 px-6 border-b-2 font-medium text-sm transition-colors">
                        Perfume Details
                    </button>
                    <button @click="activeTab = 'reviews'"
                            :class="{
                                'border-pink-500 text-pink-600': activeTab === 'reviews',
                                'border-transparent text-gray-500 hover:text-gray-700': activeTab !== 'reviews'
                            }"
                            class="py-4 px-6 border-b-2 font-medium text-sm transition-colors">
                        Reviews
                    </button>
                </nav>
            </div>

            <!-- Tab Content -->
            <div class="p-8">
                <!-- All Sellers Tab -->
                <div x-show="activeTab === 'sellers'" x-transition>
                    <div class="mb-6">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">All Sellers (<span x-text="prices.length"></span>)</h3>
                        <p class="text-gray-600">Compare prices from verified sellers</p>
                    </div>

                    <template x-if="isLoading">
                        <div class="flex justify-center items-center py-12">
                            <div class="animate-spin rounded-full h-12 w-12 border-4 border-pink-200 border-t-pink-600"></div>
                        </div>
                    </template>

                    <template x-if="!isLoading">
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead>
                                    <tr class="bg-gray-50 rounded-t-xl">
                                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Seller</th>
                                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Price</th>
                                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Rating</th>
                                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Stock</th>
                                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <template x-for="(price, index) in prices" :key="price.id">
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-4">
                                                <div class="flex items-center">
                                                    <div class="w-8 h-8 bg-gradient-to-r from-pink-400 to-purple-500 rounded-full flex items-center justify-center mr-3">
                                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                    </div>
                                                    <div>
                                                        <div class="font-semibold text-gray-900" x-text="price.seller.name"></div>
                                                        <div class="text-sm text-gray-500" x-show="index === 0">✓ Verified</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-lg font-bold text-gray-900">₹<span x-text="price.price.toLocaleString()"></span></div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="flex items-center">
                                                    <svg class="w-4 h-4 text-yellow-400 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                                    </svg>
                                                    <span x-text="price.rating || '4.5'"></span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <span :class="{
                                                    'bg-green-100 text-green-800': price.stock_status === 'In Stock',
                                                    'bg-red-100 text-red-800': price.stock_status === 'Out of Stock'
                                                }" class="px-2 py-1 text-xs font-medium rounded-full" x-text="price.stock_status || 'In Stock'"></span>
                                            </td>
                                            <td class="px-6 py-4">
                                                <a :href="price.product_url || '#'" 
                                                   class="bg-gradient-to-r from-pink-500 to-purple-600 text-white px-4 py-2 rounded-xl text-sm font-medium hover:from-pink-600 hover:to-purple-700 transition-all">
                                                    Visit Store
                                                </a>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4 text-sm text-gray-500 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Prices updated 2 days ago
                        </div>
                    </template>
                </div>

                <!-- Perfume Details Tab -->
                <div x-show="activeTab === 'details'" x-transition>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Technical Details -->
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-6">Technical Details</h3>
                            <div class="space-y-4">
                                <div class="flex justify-between py-3 border-b border-gray-100">
                                    <span class="font-medium text-gray-600">Scent Type</span>
                                    <span class="text-gray-900">{{ $perfume->scent_type ?? 'Floral' }}</span>
                                </div>
                                <div class="flex justify-between py-3 border-b border-gray-100">
                                    <span class="font-medium text-gray-600">Concentration</span>
                                    <span class="text-gray-900">{{ $perfume->concentration ?? 'Eau de Parfum' }}</span>
                                </div>
                                <div class="flex justify-between py-3 border-b border-gray-100">
                                    <span class="font-medium text-gray-600">Longevity</span>
                                    <span class="text-gray-900">{{ $perfume->longevity ?? '6-8 hours' }}</span>
                                </div>
                                <div class="flex justify-between py-3 border-b border-gray-100">
                                    <span class="font-medium text-gray-600">Year Released</span>
                                    <span class="text-gray-900">{{ $perfume->launch_year ?? '2020' }}</span>
                                </div>
                                <div class="flex justify-between py-3">
                                    <span class="font-medium text-gray-600">Available Sizes</span>
                                    <span class="text-gray-900">30ml, 50ml, 100ml</span>
                                </div>
                            </div>
                        </div>

                        <!-- Scent Profile -->
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-6">Scent Profile</h3>
                            
                            @if($perfume->notes)
                                @php
                                    $notes = is_string($perfume->notes) ? json_decode($perfume->notes, true) : $perfume->notes;
                                @endphp
                                <div class="space-y-6">
                                    @if(is_array($notes))
                                        @foreach(['top' => 'Top Notes', 'middle' => 'Middle Notes', 'base' => 'Base Notes'] as $noteType => $label)
                                            @if(!empty($notes[$noteType]))
                                                <div>
                                                    <h4 class="font-semibold text-gray-800 mb-2">{{ $label }}</h4>
                                                    <div class="flex flex-wrap gap-2">
                                                        @foreach((is_array($notes[$noteType]) ? $notes[$noteType] : explode(', ', $notes[$noteType])) as $note)
                                                            <span class="px-3 py-1 bg-gradient-to-r from-pink-100 to-purple-100 text-pink-700 text-sm rounded-full border border-pink-200">
                                                                {{ trim($note) }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    @else
                                        <div>
                                            <h4 class="font-semibold text-gray-800 mb-2">Top Notes</h4>
                                            <div class="flex flex-wrap gap-2">
                                                <span class="px-3 py-1 bg-gradient-to-r from-pink-100 to-purple-100 text-pink-700 text-sm rounded-full border border-pink-200">Rose</span>
                                                <span class="px-3 py-1 bg-gradient-to-r from-pink-100 to-purple-100 text-pink-700 text-sm rounded-full border border-pink-200">Bergamot</span>
                                                <span class="px-3 py-1 bg-gradient-to-r from-pink-100 to-purple-100 text-pink-700 text-sm rounded-full border border-pink-200">Green Notes</span>
                                            </div>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-gray-800 mb-2">Middle Notes</h4>
                                            <div class="flex flex-wrap gap-2">
                                                <span class="px-3 py-1 bg-gradient-to-r from-orange-100 to-yellow-100 text-orange-700 text-sm rounded-full border border-orange-200">Jasmine</span>
                                                <span class="px-3 py-1 bg-gradient-to-r from-orange-100 to-yellow-100 text-orange-700 text-sm rounded-full border border-orange-200">Lily</span>
                                                <span class="px-3 py-1 bg-gradient-to-r from-orange-100 to-yellow-100 text-orange-700 text-sm rounded-full border border-orange-200">Ylang-Ylang</span>
                                            </div>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-gray-800 mb-2">Base Notes</h4>
                                            <div class="flex flex-wrap gap-2">
                                                <span class="px-3 py-1 bg-gradient-to-r from-green-100 to-teal-100 text-green-700 text-sm rounded-full border border-green-200">Sandalwood</span>
                                                <span class="px-3 py-1 bg-gradient-to-r from-green-100 to-teal-100 text-green-700 text-sm rounded-full border border-green-200">Vanilla</span>
                                                <span class="px-3 py-1 bg-gradient-to-r from-green-100 to-teal-100 text-green-700 text-sm rounded-full border border-green-200">Musk</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="space-y-6">
                                    <div>
                                        <h4 class="font-semibold text-gray-800 mb-2">Top Notes</h4>
                                        <div class="flex flex-wrap gap-2">
                                            <span class="px-3 py-1 bg-gradient-to-r from-pink-100 to-purple-100 text-pink-700 text-sm rounded-full border border-pink-200">Rose</span>
                                            <span class="px-3 py-1 bg-gradient-to-r from-pink-100 to-purple-100 text-pink-700 text-sm rounded-full border border-pink-200">Bergamot</span>
                                            <span class="px-3 py-1 bg-gradient-to-r from-pink-100 to-purple-100 text-pink-700 text-sm rounded-full border border-pink-200">Green Notes</span>
                                        </div>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-800 mb-2">Middle Notes</h4>
                                        <div class="flex flex-wrap gap-2">
                                            <span class="px-3 py-1 bg-gradient-to-r from-orange-100 to-yellow-100 text-orange-700 text-sm rounded-full border border-orange-200">Jasmine</span>
                                            <span class="px-3 py-1 bg-gradient-to-r from-orange-100 to-yellow-100 text-orange-700 text-sm rounded-full border border-orange-200">Lily</span>
                                            <span class="px-3 py-1 bg-gradient-to-r from-orange-100 to-yellow-100 text-orange-700 text-sm rounded-full border border-orange-200">Ylang-Ylang</span>
                                        </div>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-800 mb-2">Base Notes</h4>
                                        <div class="flex flex-wrap gap-2">
                                            <span class="px-3 py-1 bg-gradient-to-r from-green-100 to-teal-100 text-green-700 text-sm rounded-full border border-green-200">Sandalwood</span>
                                            <span class="px-3 py-1 bg-gradient-to-r from-green-100 to-teal-100 text-green-700 text-sm rounded-full border border-green-200">Vanilla</span>
                                            <span class="px-3 py-1 bg-gradient-to-r from-green-100 to-teal-100 text-green-700 text-sm rounded-full border border-green-200">Musk</span>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Reviews Tab -->
                <div x-show="activeTab === 'reviews'" x-transition
                     x-data="{ 
                        overallRating: 4.7,
                        totalReviews: 3,
                        ratingBreakdown: [
                            { stars: 5, count: 2 },
                            { stars: 4, count: 1 },
                            { stars: 3, count: 0 },
                            { stars: 2, count: 0 },
                            { stars: 1, count: 0 }
                        ],
                        reviews: [
                            { name: 'Sophie L.', rating: 5, text: 'My absolute favorite spring fragrance! Lasts all day and gets so many compliments.' },
                            { name: 'Michael R.', rating: 4, text: 'Bought this for my wife and she loves it. Great floral scent that\\'s not overpowering.' },
                            { name: 'Anna T.', rating: 5, text: 'Perfect balance of floral notes. Elegant and sophisticated without being old-fashioned.' }
                        ]
                    }">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <!-- Rating Summary -->
                        <div class="lg:col-span-1">
                            <h3 class="text-xl font-bold text-gray-900 mb-6">Customer Reviews</h3>
                            
                            <div class="text-center mb-6">
                                <div class="text-6xl font-bold text-pink-600 mb-2" x-text="overallRating"></div>
                                <div class="flex justify-center mb-2">
                                    <template x-for="i in 5">
                                        <svg class="w-6 h-6" :class="i <= overallRating ? 'text-yellow-400' : 'text-gray-300'" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                        </svg>
                                    </template>
                                </div>
                                <p class="text-gray-600">Based on <span x-text="totalReviews"></span> reviews</p>
                            </div>

                            <!-- Rating Breakdown -->
                            <div class="space-y-2">
                                <template x-for="item in ratingBreakdown" :key="item.stars">
                                    <div class="flex items-center gap-2 text-sm">
                                        <span x-text="item.stars"></span>
                                        <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                        </svg>
                                        <div class="flex-1 bg-gray-200 rounded-full h-2">
                                            <div class="bg-gradient-to-r from-pink-500 to-purple-600 h-2 rounded-full"
                                                 :style="`width: ${(item.count / totalReviews) * 100}%`"></div>
                                        </div>
                                        <span x-text="item.count" class="w-4"></span>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Individual Reviews -->
                        <div class="lg:col-span-2">
                            <div class="space-y-6">
                                <template x-for="review in reviews" :key="review.name">
                                    <div class="bg-gray-50 rounded-2xl p-6">
                                        <div class="flex items-center justify-between mb-3">
                                            <h4 class="font-semibold text-gray-900" x-text="review.name"></h4>
                                            <div class="flex">
                                                <template x-for="i in 5">
                                                    <svg class="w-4 h-4" :class="i <= review.rating ? 'text-yellow-400' : 'text-gray-300'" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                                    </svg>
                                                </template>
                                            </div>
                                        </div>
                                        <p class="text-gray-600" x-text="review.text"></p>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Similar Perfumes Section -->
        <div class="mt-12">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-3xl font-bold font-playfair">
                    <span class="bg-gradient-to-r from-pink-600 to-purple-600 bg-clip-text text-transparent">
                        Similar Perfumes
                    </span>
                </h2>
                <a href="{{ route('perfumes.index') }}" class="text-pink-600 hover:text-pink-700 font-medium flex items-center gap-1">
                    View All
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6"
                 x-data="{
                    similarPerfumes: [
                        { name: 'Aqua Marine', brand: 'Dior', season: 'Summer', price: 6499, sellers: 3 },
                        { name: 'Citrus Breeze', brand: 'Jo Malone', season: 'Summer', price: 7999, sellers: 2 },
                        { name: 'Fresh Linen', brand: 'Clean', season: 'Spring', price: 3999, sellers: 2 }
                    ]
                 }">
                <template x-for="perfume in similarPerfumes" :key="perfume.name">
                    <div class="bg-white/80 backdrop-blur-lg rounded-3xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-white/50 group">
                        <div class="relative h-48 bg-gray-100 overflow-hidden">
                            <img src="https://images.unsplash.com/photo-1541643600914-78b084683601?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80"
                                 alt="Perfume"
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            <div class="absolute top-3 left-3">
                                <span class="px-3 py-1 bg-gradient-to-r from-pink-500 to-purple-600 text-white text-xs font-medium rounded-full" x-text="perfume.season"></span>
                            </div>
                        </div>
                        <div class="p-5">
                            <p class="text-sm font-medium text-pink-600 mb-1" x-text="perfume.brand"></p>
                            <h3 class="text-lg font-semibold text-gray-800 mb-2" x-text="perfume.name"></h3>
                            <div class="flex items-center justify-between mb-4">
                                <span class="text-xl font-bold text-gray-900">₹<span x-text="perfume.price.toLocaleString()"></span></span>
                                <span class="text-sm text-gray-500"><span x-text="perfume.sellers"></span> sellers</span>
                            </div>
                            <button class="w-full bg-gradient-to-r from-pink-500 to-purple-600 text-white py-2 rounded-xl font-medium hover:from-pink-600 hover:to-purple-700 transition-all">
                                View Details
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Back to Perfumes -->
        <div class="mt-8 text-center">
            <a href="{{ route('perfumes.index') }}" class="inline-flex items-center gap-2 text-pink-600 hover:text-pink-700 font-medium transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to all perfumes
            </a>
        </div>
    </div>
</div>
@endsection