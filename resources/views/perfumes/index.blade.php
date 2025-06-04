@extends('layouts.app')

@section('title', 'Browse Perfumes')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-rose-50 via-pink-50 to-purple-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8 text-center">
            <h1 class="text-4xl lg:text-5xl font-bold font-playfair mb-4">
                <span class="bg-gradient-to-r from-pink-600 to-purple-600 bg-clip-text text-transparent">
                    Discover Perfumes
                </span>
            </h1>
            <p class="text-xl text-gray-600 font-light max-w-2xl mx-auto">
                Browse through our collection of exquisite fragrances and find the best deals
            </p>
        </div>

        <div x-data="{ 
                perfumes: [], 
                isLoading: true, 
                error: null,
                pagination: {},
                currentPage: 1,
                filters: {
                    priceRange: [0, 15000],
                    brands: [],
                    seasons: [],
                    sortBy: 'price_low_to_high'
                },
                showMobileFilters: false,
                fetchPerfumes(page = 1) {
                    this.isLoading = true;
                    this.error = null;
                    
                    // Build query parameters
                    const params = new URLSearchParams();
                    params.append('page', page);
                    params.append('min_price', this.filters.priceRange[0]);
                    params.append('max_price', this.filters.priceRange[1]);
                    params.append('sort', this.filters.sortBy);
                    
                    if (this.filters.brands.length > 0) {
                        this.filters.brands.forEach(brand => params.append('brands[]', brand));
                    }
                    
                    if (this.filters.seasons.length > 0) {
                        this.filters.seasons.forEach(season => params.append('seasons[]', season));
                    }
                    
                    fetch(`/api/v1/perfumes?${params.toString()}`)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            this.perfumes = data.data || data;
                            this.pagination = data.meta || {};
                            this.currentPage = data.meta?.current_page || 1;
                            this.isLoading = false;
                        })
                        .catch(err => {
                            this.error = err.message;
                            this.isLoading = false;
                            console.error('Error fetching perfumes:', err);
                        });
                },
                applyFilters() {
                    this.fetchPerfumes(1);
                },
                toggleBrand(brand) {
                    const index = this.filters.brands.indexOf(brand);
                    if (index > -1) {
                        this.filters.brands.splice(index, 1);
                    } else {
                        this.filters.brands.push(brand);
                    }
                    this.applyFilters();
                },
                toggleSeason(season) {
                    const index = this.filters.seasons.indexOf(season);
                    if (index > -1) {
                        this.filters.seasons.splice(index, 1);
                    } else {
                        this.filters.seasons.push(season);
                    }
                    this.applyFilters();
                },
                clearFilters() {
                    this.filters = {
                        priceRange: [0, 15000],
                        brands: [],
                        seasons: [],
                        sortBy: 'price_low_to_high'
                    };
                    this.applyFilters();
                }
            }" 
            x-init="fetchPerfumes()">

            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Sidebar Filters -->
                <div class="lg:w-80 flex-shrink-0">
                    <!-- Mobile Filter Toggle -->
                    <div class="lg:hidden mb-4">
                        <button @click="showMobileFilters = !showMobileFilters" 
                                class="w-full bg-white/80 backdrop-blur-lg rounded-2xl px-4 py-3 flex items-center justify-between border border-white/50 shadow-lg">
                            <span class="font-semibold text-gray-700">Filters</span>
                            <svg class="w-5 h-5 transition-transform" :class="{'rotate-180': showMobileFilters}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Filters Panel -->
                    <div class="bg-white/80 backdrop-blur-lg rounded-3xl p-6 shadow-xl border border-white/50 sticky top-24"
                         :class="{'hidden lg:block': !showMobileFilters, 'block': showMobileFilters}">
                        
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-bold text-gray-800">Filters</h3>
                            <button @click="clearFilters()" class="text-sm text-pink-600 hover:text-pink-700 font-medium">
                                Clear All
                            </button>
                        </div>

                        <!-- Price Range -->
                        <div class="mb-8">
                            <h4 class="font-semibold text-gray-700 mb-4">Price Range</h4>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between text-sm text-gray-600">
                                    <span>₹<span x-text="filters.priceRange[0]"></span></span>
                                    <span>₹<span x-text="filters.priceRange[1]"></span></span>
                                </div>
                                <div class="relative">
                                    <input type="range" x-model="filters.priceRange[0]" min="0" max="15000" step="500" 
                                           @input="applyFilters()"
                                           class="absolute w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer slider-thumb">
                                    <input type="range" x-model="filters.priceRange[1]" min="0" max="15000" step="500" 
                                           @input="applyFilters()"
                                           class="absolute w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer slider-thumb">
                                </div>
                            </div>
                        </div>

                        <!-- Brand Filter -->
                        <div class="mb-8">
                            <h4 class="font-semibold text-gray-700 mb-4">Brand</h4>
                            <div class="space-y-3">
                                <template x-for="brand in ['Chanel', 'Dior', 'Tom Ford', 'Jo Malone', 'Gucci', 'Yves Saint Laurent', 'Clean', 'Hermès']">
                                    <label class="flex items-center group cursor-pointer">
                                        <input type="checkbox" 
                                               :checked="filters.brands.includes(brand)"
                                               @change="toggleBrand(brand)"
                                               class="w-4 h-4 text-pink-600 bg-white border-gray-300 rounded focus:ring-pink-500 focus:ring-2">
                                        <span class="ml-3 text-sm text-gray-700 group-hover:text-pink-600 transition-colors" x-text="brand"></span>
                                    </label>
                                </template>
                            </div>
                        </div>

                        <!-- Season Filter -->
                        <div class="mb-6">
                            <h4 class="font-semibold text-gray-700 mb-4">Season</h4>
                            <div class="grid grid-cols-2 gap-3">
                                <template x-for="season in ['Spring', 'Summer', 'Fall', 'Winter']">
                                    <label class="flex items-center group cursor-pointer">
                                        <input type="checkbox" 
                                               :checked="filters.seasons.includes(season)"
                                               @change="toggleSeason(season)"
                                               class="w-4 h-4 text-pink-600 bg-white border-gray-300 rounded focus:ring-pink-500 focus:ring-2">
                                        <span class="ml-2 text-sm text-gray-700 group-hover:text-pink-600 transition-colors" x-text="season"></span>
                                    </label>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="flex-1">
                    <!-- Results Header -->
                    <div class="bg-white/60 backdrop-blur-lg rounded-2xl p-6 mb-8 border border-white/30 shadow-lg">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div>
                                <h2 class="text-2xl font-bold text-gray-800">
                                    <span x-text="!isLoading && perfumes ? perfumes.length : '0'"></span> Perfumes Found
                                </h2>
                                <p class="text-gray-600 mt-1">Discover your perfect scent</p>
                            </div>
                            
                            <div class="flex items-center gap-3">
                                <span class="text-sm font-medium text-gray-700">Sort by:</span>
                                <select x-model="filters.sortBy" @change="applyFilters()" 
                                        class="bg-white/90 border border-gray-200 rounded-xl px-4 py-2 text-sm focus:border-pink-400 focus:ring-2 focus:ring-pink-200 transition-all">
                                    <option value="price_low_to_high">Price: Low to High</option>
                                    <option value="price_high_to_low">Price: High to Low</option>
                                    <option value="name_asc">Name: A-Z</option>
                                    <option value="name_desc">Name: Z-A</option>
                                    <option value="brand_asc">Brand: A-Z</option>
                                    <option value="newest">Newest First</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Loading State -->
                    <template x-if="isLoading">
                        <div class="flex justify-center items-center py-20">
                            <div class="animate-spin rounded-full h-16 w-16 border-4 border-pink-200 border-t-pink-600"></div>
                        </div>
                    </template>

                    <!-- Error State -->
                    <template x-if="error">
                        <div class="text-center py-20">
                            <div class="bg-red-50 border border-red-200 rounded-2xl p-8 max-w-md mx-auto">
                                <svg class="w-12 h-12 mx-auto mb-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <h3 class="text-lg font-semibold text-red-800 mb-2">Oops! Something went wrong</h3>
                                <p class="text-red-600" x-text="error"></p>
                            </div>
                        </div>
                    </template>

                    <!-- Empty State -->
                    <template x-if="!isLoading && !error && perfumes.length === 0">
                        <div class="text-center py-20">
                            <div class="bg-gray-50 border border-gray-200 rounded-2xl p-8 max-w-md mx-auto">
                                <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                <h3 class="text-lg font-semibold text-gray-700 mb-2">No perfumes found</h3>
                                <p class="text-gray-600 mb-4">Try adjusting your filters or search criteria</p>
                                <button @click="clearFilters()" class="bg-gradient-to-r from-pink-500 to-purple-600 text-white px-6 py-2 rounded-xl font-medium hover:from-pink-600 hover:to-purple-700 transition-all">
                                    Clear Filters
                                </button>
                            </div>
                        </div>
                    </template>

                    <!-- Perfumes Grid -->
                    <template x-if="!isLoading && !error && perfumes.length > 0">
                        <div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                                <template x-for="perfume in perfumes" :key="perfume.id">
                                    <div class="group bg-white/80 backdrop-blur-lg rounded-3xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-white/50">
                                        <!-- Image Container -->
                                        <div class="relative overflow-hidden h-48">
                                            <img :src="perfume.image_url || 'https://images.unsplash.com/photo-1541643600914-78b084683601?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80'" 
                                                 :alt="perfume.name" 
                                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                            
                                            <!-- Season Badge -->
                                            <div class="absolute top-3 left-3">
                                                <span class="px-3 py-1 bg-gradient-to-r from-pink-500 to-purple-600 text-white text-xs font-medium rounded-full">
                                                    <span x-text="perfume.season || 'All Season'"></span>
                                                </span>
                                            </div>
                                            
                                            <!-- Wishlist Button -->
                                            <button class="absolute top-3 right-3 w-8 h-8 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all transform translate-y-2 group-hover:translate-y-0 hover:bg-pink-500 hover:text-white">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                                </svg>
                                            </button>
                                        </div>
                                        
                                        <!-- Content -->
                                        <div class="p-5">
                                            <div class="mb-3">
                                                <p class="text-sm font-medium text-pink-600 mb-1" x-text="perfume.brand"></p>
                                                <h3 class="text-lg font-semibold text-gray-800 line-clamp-1" x-text="perfume.name"></h3>
                                            </div>
                                            
                                            <!-- Price Display -->
                                            <div class="mb-4">
                                                <div class="flex items-center justify-between mb-2">
                                                    <span class="text-2xl font-bold text-gray-900">
                                                        ₹<span x-text="perfume.min_price || '4,999'"></span>
                                                    </span>
                                                    <span class="text-sm text-gray-500">
                                                        <span x-text="perfume.seller_count || '3'"></span> sellers
                                                    </span>
                                                </div>
                                                
                                                <!-- Seller Prices -->
                                                <div class="space-y-1 text-xs">
                                                    <div class="flex justify-between text-gray-600">
                                                        <span>Luxury Scents</span>
                                                        <span>₹<span x-text="perfume.min_price || '4,999'"></span></span>
                                                    </div>
                                                    <div class="flex justify-between text-gray-600">
                                                        <span>Perfume World</span>
                                                        <span>₹<span x-text="(parseInt(perfume.min_price || '4999') + 300).toLocaleString()"></span></span>
                                                    </div>
                                                    <template x-if="(perfume.seller_count || 3) > 2">
                                                        <div class="text-pink-600 font-medium">
                                                            +<span x-text="(perfume.seller_count || 3) - 2"></span> more sellers
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                            
                                            <!-- Action Button -->
                                            <a :href="'/perfumes/' + perfume.id" 
                                               class="block w-full bg-gradient-to-r from-pink-500 to-purple-600 text-white py-3 rounded-2xl font-semibold text-center hover:from-pink-600 hover:to-purple-700 transition-all transform hover:scale-105 shadow-md">
                                                View Details
                                            </a>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <!-- Pagination -->
                            <div class="mt-12" x-show="pagination && pagination.last_page > 1">
                                <nav class="flex items-center justify-center">
                                    <div class="flex items-center space-x-2">
                                        <!-- Previous Button -->
                                        <button @click="fetchPerfumes(currentPage - 1)"
                                                :disabled="currentPage === 1"
                                                class="px-4 py-2 text-sm font-medium text-gray-500 bg-white/80 backdrop-blur-lg border border-gray-200 rounded-xl hover:bg-white hover:text-pink-600 disabled:opacity-50 disabled:cursor-not-allowed transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                            </svg>
                                        </button>
                                        
                                        <!-- Page Numbers -->
                                        <template x-for="page in Array.from({length: Math.min(5, pagination.last_page)}, (_, i) => i + Math.max(1, currentPage - 2))">
                                            <button @click="fetchPerfumes(page)"
                                                    :class="{
                                                        'bg-gradient-to-r from-pink-500 to-purple-600 text-white': page === currentPage,
                                                        'bg-white/80 text-gray-700 hover:bg-white hover:text-pink-600': page !== currentPage
                                                    }"
                                                    class="px-4 py-2 text-sm font-medium backdrop-blur-lg border border-gray-200 rounded-xl transition-all"
                                                    x-text="page">
                                            </button>
                                        </template>
                                        
                                        <!-- Next Button -->
                                        <button @click="fetchPerfumes(currentPage + 1)"
                                                :disabled="currentPage === pagination.last_page"
                                                class="px-4 py-2 text-sm font-medium text-gray-500 bg-white/80 backdrop-blur-lg border border-gray-200 rounded-xl hover:bg-white hover:text-pink-600 disabled:opacity-50 disabled:cursor-not-allowed transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </button>
                                    </div>
                                </nav>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .slider-thumb::-webkit-slider-thumb {
        appearance: none;
        height: 20px;
        width: 20px;
        border-radius: 50%;
        background: linear-gradient(45deg, #ec4899, #9333ea);
        cursor: pointer;
        border: 2px solid white;
        box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    }
    
    .slider-thumb::-moz-range-thumb {
        height: 20px;
        width: 20px;
        border-radius: 50%;
        background: linear-gradient(45deg, #ec4899, #9333ea);
        cursor: pointer;
        border: 2px solid white;
        box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    }
    
    .line-clamp-1 {
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endsection