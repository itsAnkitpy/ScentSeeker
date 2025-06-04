@extends('layouts.app')

@section('title', 'Discover Your Perfect Scent')

@section('content')
    <!-- Hero Section -->
    <section class="relative overflow-hidden">
        <!-- Background Elements -->
        <div class="absolute inset-0 bg-gradient-to-br from-pink-100 via-purple-50 to-indigo-100"></div>
        <div class="absolute top-10 right-10 w-32 h-32 bg-pink-200 rounded-full opacity-20 animate-pulse"></div>
        <div class="absolute bottom-20 left-10 w-24 h-24 bg-purple-200 rounded-full opacity-30 animate-pulse delay-1000"></div>
        <div class="absolute top-1/2 right-1/4 w-16 h-16 bg-indigo-200 rounded-full opacity-25 animate-pulse delay-500"></div>
        
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-32">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <!-- Left Content -->
                <div class="text-center lg:text-left">
                    <h1 class="text-5xl lg:text-7xl font-bold leading-tight mb-6">
                        <span class="font-playfair bg-gradient-to-r from-pink-600 via-purple-600 to-indigo-600 bg-clip-text text-transparent">
                            Discover Your
                        </span>
                        <br>
                        <span class="font-playfair bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">
                            Perfect Scent
                        </span>
                    </h1>
                    
                    <p class="text-xl lg:text-2xl text-gray-600 mb-8 font-light leading-relaxed max-w-lg lg:max-w-none">
                        Compare prices, read reviews, and find the best deals on luxury perfumes from top brands worldwide
                    </p>
                    
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                        <button class="group bg-gradient-to-r from-pink-500 to-purple-600 text-white px-8 py-4 rounded-2xl font-semibold text-lg hover:from-pink-600 hover:to-purple-700 transition-all transform hover:scale-105 shadow-lg hover:shadow-xl">
                            <span class="flex items-center justify-center">
                                Start Comparing
                                <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </span>
                        </button>
                        
                        <button class="group bg-white/80 backdrop-blur-lg text-gray-700 px-8 py-4 rounded-2xl font-semibold text-lg hover:bg-white hover:shadow-lg transition-all transform hover:scale-105 border border-white/50">
                            <span class="flex items-center justify-center">
                                Browse Perfumes
                                <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                </svg>
                            </span>
                        </button>
                    </div>
                </div>
                
                <!-- Right Visual -->
                <div class="relative flex justify-center lg:justify-end">
                    <div class="relative">
                        <!-- Decorative perfume bottles -->
                        <div class="absolute -top-8 -left-8 w-16 h-24 bg-gradient-to-b from-pink-400 to-pink-500 rounded-t-full rounded-b-lg opacity-80 transform -rotate-12 animate-float"></div>
                        <div class="absolute -top-4 -right-12 w-20 h-28 bg-gradient-to-b from-purple-400 to-purple-500 rounded-t-full rounded-b-lg opacity-90 transform rotate-12 animate-float-delayed"></div>
                        <div class="w-24 h-32 bg-gradient-to-b from-indigo-400 to-indigo-500 rounded-t-full rounded-b-lg shadow-2xl transform hover:scale-110 transition-transform cursor-pointer"></div>
                        <div class="absolute -bottom-6 -right-6 w-14 h-20 bg-gradient-to-b from-rose-400 to-rose-500 rounded-t-full rounded-b-lg opacity-75 transform rotate-6 animate-float"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Search Bar Section -->
    <section class="bg-white/60 backdrop-blur-lg py-12 border-y border-white/30">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white/80 backdrop-blur-lg rounded-3xl p-8 shadow-xl border border-white/40">
                <h3 class="text-2xl font-semibold text-center mb-6 text-gray-800">Find Your Perfect Fragrance</h3>
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1 relative">
                        <input type="text" placeholder="Search by perfume name, brand, or notes..." 
                               class="w-full px-6 py-4 rounded-2xl border border-gray-200 focus:border-pink-400 focus:ring-2 focus:ring-pink-200 text-lg bg-white/90 backdrop-blur-sm transition-all">
                        <svg class="absolute right-4 top-1/2 transform -translate-y-1/2 w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <button class="bg-gradient-to-r from-pink-500 to-purple-600 text-white px-8 py-4 rounded-2xl font-semibold hover:from-pink-600 hover:to-purple-700 transition-all transform hover:scale-105">
                        Search
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Perfumes Section -->
    <section class="py-20 relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-pink-50/30 to-transparent"></div>
        
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" x-data="{ perfumes: [], isLoading: true, error: null }">
            <div class="text-center mb-16">
                <h2 class="text-4xl lg:text-5xl font-bold font-playfair mb-4">
                    <span class="bg-gradient-to-r from-pink-600 to-purple-600 bg-clip-text text-transparent">
                        Featured Perfumes
                    </span>
                </h2>
                <p class="text-xl text-gray-600 font-light max-w-2xl mx-auto">
                    Discover the most popular and trending fragrances with the best prices from verified sellers
                </p>
            </div>
            
            <div x-init="
                fetch('/api/v1/perfumes')
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        perfumes = data.data || data; // Handle different API response formats
                        isLoading = false;
                    })
                    .catch(err => {
                        error = err.message;
                        isLoading = false;
                        console.error('Error fetching perfumes:', err);
                    })
            ">
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                            </svg>
                            <h3 class="text-lg font-semibold text-gray-700 mb-2">No perfumes available</h3>
                            <p class="text-gray-600">Check back soon for amazing fragrance deals!</p>
                        </div>
                    </div>
                </template>

                <!-- Perfumes Grid -->
                <template x-if="!isLoading && !error && perfumes.length > 0">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                        <template x-for="perfume in perfumes" :key="perfume.id">
                            <div class="group bg-white/80 backdrop-blur-lg rounded-3xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-white/50">
                                <!-- Image -->
                                <div class="relative overflow-hidden h-64">
                                    <img :src="perfume.image_url || 'https://images.unsplash.com/photo-1541643600914-78b084683601?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80'" 
                                         :alt="perfume.name" 
                                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                    
                                    <!-- Wishlist Button -->
                                    <button class="absolute top-4 right-4 w-10 h-10 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all transform translate-y-2 group-hover:translate-y-0 hover:bg-pink-500 hover:text-white">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                        </svg>
                                    </button>
                                </div>
                                
                                <!-- Content -->
                                <div class="p-6">
                                    <div class="mb-3">
                                        <h3 class="text-lg font-semibold text-gray-800 mb-1 line-clamp-1" x-text="perfume.name"></h3>
                                        <p class="text-sm font-medium text-pink-600" x-text="perfume.brand"></p>
                                    </div>
                                    
                                    <p class="text-sm text-gray-600 mb-4 line-clamp-2" x-text="perfume.description || 'A luxurious fragrance that captures the essence of elegance and sophistication.'"></p>
                                    
                                    <!-- Rating -->
                                    <div class="flex items-center mb-4">
                                        <div class="flex text-yellow-400">
                                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                            <svg class="w-4 h-4 text-gray-300 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                        </div>
                                        <span class="text-xs text-gray-500 ml-2">4.5 (128)</span>
                                    </div>
                                    
                                    <!-- Action Button -->
                                    <button @click="console.log('View details for', perfume.id)" 
                                            class="w-full bg-gradient-to-r from-pink-500 to-purple-600 text-white py-3 rounded-2xl font-semibold hover:from-pink-600 hover:to-purple-700 transition-all transform hover:scale-105 shadow-md">
                                        View Details & Prices
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-20 bg-white/40 backdrop-blur-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold font-playfair mb-4">
                    <span class="bg-gradient-to-r from-pink-600 to-purple-600 bg-clip-text text-transparent">
                        Why Choose ScentSeeker?
                    </span>
                </h2>
                <p class="text-xl text-gray-600 font-light max-w-2xl mx-auto">
                    We make finding your perfect fragrance simple, affordable, and enjoyable
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center group">
                    <div class="w-16 h-16 bg-gradient-to-r from-pink-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Price Comparison</h3>
                    <p class="text-gray-600">Compare prices from multiple sellers to find the best deals on your favorite fragrances.</p>
                </div>
                
                <div class="text-center group">
                    <div class="w-16 h-16 bg-gradient-to-r from-purple-500 to-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Verified Reviews</h3>
                    <p class="text-gray-600">Read authentic reviews from real customers to make informed purchasing decisions.</p>
                </div>
                
                <div class="text-center group">
                    <div class="w-16 h-16 bg-gradient-to-r from-indigo-500 to-pink-600 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9H4l5-5v5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Smart Alerts</h3>
                    <p class="text-gray-600">Get notified when your favorite perfumes go on sale or drop in price.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Custom Styles -->
    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(-12deg); }
            50% { transform: translateY(-10px) rotate(-12deg); }
        }
        
        @keyframes float-delayed {
            0%, 100% { transform: translateY(0px) rotate(12deg); }
            50% { transform: translateY(-15px) rotate(12deg); }
        }
        
        .animate-float {
            animation: float 3s ease-in-out infinite;
        }
        
        .animate-float-delayed {
            animation: float-delayed 3s ease-in-out infinite 1s;
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
    </style>
@endsection
