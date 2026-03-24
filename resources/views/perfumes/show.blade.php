@extends('layouts.app')

@section('title', $perfume->name)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8"
     x-data="{
        prices: [],
        allPrices: [],
        sizes: [],
        selectedSize: null,
        isLoading: true,
        error: null,
        activeTab: 'sellers',
        authToken: localStorage.getItem('auth_token'),
        inWishlist: false,
        hasAlert: false,
        existingAlert: null,
        showAlertModal: false,
        targetPrice: '',
        alertSize: '',
        alertMessage: '',

        get minPrice() {
            const inStock = this.prices.filter(p => p.stock_status === 'In Stock');
            if (!inStock.length) return this.prices.length ? Math.min(...this.prices.map(p => parseFloat(p.price))) : 0;
            return Math.min(...inStock.map(p => parseFloat(p.price)));
        },
        get bestSeller() {
            if (!this.prices.length) return '';
            const sorted = [...this.prices].filter(p => p.stock_status === 'In Stock').sort((a, b) => a.price - b.price);
            return sorted.length ? sorted[0].seller.name : this.prices[0].seller.name;
        },
        get sellerCount() {
            return new Set(this.prices.map(p => p.seller.id)).size;
        },

        async init() {
            await this.fetchPrices();
            this.checkWishlistAndAlert();
        },

        async fetchPrices() {
            this.isLoading = true;
            this.error = null;
            try {
                const res = await fetch('/api/v1/perfumes/{{ $perfume->id }}/prices?per_page=100');
                if (res.ok) {
                    const data = await res.json();
                    this.allPrices = data.data || [];
                } else {
                    this.allPrices = [];
                    this.error = 'Failed to load prices. Please try again.';
                }
            } catch (e) {
                this.allPrices = [];
                this.error = 'Could not connect to server. Please check your connection.';
            }
            // Extract unique sizes
            this.sizes = [...new Set(this.allPrices.map(p => p.size_ml).filter(Boolean))].sort((a, b) => a - b);
            // Default to first available size, or show all
            if (this.sizes.length > 0) {
                this.selectedSize = this.sizes[0];
            }
            this.filterBySize();
            this.isLoading = false;
        },

        filterBySize() {
            if (this.selectedSize) {
                this.prices = this.allPrices.filter(p => p.size_ml === this.selectedSize);
            } else {
                this.prices = [...this.allPrices];
            }
        },

        selectSize(size) {
            this.selectedSize = size;
            this.filterBySize();
        },

        showAllSizes() {
            this.selectedSize = null;
            this.prices = [...this.allPrices];
        },

        async checkWishlistAndAlert() {
            if (!this.authToken) return;
            try {
                const res = await fetch('/api/v1/wishlist/check?perfume_id={{ $perfume->id }}', {
                    headers: { 'Authorization': `Bearer ${this.authToken}` }
                });
                const data = await res.json();
                this.inWishlist = data.in_wishlist;
            } catch (e) {}
            try {
                const res = await fetch('/api/v1/price-alerts/check?perfume_id={{ $perfume->id }}', {
                    headers: { 'Authorization': `Bearer ${this.authToken}` }
                });
                const data = await res.json();
                this.hasAlert = data.has_alert;
                this.existingAlert = data.alert;
                if (this.existingAlert) this.targetPrice = this.existingAlert.target_price;
            } catch (e) {}
        },

        async toggleWishlist() {
            if (!this.authToken) { window.location.href = '/login'; return; }
            try {
                const res = await fetch('/api/v1/wishlist/toggle', {
                    method: 'POST',
                    headers: { 'Authorization': `Bearer ${this.authToken}`, 'Content-Type': 'application/json' },
                    body: JSON.stringify({ perfume_id: {{ $perfume->id }} })
                });
                const data = await res.json();
                this.inWishlist = data.in_wishlist;
            } catch (e) {}
        },

        async saveAlert() {
            if (!this.authToken) { window.location.href = '/login'; return; }
            if (!this.targetPrice || this.targetPrice <= 0) { this.alertMessage = 'Please enter a valid target price'; return; }
            try {
                const res = await fetch('/api/v1/price-alerts', {
                    method: 'POST',
                    headers: { 'Authorization': `Bearer ${this.authToken}`, 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        perfume_id: {{ $perfume->id }},
                        size_ml: this.alertSize ? parseInt(this.alertSize) : null,
                        target_price: this.targetPrice
                    })
                });
                const data = await res.json();
                if (res.ok) {
                    this.hasAlert = true;
                    this.existingAlert = data.data;
                    this.showAlertModal = false;
                    this.alertMessage = '';
                } else {
                    this.alertMessage = data.message || 'Failed to create alert';
                }
            } catch (e) { this.alertMessage = 'An error occurred'; }
        }
     }">

    <!-- Main Product Section -->
    <div class="bg-white/80 backdrop-blur-lg rounded-3xl shadow-xl border border-white/50 overflow-hidden mb-8">
        <div class="lg:flex">
            <!-- Product Image -->
            <div class="lg:w-1/2 relative">
                <div class="aspect-square relative overflow-hidden flex items-center justify-center bg-gradient-to-br from-teal-50 to-cyan-50">
                    @if($perfume->image_url)
                    <img class="w-full h-full object-cover"
                         src="{{ $perfume->image_url }}"
                         alt="{{ $perfume->name }}">
                    @else
                    <span class="text-8xl font-bold text-teal-200 font-playfair">{{ substr($perfume->brand, 0, 1) }}</span>
                    @endif
                </div>
            </div>

            <!-- Product Details -->
            <div class="lg:w-1/2 p-8">
                <!-- Brand -->
                <div class="flex items-center gap-3 mb-4">
                    @if($perfume->concentration)
                    <span class="px-3 py-1 bg-gradient-to-r from-teal-500 to-cyan-600 text-white text-sm font-medium rounded-full">
                        {{ $perfume->concentration }}
                    </span>
                    @endif
                    <span class="text-gray-600 font-medium">{{ $perfume->brand }}</span>
                </div>

                <!-- Product Name -->
                <h1 class="text-3xl lg:text-4xl font-bold font-playfair text-gray-900 mb-4">{{ $perfume->name }}</h1>

                <!-- Gender & Year -->
                <div class="flex items-center gap-4 mb-6 text-sm text-gray-500">
                    @if($perfume->gender_affinity)
                        <span>{{ $perfume->gender_affinity }}</span>
                    @endif
                    @if($perfume->launch_year)
                        <span>Launched {{ $perfume->launch_year }}</span>
                    @endif
                </div>

                <!-- Price Section (Dynamic) -->
                <div class="mb-6">
                    <template x-if="!isLoading && prices.length > 0">
                        <div>
                            <div class="flex items-baseline gap-2 mb-2">
                                <span class="text-3xl font-bold text-gray-900">₹<span x-text="minPrice.toLocaleString()"></span></span>
                                <span class="text-gray-600">lowest from <span x-text="sellerCount"></span> <span x-text="sellerCount === 1 ? 'seller' : 'sellers'"></span></span>
                            </div>
                            <div class="flex items-center gap-2 text-teal-600">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>Best price at <span x-text="bestSeller" class="font-semibold"></span></span>
                            </div>
                        </div>
                    </template>
                    <template x-if="!isLoading && prices.length === 0">
                        <p class="text-gray-500">No prices available<span x-show="selectedSize"> for this size</span></p>
                    </template>
                    <template x-if="isLoading">
                        <div class="animate-pulse h-12 bg-gray-200 rounded-xl w-48"></div>
                    </template>
                </div>

                <!-- Description -->
                @if($perfume->description)
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Description</h3>
                    <p class="text-gray-600 leading-relaxed">{{ $perfume->description }}</p>
                </div>
                @endif

                <!-- Size Selector (Dynamic from API data) -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">Select Size</h3>
                    <template x-if="isLoading">
                        <div class="animate-pulse flex gap-3">
                            <div class="h-10 w-16 bg-gray-200 rounded-xl"></div>
                            <div class="h-10 w-16 bg-gray-200 rounded-xl"></div>
                        </div>
                    </template>
                    <template x-if="!isLoading && sizes.length > 0">
                        <div class="flex flex-wrap gap-3">
                            <button @click="showAllSizes()"
                                    :class="selectedSize === null ? 'border-teal-500 bg-teal-50 text-teal-700' : 'border-gray-200 bg-white text-gray-700 hover:border-teal-300'"
                                    class="px-4 py-2 border rounded-xl font-medium transition-all">
                                All
                            </button>
                            <template x-for="size in sizes" :key="size">
                                <button @click="selectSize(size)"
                                        :class="selectedSize === size ? 'border-teal-500 bg-teal-50 text-teal-700' : 'border-gray-200 bg-white text-gray-700 hover:border-teal-300'"
                                        class="px-4 py-2 border rounded-xl font-medium transition-all"
                                        x-text="size + 'ml'">
                                </button>
                            </template>
                        </div>
                    </template>
                    <template x-if="!isLoading && sizes.length === 0">
                        <p class="text-gray-500 text-sm">No size data available</p>
                    </template>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-3">
                    <button @click="toggleWishlist()"
                            :class="inWishlist ? 'bg-teal-600 text-white border-teal-600' : 'bg-white/90 border-gray-200 text-gray-700'"
                            class="flex-1 border py-4 px-6 rounded-2xl font-semibold hover:shadow-lg transition-all flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" :fill="inWishlist ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                        <span x-text="inWishlist ? 'In Wishlist' : 'Add to Wishlist'"></span>
                    </button>

                    <button @click="authToken ? showAlertModal = true : window.location.href = '/login'"
                            :class="hasAlert ? 'bg-amber-500 text-white border-amber-500' : 'bg-white/90 border-gray-200 text-gray-700'"
                            class="flex-1 border py-4 px-6 rounded-2xl font-semibold hover:shadow-lg transition-all flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <span x-text="hasAlert ? 'Alert Set' : 'Set Price Alert'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Price Alert Modal -->
    <div x-show="showAlertModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
         @click.self="showAlertModal = false">
        <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4 shadow-2xl">
            <h3 class="text-xl font-bold text-gray-800 mb-2">Set Price Alert</h3>
            <p class="text-gray-500 text-sm mb-6">We'll notify you when the price drops below your target.</p>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Size</label>
                <select x-model="alertSize"
                        class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-teal-400">
                    <option value="">Any Size</option>
                    <template x-for="size in sizes" :key="size">
                        <option :value="size" x-text="size + 'ml'"></option>
                    </template>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Target Price</label>
                <input type="number" x-model="targetPrice" placeholder="Enter target price..."
                       class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-teal-400">
            </div>

            <template x-if="alertMessage">
                <div class="mb-4 text-red-500 text-sm" x-text="alertMessage"></div>
            </template>

            <div class="flex gap-4">
                <button @click="saveAlert()"
                        class="flex-1 bg-gradient-to-r from-teal-500 to-cyan-600 text-white py-3 rounded-xl font-semibold hover:from-teal-600 hover:to-cyan-700 transition-all">
                    Save Alert
                </button>
                <button @click="showAlertModal = false"
                        class="flex-1 bg-gray-200 text-gray-700 py-3 rounded-xl font-semibold">
                    Cancel
                </button>
            </div>
        </div>
    </div>

    <!-- Tabbed Content Section -->
    <div class="bg-white/80 backdrop-blur-lg rounded-3xl shadow-xl border border-white/50 overflow-hidden">
        <!-- Tab Navigation -->
        <div class="border-b border-gray-200">
            <nav class="flex px-8">
                <button @click="activeTab = 'sellers'"
                        :class="activeTab === 'sellers' ? 'border-teal-500 text-teal-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                        class="py-4 px-6 border-b-2 font-medium text-sm transition-colors">
                    All Sellers
                </button>
                <button @click="activeTab = 'details'"
                        :class="activeTab === 'details' ? 'border-teal-500 text-teal-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                        class="py-4 px-6 border-b-2 font-medium text-sm transition-colors">
                    Perfume Details
                </button>
            </nav>
        </div>

        <!-- Tab Content -->
        <div class="p-8">
            <!-- All Sellers Tab -->
            <div x-show="activeTab === 'sellers'" x-transition>
                <div class="mb-6">
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Price Comparison <span x-show="selectedSize" class="text-teal-600" x-text="'(' + selectedSize + 'ml)'"></span></h3>
                    <p class="text-gray-600"><span x-text="prices.length"></span> <span x-text="prices.length === 1 ? 'offer' : 'offers'"></span> found</p>
                </div>

                <template x-if="isLoading">
                    <div class="flex justify-center items-center py-12">
                        <div class="animate-spin rounded-full h-12 w-12 border-4 border-teal-200 border-t-teal-600"></div>
                    </div>
                </template>

                <template x-if="!isLoading && error">
                    <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-center">
                        <p class="text-red-600 text-sm" x-text="error"></p>
                        <button @click="fetchPrices()" class="mt-2 text-sm text-red-700 font-semibold hover:underline">Try Again</button>
                    </div>
                </template>

                <template x-if="!isLoading && !error && prices.length > 0">
                    <div class="space-y-4">
                        <template x-for="(price, index) in prices.sort((a, b) => a.price - b.price)" :key="price.id">
                            <div class="flex items-center justify-between p-5 rounded-2xl border transition-all"
                                 :class="index === 0 ? 'border-teal-200 bg-teal-50/50' : 'border-gray-100 hover:border-gray-200'">
                                <div class="flex items-center gap-4">
                                    <!-- Seller icon -->
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm"
                                         :class="index === 0 ? 'bg-gradient-to-r from-teal-500 to-cyan-600' : 'bg-gray-400'">
                                        <span x-text="price.seller.name.charAt(0)"></span>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-gray-900" x-text="price.seller.name"></div>
                                        <div class="flex items-center gap-2 text-sm text-gray-500">
                                            <span x-show="price.size_ml" x-text="price.size_ml + 'ml'"></span>
                                            <span x-show="price.item_type" x-text="'· ' + price.item_type"></span>
                                            <span x-show="index === 0" class="text-teal-600 font-medium">· Best Price</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-6">
                                    <!-- Stock status -->
                                    <span :class="price.stock_status === 'In Stock' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                          class="px-2 py-1 text-xs font-medium rounded-full hidden sm:inline-block"
                                          x-text="price.stock_status || 'In Stock'"></span>

                                    <!-- Price -->
                                    <div class="text-right">
                                        <div class="text-xl font-bold text-gray-900">₹<span x-text="parseFloat(price.price).toLocaleString()"></span></div>
                                        <div x-show="price.offer_details" class="text-xs text-amber-600" x-text="price.offer_details"></div>
                                    </div>

                                    <!-- Visit Store -->
                                    <a :href="price.product_url || '#'"
                                       target="_blank"
                                       class="bg-gradient-to-r from-teal-500 to-cyan-600 text-white px-5 py-2.5 rounded-xl text-sm font-medium hover:from-teal-600 hover:to-cyan-700 transition-all hidden sm:inline-block">
                                        Visit Store
                                    </a>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>

                <template x-if="!isLoading && !error && prices.length === 0">
                    <div class="text-center py-12 text-gray-500">
                        <p>No prices available<span x-show="selectedSize"> for <span x-text="selectedSize + 'ml'"></span></span>. Try a different size.</p>
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
                            @if($perfume->concentration)
                            <div class="flex justify-between py-3 border-b border-gray-100">
                                <span class="font-medium text-gray-600">Concentration</span>
                                <span class="text-gray-900">{{ $perfume->concentration }}</span>
                            </div>
                            @endif
                            @if($perfume->gender_affinity)
                            <div class="flex justify-between py-3 border-b border-gray-100">
                                <span class="font-medium text-gray-600">Gender</span>
                                <span class="text-gray-900">{{ $perfume->gender_affinity }}</span>
                            </div>
                            @endif
                            @if($perfume->launch_year)
                            <div class="flex justify-between py-3 border-b border-gray-100">
                                <span class="font-medium text-gray-600">Year Released</span>
                                <span class="text-gray-900">{{ $perfume->launch_year }}</span>
                            </div>
                            @endif
                            <div class="flex justify-between py-3 border-b border-gray-100" x-show="sizes.length > 0">
                                <span class="font-medium text-gray-600">Available Sizes</span>
                                <span class="text-gray-900" x-text="sizes.map(s => s + 'ml').join(', ')"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Scent Profile -->
                    @if($perfume->notes)
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 mb-6">Scent Profile</h3>
                        @php
                            $notes = is_string($perfume->notes) ? json_decode($perfume->notes, true) : $perfume->notes;
                        @endphp
                        @if(is_array($notes))
                            <div class="space-y-6">
                                @foreach(['top' => 'Top Notes', 'middle' => 'Middle Notes', 'base' => 'Base Notes'] as $noteType => $label)
                                    @if(!empty($notes[$noteType]))
                                    <div>
                                        <h4 class="font-semibold text-gray-800 mb-2">{{ $label }}</h4>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach((is_array($notes[$noteType]) ? $notes[$noteType] : explode(', ', $notes[$noteType])) as $note)
                                                <span class="px-3 py-1 bg-teal-50 text-teal-700 text-sm rounded-full border border-teal-200">
                                                    {{ trim($note) }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif
                                @endforeach
                                {{-- If notes is a flat array (not keyed by top/middle/base) --}}
                                @if(!isset($notes['top']) && !isset($notes['middle']) && !isset($notes['base']))
                                    <div>
                                        <h4 class="font-semibold text-gray-800 mb-2">Notes</h4>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($notes as $note)
                                                <span class="px-3 py-1 bg-teal-50 text-teal-700 text-sm rounded-full border border-teal-200">
                                                    {{ trim(is_string($note) ? $note : json_encode($note)) }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
