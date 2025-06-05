@extends('layouts.app')

@section('title', 'Browse Perfumes')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-rose-50 via-pink-50 to-purple-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Header -->
        <div class="mb-16 text-center">
            <h1 class="text-5xl lg:text-6xl font-bold font-playfair mb-6">
                <span class="bg-gradient-to-r from-pink-600 to-purple-600 bg-clip-text text-transparent">
                    Discover Perfumes
                </span>
            </h1>
            <p class="text-xl text-gray-600 font-light max-w-3xl mx-auto leading-relaxed">
                Browse through our curated collection of exquisite fragrances and discover the best deals from premium retailers
            </p>
            <div class="mt-8 w-24 h-1 bg-gradient-to-r from-pink-400 to-purple-600 rounded-full mx-auto"></div>
        </div>

        <div x-data="{ 
                perfumes: [], 
                isLoading: true, 
                error: null,
                pagination: {},
                currentPage: 1,
                searchTerm: '', 
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
                    if (this.searchTerm.trim() !== '') {
                        params.append('search', this.searchTerm.trim());
                    }
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
                    this.searchTerm = '';
                    this.applyFilters();
                }
            }"
            x-init="fetchPerfumes()">

            <div class="flex flex-col lg:flex-row gap-10 lg:gap-12">
                <!-- Sidebar Filters -->
                <div class="lg:w-80 flex-shrink-0">
                    <!-- Mobile Filter Toggle -->
                    <div class="lg:hidden mb-6">
                        <button @click="showMobileFilters = !showMobileFilters" 
                                class="w-full bg-white/90 backdrop-blur-xl rounded-3xl px-6 py-4 flex items-center justify-between border border-white/60 shadow-xl hover:shadow-2xl transition-all transform hover:scale-[1.02]">
                            <span class="font-bold text-gray-800 text-lg">Filters & Search</span>
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 bg-gradient-to-r from-pink-500 to-purple-600 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white transition-transform" :class="{'rotate-180': showMobileFilters}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </div>
                            </div>
                        </button>
                    </div>

                    <!-- Filters Panel -->
                    <div class="bg-white/90 backdrop-blur-xl rounded-3xl p-8 shadow-2xl border border-white/60 sticky top-24 space-y-8"
                         :class="{'hidden lg:block': !showMobileFilters, 'block': showMobileFilters}">
                        
                        <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                            <h3 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                                <div class="w-8 h-8 bg-gradient-to-r from-pink-500 to-purple-600 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"/>
                                    </svg>
                                </div>
                                Filters
                            </h3>
                            <button @click="clearFilters()" class="text-sm text-pink-600 hover:text-pink-700 font-semibold bg-pink-50 hover:bg-pink-100 px-4 py-2 rounded-full transition-all">
                                Clear All
                            </button>
                        </div>

                        <!-- Price Range -->
                        <div class="space-y-6">
                            <div class="bg-gradient-to-r from-pink-50 to-purple-50 rounded-2xl p-6">
                                <h4 class="font-bold text-gray-800 mb-6 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                    </svg>
                                    Price Range
                                </h4>
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between text-sm font-semibold text-gray-700 bg-white/80 rounded-xl px-4 py-2">
                                        <span class="bg-gradient-to-r from-pink-600 to-purple-600 bg-clip-text text-transparent">₹<span x-text="filters.priceRange[0].toLocaleString()"></span></span>
                                        <span class="text-gray-400">to</span>
                                        <span class="bg-gradient-to-r from-pink-600 to-purple-600 bg-clip-text text-transparent">₹<span x-text="filters.priceRange[1].toLocaleString()"></span></span>
                                    </div>
                                    <div class="relative py-4">
                                        <input type="range" x-model="filters.priceRange[0]" min="0" max="15000" step="500" 
                                               @input="applyFilters()"
                                               class="absolute w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer slider-thumb">
                                        <input type="range" x-model="filters.priceRange[1]" min="0" max="15000" step="500" 
                                               @input="applyFilters()"
                                               class="absolute w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer slider-thumb">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Brand Filter -->
                        <div class="space-y-6">
                            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl p-6">
                                <h4 class="font-bold text-gray-800 mb-6 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                    </svg>
                                    Premium Brands
                                </h4>
                                <div class="space-y-4">
                                    <template x-for="brand in ['Chanel', 'Dior', 'Tom Ford', 'Jo Malone', 'Gucci', 'Yves Saint Laurent', 'Clean', 'Hermès']">
                                        <label class="flex items-center group cursor-pointer bg-white/80 hover:bg-white rounded-xl p-3 transition-all hover:shadow-md">
                                            <div class="relative">
                                                <input type="checkbox" 
                                                       :checked="filters.brands.includes(brand)"
                                                       @change="toggleBrand(brand)"
                                                       class="w-5 h-5 text-pink-600 bg-white border-2 border-gray-300 rounded-lg focus:ring-pink-500 focus:ring-2 transition-all">
                                                <div class="absolute inset-0 bg-gradient-to-r from-pink-500 to-purple-600 rounded-lg opacity-0 group-hover:opacity-20 transition-opacity"></div>
                                            </div>
                                            <span class="ml-4 text-sm font-medium text-gray-700 group-hover:text-pink-600 transition-colors" x-text="brand"></span>
                                        </label>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- Season Filter -->
                        <div class="space-y-6">
                            <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-2xl p-6">
                                <h4 class="font-bold text-gray-800 mb-6 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                    Season Collection
                                </h4>
                                <div class="grid grid-cols-2 gap-3">
                                    <template x-for="season in ['Spring', 'Summer', 'Fall', 'Winter']">
                                        <label class="flex items-center group cursor-pointer bg-white/80 hover:bg-white rounded-xl p-3 transition-all hover:shadow-md">
                                            <div class="relative">
                                                <input type="checkbox" 
                                                       :checked="filters.seasons.includes(season)"
                                                       @change="toggleSeason(season)"
                                                       class="w-5 h-5 text-green-600 bg-white border-2 border-gray-300 rounded-lg focus:ring-green-500 focus:ring-2 transition-all">
                                                <div class="absolute inset-0 bg-gradient-to-r from-green-500 to-emerald-600 rounded-lg opacity-0 group-hover:opacity-20 transition-opacity"></div>
                                            </div>
                                            <span class="ml-2 text-xs font-medium text-gray-700 group-hover:text-green-600 transition-colors" x-text="season"></span>
                                        </label>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="flex-1 space-y-10">
                    <!-- Results Header -->
                    <div class="bg-white/70 backdrop-blur-xl rounded-3xl p-8 border border-white/40 shadow-2xl">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-8">
                            <!-- Search Input -->
                            <div class="relative flex-grow md:flex-grow-0 md:w-80">
                                <input type="text"
                                       x-model.debounce.500ms="searchTerm"
                                       @input="fetchPerfumes(1)"
                                       placeholder="Search perfumes or brands..."
                                       class="w-full bg-white/95 border-2 border-gray-200 rounded-2xl px-6 py-4 pl-14 text-sm focus:border-pink-400 focus:ring-4 focus:ring-pink-200 transition-all shadow-inner"
                                >
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <div class="w-8 h-8 bg-gradient-to-r from-pink-500 to-purple-600 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6 flex-grow">
                                <div class="text-center sm:text-left">
                                    <h2 class="text-3xl font-bold text-gray-800 mb-2">
                                        <span class="bg-gradient-to-r from-pink-600 to-purple-600 bg-clip-text text-transparent" x-text="pagination.total || '0'"></span> 
                                        <span class="text-gray-700">Perfumes</span>
                                    </h2>
                                    <p class="text-gray-600 font-medium">Discover your signature scent</p>
                                </div>
                                
                                <div class="flex items-center gap-4 bg-white/80 rounded-2xl px-6 py-3 shadow-inner">
                                    <span class="text-sm font-bold text-gray-800 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                                        </svg>
                                        Sort by:
                                    </span>
                                    <select x-model="filters.sortBy" @change="applyFilters()"
                                            class="bg-transparent border-none text-sm font-medium text-gray-700 focus:ring-0 cursor-pointer">
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
                    </div>

                    <!-- Loading State -->
                    <template x-if="isLoading">
                        <div class="flex justify-center items-center py-32">
                            <div class="relative">
                                <div class="animate-spin rounded-full h-20 w-20 border-4 border-pink-200 border-t-pink-600"></div>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- Error State -->
                    <template x-if="error">
                        <div class="text-center py-32">
                            <div class="bg-red-50 border-2 border-red-200 rounded-3xl p-12 max-w-lg mx-auto shadow-xl">
                                <div class="w-16 h-16 mx-auto mb-6 bg-red-100 rounded-full flex items-center justify-center">
                                    <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <h3 class="text-xl font-bold text-red-800 mb-4">Oops! Something went wrong</h3>
                                <p class="text-red-600 mb-6" x-text="error"></p>
                                <button @click="fetchPerfumes()" class="bg-red-500 hover:bg-red-600 text-white px-6 py-3 rounded-2xl font-semibold transition-all">
                                    Try Again
                                </button>
                            </div>
                        </div>
                    </template>

                    <!-- Empty State -->
                    <template x-if="!isLoading && !error && perfumes.length === 0">
                        <div class="text-center py-32">
                            <div class="bg-gray-50 border-2 border-gray-200 rounded-3xl p-12 max-w-lg mx-auto shadow-xl">
                                <div class="w-16 h-16 mx-auto mb-6 bg-gray-100 rounded-full flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>
                                <h3 class="text-xl font-bold text-gray-700 mb-4">No perfumes found</h3>
                                <p class="text-gray-600 mb-8">Try adjusting your filters or search criteria to discover more fragrances</p>
                                <button @click="clearFilters()" class="bg-gradient-to-r from-pink-500 to-purple-600 text-white px-8 py-4 rounded-2xl font-semibold hover:from-pink-600 hover:to-purple-700 transition-all transform hover:scale-105 shadow-lg">
                                    Clear All Filters
                                </button>
                            </div>
                        </div>
                    </template>

                    <!-- Perfumes Grid -->
                    <template x-if="!isLoading && !error && perfumes.length > 0">
                        <div class="space-y-12">
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                                <template x-for="perfume in perfumes" :key="perfume.id">
                                    <div class="group bg-white/85 backdrop-blur-xl rounded-3xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-3 border border-white/60 hover:border-white/80">
                                        <!-- Image Container -->
                                        <div class="relative overflow-hidden h-40">
                                            <img :src="perfume.image_url || 'https://images.unsplash.com/photo-1541643600914-78b084683601?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80'" 
                                                 :alt="perfume.name" 
                                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                                            
                                            <!-- Overlay Gradient -->
                                            <div class="absolute inset-0 bg-gradient-to-t from-black/20 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                            
                                            <!-- Season Badge -->
                                            <div class="absolute top-3 left-3">
                                                <span class="px-3 py-1 bg-gradient-to-r from-pink-500 to-purple-600 text-white text-xs font-bold rounded-full shadow-lg backdrop-blur-sm">
                                                    <span x-text="perfume.season || 'All Season'"></span>
                                                </span>
                                            </div>
                                            
                                            <!-- Wishlist Button -->
                                            <button class="absolute top-3 right-3 w-8 h-8 bg-white/95 backdrop-blur-sm rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all transform translate-y-2 group-hover:translate-y-0 hover:bg-pink-500 hover:text-white shadow-lg hover:shadow-xl">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                                </svg>
                                            </button>
                                        </div>
                                        
                                        <!-- Content -->
                                        <div class="p-4 space-y-3">
                                            <div class="space-y-1">
                                                <p class="text-xs font-bold text-pink-600 uppercase tracking-wide" x-text="perfume.brand"></p>
                                                <h3 class="text-base font-bold text-gray-800 line-clamp-2 leading-tight" x-text="perfume.name"></h3>
                                            </div>
                                            
                                            <!-- Price Display -->
                                            <div class="space-y-3">
                                                <div class="flex items-center justify-between">
                                                    <div>
                                                        <span class="text-xl font-bold text-gray-900">
                                                            ₹<span x-text="(perfume.min_price || '4,999').toLocaleString()"></span>
                                                        </span>
                                                        <p class="text-xs text-gray-500 font-medium">
                                                            <span x-text="perfume.seller_count || '3'"></span> sellers
                                                        </p>
                                                    </div>
                                                    <div class="w-8 h-8 bg-gradient-to-r from-green-400 to-green-600 rounded-full flex items-center justify-center shadow-md">
                                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                                        </svg>
                                                    </div>
                                                </div>
                                                
                                                <!-- Seller Prices -->
                                                <div class="bg-gray-50 rounded-xl p-3 space-y-1 text-xs">
                                                    <div class="flex justify-between text-gray-700">
                                                        <span class="font-medium">Luxury Scents</span>
                                                        <span class="font-bold text-green-600">₹<span x-text="(perfume.min_price || '4,999').toLocaleString()"></span></span>
                                                    </div>
                                                    <div class="flex justify-between text-gray-600">
                                                        <span>Perfume World</span>
                                                        <span>₹<span x-text="(parseInt(perfume.min_price || '4999') + 300).toLocaleString()"></span></span>
                                                    </div>
                                                    <template x-if="(perfume.seller_count || 3) > 2">
                                                        <div class="text-pink-600 font-bold text-center pt-1 border-t border-gray-200">
                                                            +<span x-text="(perfume.seller_count || 3) - 2"></span> more sellers
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                            
                                            <!-- Action Button -->
                                            <a :href="'/perfumes/' + perfume.id" 
                                               class="block w-full bg-gradient-to-r from-pink-500 to-purple-600 text-white py-3 rounded-xl font-bold text-center hover:from-pink-600 hover:to-purple-700 transition-all transform hover:scale-105 shadow-lg hover:shadow-xl text-sm">
                                                View All Prices
                                            </a>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <!-- Pagination -->
                            <div class="pt-8" x-show="pagination && pagination.last_page > 1">
                                <nav class="flex items-center justify-center">
                                    <div class="flex items-center space-x-3 bg-white/80 backdrop-blur-xl rounded-3xl p-4 shadow-xl border border-white/50">
                                        <!-- Previous Button -->
                                        <button @click="fetchPerfumes(currentPage - 1)"
                                                :disabled="currentPage === 1"
                                                class="w-12 h-12 flex items-center justify-center text-gray-500 bg-white/90 border border-gray-200 rounded-2xl hover:bg-white hover:text-pink-600 disabled:opacity-50 disabled:cursor-not-allowed transition-all hover:shadow-lg">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                            </svg>
                                        </button>
                                        
                                        <!-- Page Numbers -->
                                        <template x-for="page in Array.from({length: Math.min(5, pagination.last_page)}, (_, i) => i + Math.max(1, currentPage - 2))">
                                            <button @click="fetchPerfumes(page)"
                                                    :class="{
                                                        'bg-gradient-to-r from-pink-500 to-purple-600 text-white shadow-lg': page === currentPage,
                                                        'bg-white/90 text-gray-700 hover:bg-white hover:text-pink-600 hover:shadow-lg': page !== currentPage
                                                    }"
                                                    class="w-12 h-12 flex items-center justify-center text-sm font-bold backdrop-blur-lg border border-gray-200 rounded-2xl transition-all"
                                                    x-text="page">
                                            </button>
                                        </template>
                                        
                                        <!-- Next Button -->
                                        <button @click="fetchPerfumes(currentPage + 1)"
                                                :disabled="currentPage === pagination.last_page"
                                                class="w-12 h-12 flex items-center justify-center text-gray-500 bg-white/90 border border-gray-200 rounded-2xl hover:bg-white hover:text-pink-600 disabled:opacity-50 disabled:cursor-not-allowed transition-all hover:shadow-lg">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
        height: 24px;
        width: 24px;
        border-radius: 50%;
        background: linear-gradient(45deg, #ec4899, #9333ea);
        cursor: pointer;
        border: 3px solid white;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        transition: all 0.2s ease;
    }
    
    .slider-thumb::-webkit-slider-thumb:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 20px rgba(0,0,0,0.2);
    }
    
    .slider-thumb::-moz-range-thumb {
        height: 24px;
        width: 24px;
        border-radius: 50%;
        background: linear-gradient(45deg, #ec4899, #9333ea);
        cursor: pointer;
        border: 3px solid white;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        transition: all 0.2s ease;
    }
    
    .slider-thumb::-moz-range-thumb:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 20px rgba(0,0,0,0.2);
    }
    
    .line-clamp-1 {
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Custom scrollbar for webkit browsers */
    ::-webkit-scrollbar {
        width: 8px;
    }
    
    ::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 10px;
    }
    
    ::-webkit-scrollbar-thumb {
        background: linear-gradient(45deg, #ec4899, #9333ea);
        border-radius: 10px;
    }
    
    ::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(45deg, #be185d, #7c3aed);
    }

    /* Smooth animations */
    * {
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    }
</style>
@endsection