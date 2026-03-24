@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="dashboardApp()">
        <!-- Welcome Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold font-playfair">
                <span class="bg-gradient-to-r from-teal-600 to-teal-700 bg-clip-text text-transparent">
                    Welcome back, <span x-text="user?.username || 'there'"></span>
                </span>
            </h1>
            <p class="text-gray-600 mt-1">Here's what's happening with your fragrances</p>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
            <div class="bg-white/80 backdrop-blur-lg rounded-2xl border border-white/50 shadow-sm p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Wishlist Items</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1" x-text="stats.wishlistCount">-</p>
                    </div>
                    <div class="w-10 h-10 bg-teal-50 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </div>
                </div>
                <a href="/wishlist" class="text-sm text-teal-600 hover:text-teal-700 mt-3 inline-block font-medium">View wishlist &rarr;</a>
            </div>

            <div class="bg-white/80 backdrop-blur-lg rounded-2xl border border-white/50 shadow-sm p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Active Alerts</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1" x-text="stats.activeAlerts">-</p>
                    </div>
                    <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </div>
                </div>
                <a href="/alerts" class="text-sm text-amber-600 hover:text-amber-700 mt-3 inline-block font-medium">Manage alerts &rarr;</a>
            </div>

            <div class="bg-white/80 backdrop-blur-lg rounded-2xl border border-white/50 shadow-sm p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Price Drops</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1" x-text="stats.triggeredAlerts">-</p>
                    </div>
                    <div class="w-10 h-10 bg-green-50 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                        </svg>
                    </div>
                </div>
                <a href="/alerts" class="text-sm text-green-600 hover:text-green-700 mt-3 inline-block font-medium">View drops &rarr;</a>
            </div>
        </div>

        <!-- Error Banner -->
        <template x-if="error">
            <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6 flex items-center justify-between">
                <p class="text-red-600 text-sm" x-text="error"></p>
                <button @click="fetchData(localStorage.getItem('auth_token'))" class="text-sm text-red-700 font-semibold hover:underline ml-4">Retry</button>
            </div>
        </template>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Price Alerts Feed -->
            <div class="bg-white/80 backdrop-blur-lg rounded-2xl border border-white/50 shadow-sm">
                <div class="flex items-center justify-between p-5 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-900">Recent Price Alerts</h2>
                    <a href="/alerts" class="text-sm text-teal-600 hover:text-teal-700 font-medium">View all</a>
                </div>
                <div class="p-5">
                    <!-- Loading -->
                    <template x-if="loading">
                        <div class="flex justify-center py-8">
                            <div class="w-6 h-6 border-2 border-teal-500 border-t-transparent rounded-full animate-spin"></div>
                        </div>
                    </template>

                    <!-- Alerts List -->
                    <template x-if="!loading && alerts.length > 0">
                        <div class="space-y-4">
                            <template x-for="alert in alerts" :key="alert.id">
                                <a :href="'/perfumes/' + alert.perfume_id" class="flex items-center gap-4 p-3 rounded-xl hover:bg-teal-50/50 transition-colors group">
                                    <div class="w-12 h-12 bg-gradient-to-br from-teal-50 to-cyan-50 rounded-xl flex items-center justify-center flex-shrink-0">
                                        <template x-if="alert.perfume?.image_url">
                                            <img :src="alert.perfume.image_url" class="w-10 h-10 object-cover rounded-lg" :alt="alert.perfume?.name">
                                        </template>
                                        <template x-if="!alert.perfume?.image_url">
                                            <svg class="w-5 h-5 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                            </svg>
                                        </template>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate" x-text="alert.perfume?.name || 'Unknown Perfume'"></p>
                                        <p class="text-xs text-gray-500" x-text="alert.perfume?.brand || ''"></p>
                                    </div>
                                    <div class="text-right flex-shrink-0">
                                        <p class="text-sm font-semibold" :class="alert.triggered_at ? 'text-green-600' : 'text-gray-900'">
                                            <span x-text="'Rs ' + parseFloat(alert.target_price).toLocaleString()"></span>
                                        </p>
                                        <p class="text-xs" :class="alert.triggered_at ? 'text-green-500' : 'text-gray-400'"
                                            x-text="alert.triggered_at ? 'Triggered' : (alert.is_active ? 'Watching' : 'Paused')"></p>
                                    </div>
                                </a>
                            </template>
                        </div>
                    </template>

                    <!-- Empty State -->
                    <template x-if="!loading && alerts.length === 0">
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <p class="text-gray-500 text-sm">No price alerts yet</p>
                            <a href="{{ route('perfumes.index') }}" class="text-sm text-teal-600 hover:text-teal-700 font-medium mt-2 inline-block">Browse perfumes to set alerts &rarr;</a>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Wishlist Preview -->
            <div class="bg-white/80 backdrop-blur-lg rounded-2xl border border-white/50 shadow-sm">
                <div class="flex items-center justify-between p-5 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-900">My Wishlist</h2>
                    <a href="/wishlist" class="text-sm text-teal-600 hover:text-teal-700 font-medium">View all</a>
                </div>
                <div class="p-5">
                    <!-- Loading -->
                    <template x-if="loading">
                        <div class="flex justify-center py-8">
                            <div class="w-6 h-6 border-2 border-teal-500 border-t-transparent rounded-full animate-spin"></div>
                        </div>
                    </template>

                    <!-- Wishlist Items -->
                    <template x-if="!loading && wishlistItems.length > 0">
                        <div class="space-y-4">
                            <template x-for="item in wishlistItems.slice(0, 5)" :key="item.id">
                                <a :href="'/perfumes/' + item.perfume_id" class="flex items-center gap-4 p-3 rounded-xl hover:bg-teal-50/50 transition-colors group">
                                    <div class="w-12 h-12 bg-gradient-to-br from-teal-50 to-cyan-50 rounded-xl flex items-center justify-center flex-shrink-0">
                                        <template x-if="item.perfume?.image_url">
                                            <img :src="item.perfume.image_url" class="w-10 h-10 object-cover rounded-lg" :alt="item.perfume?.name">
                                        </template>
                                        <template x-if="!item.perfume?.image_url">
                                            <svg class="w-5 h-5 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                            </svg>
                                        </template>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate" x-text="item.perfume?.name || 'Unknown Perfume'"></p>
                                        <p class="text-xs text-gray-500" x-text="item.perfume?.brand || ''"></p>
                                    </div>
                                    <div class="text-right flex-shrink-0">
                                        <template x-if="item.perfume?.lowest_price">
                                            <div>
                                                <p class="text-sm font-semibold text-gray-900" x-text="'Rs ' + parseFloat(item.perfume.lowest_price).toLocaleString()"></p>
                                                <p class="text-xs text-gray-400">Lowest price</p>
                                            </div>
                                        </template>
                                    </div>
                                </a>
                            </template>
                        </div>
                    </template>

                    <!-- Empty State -->
                    <template x-if="!loading && wishlistItems.length === 0">
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                            <p class="text-gray-500 text-sm">Your wishlist is empty</p>
                            <a href="{{ route('perfumes.index') }}" class="text-sm text-teal-600 hover:text-teal-700 font-medium mt-2 inline-block">Browse perfumes to add some &rarr;</a>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Quick Browse CTA -->
        <div class="mt-8 bg-gradient-to-r from-teal-500 to-teal-700 rounded-2xl p-6 text-center">
            <h3 class="text-xl font-bold text-white font-playfair mb-2">Discover New Fragrances</h3>
            <p class="text-teal-100 text-sm mb-4">Compare prices from multiple sellers and find the best deals</p>
            <a href="{{ route('perfumes.index') }}"
                class="inline-block bg-white text-teal-700 px-6 py-2.5 rounded-xl font-semibold hover:bg-teal-50 transition-colors">
                Browse Perfumes
            </a>
        </div>
    </div>

    <script>
        function dashboardApp() {
            return {
                user: null,
                loading: true,
                error: null,
                alerts: [],
                wishlistItems: [],
                stats: {
                    wishlistCount: '-',
                    activeAlerts: '-',
                    triggeredAlerts: '-',
                },

                init() {
                    const userJson = localStorage.getItem('user');
                    const token = localStorage.getItem('auth_token');

                    if (!userJson || !token) {
                        window.location.href = '/login';
                        return;
                    }

                    try {
                        this.user = JSON.parse(userJson);
                    } catch (e) {
                        window.location.href = '/login';
                        return;
                    }

                    this.fetchData(token);
                },

                async fetchData(token) {
                    this.error = null;
                    const headers = {
                        'Accept': 'application/json',
                        'Authorization': 'Bearer ' + token
                    };

                    try {
                        const [alertsRes, wishlistsRes] = await Promise.all([
                            fetch('/api/v1/price-alerts', { headers }).then(r => {
                                if (r.status === 401) throw new Error('unauthorized');
                                return r.json();
                            }),
                            fetch('/api/v1/wishlists', { headers }).then(r => {
                                if (r.status === 401) throw new Error('unauthorized');
                                return r.json();
                            })
                        ]);

                        // Process alerts
                        const alertsData = alertsRes.data || alertsRes || [];
                        this.alerts = Array.isArray(alertsData) ? alertsData : [];

                        this.stats.activeAlerts = this.alerts.filter(a => a.is_active && !a.triggered_at).length;
                        this.stats.triggeredAlerts = this.alerts.filter(a => a.triggered_at).length;

                        // Process wishlists
                        const wishlistsData = wishlistsRes.data || wishlistsRes || [];
                        const wishlists = Array.isArray(wishlistsData) ? wishlistsData : [];

                        // Flatten all wishlist items
                        this.wishlistItems = [];
                        wishlists.forEach(wl => {
                            if (wl.items && Array.isArray(wl.items)) {
                                this.wishlistItems.push(...wl.items);
                            }
                        });
                        this.stats.wishlistCount = this.wishlistItems.length;

                    } catch (e) {
                        if (e.message === 'unauthorized') {
                            localStorage.removeItem('auth_token');
                            localStorage.removeItem('user');
                            window.location.href = '/login';
                            return;
                        }
                        this.error = 'Failed to load dashboard data. Please try again.';
                    } finally {
                        this.loading = false;
                    }
                }
            };
        }
    </script>
@endsection
