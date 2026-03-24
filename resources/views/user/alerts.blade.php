@extends('layouts.app')

@section('title', 'Price Alerts')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-teal-50 via-cyan-50 to-white py-12" x-data="{
                alerts: [],
                isLoading: true,
                error: null,
                authToken: localStorage.getItem('auth_token'),

                async fetchAlerts() {
                    if (!this.authToken) {
                        window.location.href = '/login';
                        return;
                    }
                    this.isLoading = true;
                    this.error = null;
                    try {
                        const res = await fetch('/api/v1/price-alerts', {
                            headers: { 'Authorization': `Bearer ${this.authToken}` }
                        });
                        if (!res.ok) throw new Error('Failed to load alerts');
                        const data = await res.json();
                        this.alerts = data.data || [];
                    } catch (e) {
                        this.error = 'Failed to load price alerts. Please try again.';
                    }
                    this.isLoading = false;
                },

                async toggleActive(alert) {
                    try {
                        await fetch(`/api/v1/price-alerts/${alert.id}`, {
                            method: 'PUT',
                            headers: {
                                'Authorization': `Bearer ${this.authToken}`,
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({ is_active: !alert.is_active })
                        });
                        alert.is_active = !alert.is_active;
                        if (alert.is_active) {
                            alert.triggered_at = null;
                        }
                    } catch (e) {
                        console.error(e);
                    }
                },

                async deleteAlert(alertId) {
                    if (!confirm('Delete this price alert?')) return;
                    try {
                        await fetch(`/api/v1/price-alerts/${alertId}`, {
                            method: 'DELETE',
                            headers: { 'Authorization': `Bearer ${this.authToken}` }
                        });
                        this.alerts = this.alerts.filter(a => a.id !== alertId);
                    } catch (e) {
                        console.error(e);
                    }
                },

                formatPrice(price) {
                    return parseFloat(price).toLocaleString('en-IN');
                }
             }" x-init="fetchAlerts()">

        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold font-playfair mb-4">
                    <span class="bg-gradient-to-r from-teal-600 to-teal-700 bg-clip-text text-transparent">
                        🔔 Price Alerts
                    </span>
                </h1>
                <p class="text-gray-600">Get notified when prices drop on your watched perfumes</p>
            </div>

            <!-- Error State -->
            <template x-if="!isLoading && error">
                <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-center">
                    <p class="text-red-600 text-sm" x-text="error"></p>
                    <button @click="fetchAlerts()" class="mt-2 text-sm text-red-700 font-semibold hover:underline">Try Again</button>
                </div>
            </template>

            <!-- Loading State -->
            <template x-if="isLoading">
                <div class="flex justify-center py-20">
                    <div class="animate-spin rounded-full h-12 w-12 border-4 border-teal-200 border-t-teal-600"></div>
                </div>
            </template>

            <!-- Empty State -->
            <template x-if="!isLoading && !error && alerts.length === 0">
                <div class="text-center py-20">
                    <div class="w-24 h-24 mx-auto mb-6 bg-yellow-100 rounded-full flex items-center justify-center">
                        <svg class="w-12 h-12 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-700 mb-4">No price alerts</h3>
                    <p class="text-gray-500 mb-8">Set alerts on perfumes to get notified when prices drop.</p>
                    <a href="/perfumes"
                        class="bg-gradient-to-r from-teal-500 to-teal-600 text-white px-8 py-3 rounded-2xl font-semibold hover:from-teal-600 hover:to-teal-700 transition-all">
                        Browse Perfumes
                    </a>
                </div>
            </template>

            <!-- Alerts List -->
            <template x-if="!isLoading && !error && alerts.length > 0">
                <div class="space-y-4">
                    <template x-for="alert in alerts" :key="alert.id">
                        <div class="bg-white/90 backdrop-blur-xl rounded-2xl p-6 shadow-xl border-2" :class="{
                                    'border-green-300 bg-green-50/50': alert.triggered_at,
                                    'border-teal-200': !alert.triggered_at && alert.is_active,
                                    'border-gray-200 opacity-60': !alert.is_active
                                 }">
                            <div class="flex flex-col md:flex-row md:items-center gap-4">
                                <!-- Perfume Image -->
                                <img :src="alert.perfume?.image_url || 'https://images.unsplash.com/photo-1541643600914-78b084683601?w=100'"
                                    :alt="alert.perfume?.name" class="w-20 h-20 object-cover rounded-xl flex-shrink-0">

                                <!-- Perfume Info -->
                                <div class="flex-1">
                                    <p class="text-xs text-teal-600 font-bold uppercase" x-text="alert.perfume?.brand"></p>
                                    <h4 class="text-lg font-bold text-gray-800" x-text="alert.perfume?.name"></h4>

                                    <div class="flex flex-wrap gap-4 mt-2 text-sm">
                                        <span class="text-gray-600" x-show="alert.size_ml">
                                            Size: <strong class="text-teal-700" x-text="alert.size_ml + 'ml'"></strong>
                                        </span>
                                        <span class="text-gray-600" x-show="!alert.size_ml">
                                            Size: <strong class="text-gray-500">Any</strong>
                                        </span>
                                        <span class="text-gray-600">
                                            Target: <strong class="text-teal-600">₹<span
                                                    x-text="formatPrice(alert.target_price)"></span></strong>
                                        </span>
                                        <span class="text-gray-600">
                                            Current: <strong
                                                x-text="alert.current_lowest_price ? '₹' + formatPrice(alert.current_lowest_price) : 'N/A'"></strong>
                                        </span>
                                    </div>

                                    <!-- Status Badge -->
                                    <template x-if="alert.triggered_at">
                                        <span
                                            class="inline-block mt-2 px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full">
                                            ✓ Triggered! Price dropped below target
                                        </span>
                                    </template>
                                </div>

                                <!-- Actions -->
                                <div class="flex items-center gap-3">
                                    <button @click="toggleActive(alert)"
                                        :class="alert.is_active ? 'bg-teal-500 hover:bg-teal-600' : 'bg-gray-400 hover:bg-gray-500'"
                                        class="px-4 py-2 text-white text-sm font-semibold rounded-xl transition-colors">
                                        <span x-text="alert.is_active ? 'Active' : 'Paused'"></span>
                                    </button>
                                    <a :href="`/perfumes/${alert.perfume_id}`"
                                        class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold rounded-xl transition-colors">
                                        View
                                    </a>
                                    <button @click="deleteAlert(alert.id)"
                                        class="p-2 text-red-500 hover:bg-red-50 rounded-xl transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </template>
        </div>
    </div>
@endsection