@extends('layouts.app')

@section('title', 'My Wishlist')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-rose-50 via-pink-50 to-purple-50 py-12" x-data="{
            wishlists: [],
            selectedWishlist: null,
            isLoading: true,
            showCreateModal: false,
            newWishlistName: '',
            authToken: localStorage.getItem('auth_token'),

            async fetchWishlists() {
                if (!this.authToken) {
                    window.location.href = '/login';
                    return;
                }
                this.isLoading = true;
                try {
                    const res = await fetch('/api/v1/wishlists', {
                        headers: { 'Authorization': `Bearer ${this.authToken}` }
                    });
                    const data = await res.json();
                    this.wishlists = data.data || [];
                    if (this.wishlists.length > 0 && !this.selectedWishlist) {
                        this.selectWishlist(this.wishlists[0]);
                    }
                } catch (e) {
                    console.error(e);
                }
                this.isLoading = false;
            },

            async selectWishlist(wishlist) {
                this.isLoading = true;
                try {
                    const res = await fetch(`/api/v1/wishlists/${wishlist.id}`, {
                        headers: { 'Authorization': `Bearer ${this.authToken}` }
                    });
                    const data = await res.json();
                    this.selectedWishlist = data.data;
                } catch (e) {
                    console.error(e);
                }
                this.isLoading = false;
            },

            async createWishlist() {
                if (!this.newWishlistName.trim()) return;
                try {
                    const res = await fetch('/api/v1/wishlists', {
                        method: 'POST',
                        headers: {
                            'Authorization': `Bearer ${this.authToken}`,
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ name: this.newWishlistName })
                    });
                    const data = await res.json();
                    if (res.ok) {
                        this.wishlists.push(data.data);
                        this.newWishlistName = '';
                        this.showCreateModal = false;
                        this.selectWishlist(data.data);
                    }
                } catch (e) {
                    console.error(e);
                }
            },

            async removeItem(perfumeId) {
                if (!this.selectedWishlist) return;
                try {
                    await fetch(`/api/v1/wishlists/${this.selectedWishlist.id}/items/${perfumeId}`, {
                        method: 'DELETE',
                        headers: { 'Authorization': `Bearer ${this.authToken}` }
                    });
                    this.selectedWishlist.items = this.selectedWishlist.items.filter(i => i.perfume_id !== perfumeId);
                } catch (e) {
                    console.error(e);
                }
            }
         }" x-init="fetchWishlists()">

        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold font-playfair mb-4">
                    <span class="bg-gradient-to-r from-pink-600 to-purple-600 bg-clip-text text-transparent">
                        ❤️ My Wishlists
                    </span>
                </h1>
                <p class="text-gray-600">Keep track of your favorite fragrances</p>
            </div>

            <!-- Loading State -->
            <template x-if="isLoading">
                <div class="flex justify-center py-20">
                    <div class="animate-spin rounded-full h-12 w-12 border-4 border-pink-200 border-t-pink-600"></div>
                </div>
            </template>

            <!-- Empty State -->
            <template x-if="!isLoading && wishlists.length === 0">
                <div class="text-center py-20">
                    <div class="w-24 h-24 mx-auto mb-6 bg-pink-100 rounded-full flex items-center justify-center">
                        <svg class="w-12 h-12 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-700 mb-4">No wishlists yet</h3>
                    <p class="text-gray-500 mb-8">Start adding perfumes to your wishlist by clicking the heart icon on any
                        perfume.</p>
                    <a href="/perfumes"
                        class="bg-gradient-to-r from-pink-500 to-purple-600 text-white px-8 py-3 rounded-2xl font-semibold hover:from-pink-600 hover:to-purple-700 transition-all">
                        Browse Perfumes
                    </a>
                </div>
            </template>

            <!-- Wishlists Content -->
            <template x-if="!isLoading && wishlists.length > 0">
                <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                    <!-- Sidebar: List of Wishlists -->
                    <div class="lg:col-span-1">
                        <div class="bg-white/80 backdrop-blur-xl rounded-2xl p-6 shadow-xl">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="font-bold text-gray-800">Your Lists</h3>
                                <button @click="showCreateModal = true"
                                    class="w-8 h-8 bg-pink-500 text-white rounded-full flex items-center justify-center hover:bg-pink-600 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4" />
                                    </svg>
                                </button>
                            </div>
                            <div class="space-y-2">
                                <template x-for="list in wishlists" :key="list.id">
                                    <button @click="selectWishlist(list)"
                                        :class="{'bg-pink-100 border-pink-400': selectedWishlist?.id === list.id, 'bg-gray-50 border-gray-200': selectedWishlist?.id !== list.id}"
                                        class="w-full text-left px-4 py-3 rounded-xl border-2 transition-all hover:border-pink-300">
                                        <span class="font-medium text-gray-800" x-text="list.name"></span>
                                        <span class="text-sm text-gray-500 ml-2"
                                            x-text="`(${list.items_count || 0})`"></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Main: Wishlist Items -->
                    <div class="lg:col-span-3">
                        <template x-if="selectedWishlist">
                            <div class="bg-white/80 backdrop-blur-xl rounded-2xl p-6 shadow-xl">
                                <h3 class="text-2xl font-bold text-gray-800 mb-6" x-text="selectedWishlist.name"></h3>

                                <template x-if="selectedWishlist.items && selectedWishlist.items.length === 0">
                                    <div class="text-center py-12 text-gray-500">
                                        No perfumes in this wishlist yet.
                                    </div>
                                </template>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <template x-for="item in (selectedWishlist.items || [])" :key="item.id">
                                        <div class="bg-gradient-to-r from-pink-50 to-purple-50 rounded-xl p-4 flex gap-4">
                                            <img :src="item.perfume?.image_url || 'https://images.unsplash.com/photo-1541643600914-78b084683601?w=100'"
                                                :alt="item.perfume?.name" class="w-20 h-20 object-cover rounded-lg">
                                            <div class="flex-1">
                                                <p class="text-xs text-pink-600 font-bold" x-text="item.perfume?.brand"></p>
                                                <h4 class="font-semibold text-gray-800" x-text="item.perfume?.name"></h4>
                                                <p class="text-sm text-gray-500" x-text="item.notes || 'No notes'"></p>
                                                <div class="mt-2 flex gap-2">
                                                    <a :href="`/perfumes/${item.perfume_id}`"
                                                        class="text-xs text-pink-600 hover:underline">View</a>
                                                    <button @click="removeItem(item.perfume_id)"
                                                        class="text-xs text-red-500 hover:underline">Remove</button>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </template>

            <!-- Create Wishlist Modal -->
            <div x-show="showCreateModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
                <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4 shadow-2xl">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Create New Wishlist</h3>
                    <input type="text" x-model="newWishlistName" placeholder="Wishlist name..."
                        class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-pink-400 mb-4">
                    <div class="flex gap-4">
                        <button @click="createWishlist()"
                            class="flex-1 bg-gradient-to-r from-pink-500 to-purple-600 text-white py-3 rounded-xl font-semibold">
                            Create
                        </button>
                        <button @click="showCreateModal = false"
                            class="flex-1 bg-gray-200 text-gray-700 py-3 rounded-xl font-semibold">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection