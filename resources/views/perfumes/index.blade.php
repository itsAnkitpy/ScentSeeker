@extends('layouts.app')

@section('title', 'Browse Perfumes')

@section('content')
    <div class="min-h-screen bg-gradient-to-b from-gray-50 to-white" x-data="perfumesApp()" x-init="init()">

        <!-- Header Section -->
        <div class="bg-white border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="text-center mb-6">
                    <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-2">
                        Discover <span class="text-teal-600">Perfumes</span>
                    </h1>
                    <p class="text-gray-600">Compare prices from 50+ sellers and find the best deals</p>
                </div>

                <!-- Horizontal Filter Bar -->
                <div class="flex flex-col lg:flex-row gap-4 items-stretch lg:items-center">
                    <!-- Search -->
                    <div class="relative flex-1 max-w-md">
                        <input type="text" x-model.debounce.400ms="searchTerm" @input="fetchPerfumes(1)"
                            placeholder="Search perfumes or brands..."
                            class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:border-teal-500 focus:ring-2 focus:ring-teal-100 transition-all text-sm">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>

                    <!-- Quick Filters -->
                    <div class="flex flex-wrap gap-2 items-center">
                        <!-- Price Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" @click.outside="open = false"
                                class="flex items-center gap-2 px-4 py-3 bg-white border border-gray-200 rounded-xl hover:border-teal-300 transition-all text-sm font-medium"
                                :class="{ 'border-teal-500 bg-teal-50': filters.priceRange[0] > 0 || filters.priceRange[1] < 15000 }">
                                <span>Price</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="open" x-transition
                                class="absolute top-full left-0 mt-2 w-64 bg-white rounded-xl shadow-lg border border-gray-100 p-4 z-50">
                                <div class="space-y-4">
                                    <div class="flex justify-between text-sm font-medium text-gray-700">
                                        <span>₹<span x-text="filters.priceRange[0].toLocaleString()"></span></span>
                                        <span>₹<span x-text="filters.priceRange[1].toLocaleString()"></span></span>
                                    </div>
                                    <input type="range" x-model="filters.priceRange[0]" min="0" max="15000" step="500"
                                        class="w-full accent-teal-600">
                                    <input type="range" x-model="filters.priceRange[1]" min="0" max="15000" step="500"
                                        class="w-full accent-teal-600">
                                    <button @click="applyFilters(); open = false"
                                        class="w-full py-2 bg-teal-600 text-white rounded-lg text-sm font-medium hover:bg-teal-700">
                                        Apply
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Brand Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" @click.outside="open = false"
                                class="flex items-center gap-2 px-4 py-3 bg-white border border-gray-200 rounded-xl hover:border-teal-300 transition-all text-sm font-medium"
                                :class="{ 'border-teal-500 bg-teal-50': filters.brands.length > 0 }">
                                <span>Brand</span>
                                <template x-if="filters.brands.length > 0">
                                    <span class="bg-teal-600 text-white text-xs px-1.5 py-0.5 rounded-full"
                                        x-text="filters.brands.length"></span>
                                </template>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="open" x-transition
                                class="absolute top-full left-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-gray-100 p-3 z-50 max-h-64 overflow-y-auto">
                                <template
                                    x-for="brand in ['Chanel', 'Dior', 'Tom Ford', 'Jo Malone', 'Gucci', 'YSL', 'Armani', 'Versace']">
                                    <label
                                        class="flex items-center gap-3 px-2 py-2 hover:bg-gray-50 rounded-lg cursor-pointer">
                                        <input type="checkbox" :checked="filters.brands.includes(brand)"
                                            @change="toggleBrand(brand)"
                                            class="w-4 h-4 text-teal-600 rounded border-gray-300 focus:ring-teal-500">
                                        <span class="text-sm text-gray-700" x-text="brand"></span>
                                    </label>
                                </template>
                            </div>
                        </div>

                        <!-- Sort Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" @click.outside="open = false"
                                class="flex items-center gap-2 px-4 py-3 bg-white border border-gray-200 rounded-xl hover:border-teal-300 transition-all text-sm font-medium">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                </svg>
                                <span x-text="sortLabels[filters.sortBy]">Price: Low</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="open" x-transition
                                class="absolute top-full right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-2 z-50">
                                <template x-for="(label, key) in sortLabels">
                                    <button @click="filters.sortBy = key; applyFilters(); open = false"
                                        class="w-full px-4 py-2 text-left text-sm hover:bg-gray-50"
                                        :class="{ 'text-teal-600 font-medium': filters.sortBy === key }">
                                        <span x-text="label"></span>
                                    </button>
                                </template>
                            </div>
                        </div>

                        <!-- All Filters Button -->
                        <button @click="showFiltersModal = true"
                            class="flex items-center gap-2 px-4 py-3 bg-gray-900 text-white rounded-xl hover:bg-gray-800 transition-all text-sm font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                            </svg>
                            <span>All Filters</span>
                        </button>
                    </div>
                </div>

                <!-- Active Filter Pills -->
                <div class="flex flex-wrap gap-2 mt-4" x-show="hasActiveFilters()">
                    <template x-for="brand in filters.brands">
                        <span
                            class="inline-flex items-center gap-1 px-3 py-1 bg-teal-50 text-teal-700 rounded-full text-sm">
                            <span x-text="brand"></span>
                            <button @click="toggleBrand(brand)" class="hover:text-teal-900">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </span>
                    </template>
                    <template x-if="filters.priceRange[0] > 0 || filters.priceRange[1] < 15000">
                        <span
                            class="inline-flex items-center gap-1 px-3 py-1 bg-teal-50 text-teal-700 rounded-full text-sm">
                            <span>₹<span x-text="filters.priceRange[0].toLocaleString()"></span> - ₹<span
                                    x-text="filters.priceRange[1].toLocaleString()"></span></span>
                            <button @click="filters.priceRange = [0, 15000]; applyFilters()" class="hover:text-teal-900">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </span>
                    </template>
                    <button @click="clearFilters()" class="text-sm text-gray-500 hover:text-gray-700 underline">
                        Clear all
                    </button>
                </div>
            </div>
        </div>

        <!-- Results Section -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Results Count -->
            <div class="flex items-center justify-between mb-6">
                <p class="text-gray-600">
                    <span class="font-semibold text-gray-900" x-text="pagination.total || '0'"></span> perfumes found
                </p>
            </div>

            <!-- Loading State -->
            <template x-if="isLoading">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <template x-for="i in 6">
                        <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-gray-100 animate-pulse">
                            <div class="h-48 bg-gray-200"></div>
                            <div class="p-5 space-y-3">
                                <div class="h-3 bg-gray-200 rounded w-1/3"></div>
                                <div class="h-5 bg-gray-200 rounded w-3/4"></div>
                                <div class="h-4 bg-gray-200 rounded w-1/2"></div>
                                <div class="h-10 bg-gray-200 rounded"></div>
                            </div>
                        </div>
                    </template>
                </div>
            </template>

            <!-- Error State -->
            <template x-if="error && !isLoading">
                <div class="text-center py-16">
                    <div class="w-16 h-16 mx-auto mb-4 bg-red-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Something went wrong</h3>
                    <p class="text-gray-600 mb-4" x-text="error"></p>
                    <button @click="fetchPerfumes()" class="px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700">
                        Try Again
                    </button>
                </div>
            </template>

            <!-- Empty State -->
            <template x-if="!isLoading && !error && perfumes.length === 0">
                <div class="text-center py-16">
                    <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">No perfumes found</h3>
                    <p class="text-gray-600 mb-4">Try adjusting your filters or search criteria</p>
                    <button @click="clearFilters()" class="px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700">
                        Clear Filters
                    </button>
                </div>
            </template>

            <!-- Perfumes Grid -->
            <template x-if="!isLoading && !error && perfumes.length > 0">
                <div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        <template x-for="perfume in perfumes" :key="perfume.id">
                            <a :href="'/perfumes/' + perfume.id"
                                class="group bg-white rounded-2xl overflow-hidden shadow-sm border border-gray-100 hover:shadow-lg hover:border-teal-200 transition-all duration-300">
                                <!-- Image -->
                                <div class="relative h-48 bg-gradient-to-br from-gray-50 to-gray-100 overflow-hidden">
                                    <img :src="perfume.image_url || 'https://images.unsplash.com/photo-1541643600914-78b084683601?w=400'"
                                        :alt="perfume.name"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                        loading="lazy">
                                    <!-- Season Badge -->
                                    <span
                                        class="absolute top-3 left-3 px-2 py-1 bg-white/90 backdrop-blur-sm text-xs font-medium text-gray-700 rounded-lg"
                                        x-text="perfume.season || 'All Season'"></span>
                                </div>

                                <!-- Content -->
                                <div class="p-5">
                                    <p class="text-xs font-semibold text-teal-600 uppercase tracking-wide mb-1"
                                        x-text="perfume.brand"></p>
                                    <h3 class="font-semibold text-gray-900 mb-1 line-clamp-2" x-text="perfume.name"></h3>
                                    <p class="text-sm text-gray-500 mb-4">
                                        <span x-text="perfume.size_ml || '100'"></span>ml • <span
                                            x-text="perfume.concentration || 'EDP'"></span>
                                    </p>

                                    <!-- Price & CTA -->
                                    <div class="flex items-end justify-between">
                                        <div>
                                            <p class="text-xs text-gray-500">From</p>
                                            <p class="text-xl font-bold text-gray-900">₹<span
                                                    x-text="(perfume.min_price || 4999).toLocaleString()"></span></p>
                                        </div>
                                        <div
                                            class="flex items-center gap-1 text-teal-600 text-sm font-medium group-hover:translate-x-1 transition-transform">
                                            <span>Compare <span x-text="perfume.seller_count || 3"></span> prices</span>
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5l7 7-7 7" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </template>
                    </div>

                    <!-- Pagination -->
                    <div class="flex justify-center mt-12" x-show="pagination.last_page > 1">
                        <div class="flex items-center gap-2">
                            <button @click="fetchPerfumes(currentPage - 1)" :disabled="currentPage === 1"
                                class="p-2 rounded-lg border border-gray-200 hover:border-teal-300 disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 19l-7-7 7-7" />
                                </svg>
                            </button>

                            <template x-for="page in paginationPages()">
                                <button @click="fetchPerfumes(page)"
                                    class="w-10 h-10 rounded-lg text-sm font-medium transition-all"
                                    :class="page === currentPage ? 'bg-teal-600 text-white' : 'border border-gray-200 hover:border-teal-300'"
                                    x-text="page">
                                </button>
                            </template>

                            <button @click="fetchPerfumes(currentPage + 1)" :disabled="currentPage === pagination.last_page"
                                class="p-2 rounded-lg border border-gray-200 hover:border-teal-300 disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- All Filters Modal -->
        <div x-show="showFiltersModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div class="flex items-center justify-center min-h-screen px-4">
                <!-- Backdrop -->
                <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="showFiltersModal = false"></div>

                <!-- Modal -->
                <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[85vh] overflow-hidden"
                    x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100">

                    <!-- Header -->
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                        <h2 class="text-xl font-bold text-gray-900">Filter Perfumes</h2>
                        <button @click="showFiltersModal = false" class="p-2 hover:bg-gray-100 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Content -->
                    <div class="px-6 py-6 overflow-y-auto max-h-[60vh] space-y-8">
                        <!-- Price Range -->
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900 mb-4">PRICE RANGE</h3>
                            <div class="flex items-center gap-4 mb-4">
                                <div class="flex-1">
                                    <label class="text-xs text-gray-500">Min</label>
                                    <input type="number" x-model.number="filters.priceRange[0]" min="0" max="15000"
                                        class="w-full mt-1 px-3 py-2 border border-gray-200 rounded-lg text-sm focus:border-teal-500 focus:ring-1 focus:ring-teal-500">
                                </div>
                                <span class="text-gray-400 pt-5">—</span>
                                <div class="flex-1">
                                    <label class="text-xs text-gray-500">Max</label>
                                    <input type="number" x-model.number="filters.priceRange[1]" min="0" max="15000"
                                        class="w-full mt-1 px-3 py-2 border border-gray-200 rounded-lg text-sm focus:border-teal-500 focus:ring-1 focus:ring-teal-500">
                                </div>
                            </div>
                        </div>

                        <!-- Brands -->
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900 mb-4">BRANDS</h3>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                <template
                                    x-for="brand in ['Chanel', 'Dior', 'Tom Ford', 'Jo Malone', 'Gucci', 'YSL', 'Armani', 'Versace', 'Creed', 'Hermès']">
                                    <label
                                        class="flex items-center gap-2 px-3 py-2 border rounded-lg cursor-pointer transition-all text-sm"
                                        :class="filters.brands.includes(brand) ? 'border-teal-500 bg-teal-50 text-teal-700' : 'border-gray-200 hover:border-gray-300'">
                                        <input type="checkbox" :checked="filters.brands.includes(brand)"
                                            @change="toggleBrand(brand)" class="sr-only">
                                        <span x-text="brand"></span>
                                    </label>
                                </template>
                            </div>
                        </div>

                        <!-- Concentration -->
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900 mb-4">CONCENTRATION</h3>
                            <div class="flex flex-wrap gap-2">
                                <template x-for="conc in ['EDP', 'EDT', 'Parfum', 'Cologne', 'Extrait']">
                                    <label
                                        class="flex items-center gap-2 px-4 py-2 border rounded-full cursor-pointer transition-all text-sm"
                                        :class="filters.concentrations.includes(conc) ? 'border-teal-500 bg-teal-50 text-teal-700' : 'border-gray-200 hover:border-gray-300'">
                                        <input type="checkbox" :checked="filters.concentrations.includes(conc)"
                                            @change="toggleFilter('concentrations', conc)" class="sr-only">
                                        <span x-text="conc"></span>
                                    </label>
                                </template>
                            </div>
                        </div>

                        <!-- Gender -->
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900 mb-4">GENDER</h3>
                            <div class="flex flex-wrap gap-2">
                                <template x-for="gender in ['Male', 'Female', 'Unisex']">
                                    <label
                                        class="flex items-center gap-2 px-4 py-2 border rounded-full cursor-pointer transition-all text-sm"
                                        :class="filters.genders.includes(gender) ? 'border-teal-500 bg-teal-50 text-teal-700' : 'border-gray-200 hover:border-gray-300'">
                                        <input type="checkbox" :checked="filters.genders.includes(gender)"
                                            @change="toggleFilter('genders', gender)" class="sr-only">
                                        <span x-text="gender"></span>
                                    </label>
                                </template>
                            </div>
                        </div>

                        <!-- Season -->
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900 mb-4">SEASON</h3>
                            <div class="flex flex-wrap gap-2">
                                <template x-for="season in ['Spring', 'Summer', 'Fall', 'Winter']">
                                    <label
                                        class="flex items-center gap-2 px-4 py-2 border rounded-full cursor-pointer transition-all text-sm"
                                        :class="filters.seasons.includes(season) ? 'border-teal-500 bg-teal-50 text-teal-700' : 'border-gray-200 hover:border-gray-300'">
                                        <input type="checkbox" :checked="filters.seasons.includes(season)"
                                            @change="toggleSeason(season)" class="sr-only">
                                        <span x-text="season"></span>
                                    </label>
                                </template>
                            </div>
                        </div>

                        <!-- Size -->
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900 mb-4">SIZE</h3>
                            <div class="flex flex-wrap gap-2">
                                <template x-for="size in [30, 50, 100, 150, 200]">
                                    <label
                                        class="flex items-center gap-2 px-4 py-2 border rounded-full cursor-pointer transition-all text-sm"
                                        :class="filters.sizes.includes(size) ? 'border-teal-500 bg-teal-50 text-teal-700' : 'border-gray-200 hover:border-gray-300'">
                                        <input type="checkbox" :checked="filters.sizes.includes(size)"
                                            @change="toggleFilter('sizes', size)" class="sr-only">
                                        <span x-text="size + 'ml'"></span>
                                    </label>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="flex items-center justify-between px-6 py-4 border-t border-gray-100 bg-gray-50">
                        <button @click="clearFilters()" class="text-sm text-gray-600 hover:text-gray-900">
                            Clear all filters
                        </button>
                        <button @click="applyFilters(); showFiltersModal = false"
                            class="px-6 py-2.5 bg-teal-600 text-white rounded-lg font-medium hover:bg-teal-700 transition-colors">
                            Apply Filters
                            <span x-show="pagination.total" class="ml-1">(<span x-text="pagination.total"></span>)</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function perfumesApp() {
            return {
                perfumes: [],
                isLoading: true,
                error: null,
                pagination: {},
                currentPage: 1,
                searchTerm: '',
                showFiltersModal: false,
                filters: {
                    priceRange: [0, 15000],
                    brands: [],
                    concentrations: [],
                    genders: [],
                    sizes: [],
                    seasons: [],
                    sortBy: 'price_low_to_high'
                },
                sortLabels: {
                    'price_low_to_high': 'Price: Low to High',
                    'price_high_to_low': 'Price: High to Low',
                    'name_asc': 'Name: A-Z',
                    'name_desc': 'Name: Z-A',
                    'brand_asc': 'Brand: A-Z',
                    'newest': 'Newest First'
                },

                init() {
                    this.fetchPerfumes();
                },

                fetchPerfumes(page = 1) {
                    this.isLoading = true;
                    this.error = null;

                    const params = new URLSearchParams();
                    params.append('page', page);
                    if (this.searchTerm.trim() !== '') {
                        params.append('search', this.searchTerm.trim());
                    }
                    params.append('min_price', this.filters.priceRange[0]);
                    params.append('max_price', this.filters.priceRange[1]);
                    params.append('sort', this.filters.sortBy);

                    if (this.filters.brands.length > 0) {
                        params.append('brands', this.filters.brands.join(','));
                    }
                    if (this.filters.concentrations.length > 0) {
                        params.append('concentrations', this.filters.concentrations.join(','));
                    }
                    if (this.filters.genders.length > 0) {
                        params.append('genders', this.filters.genders.join(','));
                    }
                    if (this.filters.sizes.length > 0) {
                        params.append('sizes', this.filters.sizes.join(','));
                    }
                    if (this.filters.seasons.length > 0) {
                        this.filters.seasons.forEach(season => params.append('seasons[]', season));
                    }

                    fetch(`/api/v1/perfumes?${params.toString()}`)
                        .then(response => {
                            if (!response.ok) throw new Error('Failed to fetch perfumes');
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

                toggleFilter(filterName, value) {
                    const arr = this.filters[filterName];
                    const index = arr.indexOf(value);
                    if (index > -1) {
                        arr.splice(index, 1);
                    } else {
                        arr.push(value);
                    }
                    this.applyFilters();
                },

                clearFilters() {
                    this.filters = {
                        priceRange: [0, 15000],
                        brands: [],
                        concentrations: [],
                        genders: [],
                        sizes: [],
                        seasons: [],
                        sortBy: 'price_low_to_high'
                    };
                    this.searchTerm = '';
                    this.applyFilters();
                },

                hasActiveFilters() {
                    return this.filters.brands.length > 0 ||
                        this.filters.priceRange[0] > 0 ||
                        this.filters.priceRange[1] < 15000 ||
                        this.filters.concentrations.length > 0 ||
                        this.filters.genders.length > 0 ||
                        this.filters.sizes.length > 0 ||
                        this.filters.seasons.length > 0;
                },

                paginationPages() {
                    const pages = [];
                    const total = this.pagination.last_page || 1;
                    const current = this.currentPage;

                    let start = Math.max(1, current - 2);
                    let end = Math.min(total, start + 4);

                    if (end - start < 4) {
                        start = Math.max(1, end - 4);
                    }

                    for (let i = start; i <= end; i++) {
                        pages.push(i);
                    }
                    return pages;
                }
            };
        }
    </script>

    <style>
        [x-cloak] {
            display: none !important;
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
@endsection