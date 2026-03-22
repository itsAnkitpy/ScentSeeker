<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - @yield('title')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Chart.js for price history charts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="font-inter antialiased bg-gradient-to-br from-teal-50 via-cyan-50 to-white text-gray-900">
    <div class="min-h-screen flex flex-col">
        <header class="bg-white/80 backdrop-blur-lg shadow-sm border-b border-white/20 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center" x-data="{ hasToken: !!localStorage.getItem('auth_token') }">
                        <a :href="hasToken ? '/dashboard' : '/'">
                            <img src="/images/logo2.png" alt="ScentCents" class="h-14 object-contain">
                        </a>
                    </div>

                    @unless(View::hasSection('hide-nav'))
                    <nav class="hidden md:flex items-center space-x-8" x-data="{
                        isLoggedIn: !!localStorage.getItem('auth_token')
                    }">
                        <a :href="isLoggedIn ? '/dashboard' : '/'" class="text-gray-700 hover:text-teal-600 font-medium transition-colors">Home</a>
                        <a href="{{ route('perfumes.index') }}"
                            class="text-gray-700 hover:text-teal-600 font-medium transition-colors">Browse</a>
                    </nav>
                    @endunless

                    <div class="flex items-center space-x-4">
                        <button class="text-gray-600 hover:text-teal-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>

                        <!-- Auth Links -->
                        <div class="hidden md:flex items-center space-x-3" x-data="{
                                 user: null,
                                 isOpen: false,
                                 init() {
                                     const userJson = localStorage.getItem('user');
                                     if (userJson) {
                                         try {
                                             this.user = JSON.parse(userJson);
                                         } catch (e) {
                                             this.user = null;
                                         }
                                     }
                                 },
                                 logout() {
                                     const token = localStorage.getItem('auth_token');
                                     // Clear localStorage first
                                     localStorage.removeItem('auth_token');
                                     localStorage.removeItem('user');
                                     this.user = null;
                                     // Call logout API (fire and forget)
                                     if (token) {
                                         fetch('/api/v1/logout', {
                                             method: 'POST',
                                             headers: {
                                                 'Accept': 'application/json',
                                                 'Authorization': 'Bearer ' + token
                                             }
                                         }).catch(() => {});
                                     }
                                     window.location.href = '/';
                                 }
                             }">
                            <!-- Guest Links -->
                            <template x-if="!user">
                                <div class="flex items-center space-x-3">
                                    <a href="{{ route('login') }}"
                                        class="text-gray-600 hover:text-teal-600 font-medium transition-colors">
                                        Login
                                    </a>
                                    <a href="{{ route('register') }}"
                                        class="bg-gradient-to-r from-teal-500 to-teal-600 text-white px-4 py-2 rounded-lg font-medium hover:from-teal-600 hover:to-teal-700 transition-all transform hover:scale-105">
                                        Sign Up
                                    </a>
                                </div>
                            </template>

                            <!-- Authenticated User Dropdown -->
                            <template x-if="user">
                                <div class="relative">
                                    <button @click="isOpen = !isOpen" @click.outside="isOpen = false"
                                        class="flex items-center space-x-2 bg-gradient-to-r from-teal-50 to-cyan-50 hover:from-teal-100 hover:to-cyan-100 px-4 py-2 rounded-xl border border-teal-200 transition-all">
                                        <div
                                            class="w-8 h-8 bg-gradient-to-r from-teal-500 to-teal-600 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                                            <span
                                                x-text="user.username ? user.username.charAt(0).toUpperCase() : 'U'"></span>
                                        </div>
                                        <span class="text-gray-700 font-medium" x-text="user.username || 'User'"></span>
                                        <svg class="w-4 h-4 text-gray-500 transition-transform"
                                            :class="{'rotate-180': isOpen}" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>

                                    <!-- Dropdown Menu -->
                                    <div x-show="isOpen" x-transition:enter="transition ease-out duration-100"
                                        x-transition:enter-start="transform opacity-0 scale-95"
                                        x-transition:enter-end="transform opacity-100 scale-100"
                                        x-transition:leave="transition ease-in duration-75"
                                        x-transition:leave-start="transform opacity-100 scale-100"
                                        x-transition:leave-end="transform opacity-0 scale-95"
                                        class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-2 z-50">
                                        <div class="px-4 py-2 border-b border-gray-100">
                                            <p class="text-sm font-medium text-gray-900" x-text="user.username"></p>
                                            <p class="text-xs text-gray-500" x-text="user.email"></p>
                                        </div>
                                        <a href="/dashboard"
                                            class="flex items-center px-4 py-2 text-gray-700 hover:bg-teal-50 hover:text-teal-600 transition-colors">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                            </svg>
                                            Dashboard
                                        </a>
                                        <a href="/wishlist"
                                            class="flex items-center px-4 py-2 text-gray-700 hover:bg-teal-50 hover:text-teal-600 transition-colors">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                            </svg>
                                            Wishlist
                                        </a>
                                        <a href="/alerts"
                                            class="flex items-center px-4 py-2 text-gray-700 hover:bg-teal-50 hover:text-teal-600 transition-colors">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                            </svg>
                                            Price Alerts
                                        </a>
                                        <div class="border-t border-gray-100 mt-2 pt-2">
                                            <button @click="logout()"
                                                class="flex items-center w-full px-4 py-2 text-red-600 hover:bg-red-50 transition-colors">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                                </svg>
                                                Logout
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Mobile menu button -->
                        <div class="md:hidden">
                            <button type="button" class="text-gray-600 hover:text-teal-600 transition-colors" x-data
                                x-on:click="$refs.mobileMenu.classList.toggle('hidden')">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Mobile menu -->
                <div class="md:hidden hidden" x-ref="mobileMenu">
                    <div class="px-2 pt-2 pb-3 space-y-1 bg-white/90 backdrop-blur-lg rounded-lg mt-2 border border-white/20"
                        x-data="{
                             user: null,
                             init() {
                                 const userJson = localStorage.getItem('user');
                                 if (userJson) {
                                     try {
                                         this.user = JSON.parse(userJson);
                                     } catch (e) {
                                         this.user = null;
                                     }
                                 }
                             },
                             logout() {
                                 const token = localStorage.getItem('auth_token');
                                 localStorage.removeItem('auth_token');
                                 localStorage.removeItem('user');
                                 this.user = null;
                                 if (token) {
                                     fetch('/api/v1/logout', {
                                         method: 'POST',
                                         headers: {
                                             'Accept': 'application/json',
                                             'Authorization': 'Bearer ' + token
                                         }
                                     }).catch(() => {});
                                 }
                                 window.location.href = '/';
                             }
                         }">
                        @unless(View::hasSection('hide-nav'))
                        <a :href="user ? '/dashboard' : '/'" class="block px-3 py-2 text-gray-700 hover:text-teal-600 font-medium">Home</a>
                        <a href="{{ route('perfumes.index') }}"
                            class="block px-3 py-2 text-gray-700 hover:text-teal-600 font-medium">Browse</a>
                        @endunless

                        <div class="border-t border-gray-200 pt-2 mt-2">
                            <!-- Guest Links -->
                            <template x-if="!user">
                                <div>
                                    <a href="{{ route('login') }}"
                                        class="block px-3 py-2 text-gray-700 hover:text-teal-600 font-medium">Login</a>
                                    <a href="{{ route('register') }}"
                                        class="block px-3 py-2 bg-gradient-to-r from-teal-500 to-teal-600 text-white rounded-lg font-medium mx-3 text-center mt-2">Sign
                                        Up</a>
                                </div>
                            </template>

                            <!-- Authenticated User -->
                            <template x-if="user">
                                <div>
                                    <div class="flex items-center px-3 py-2 mb-2">
                                        <div
                                            class="w-8 h-8 bg-gradient-to-r from-teal-500 to-teal-600 rounded-full flex items-center justify-center text-white font-semibold text-sm mr-2">
                                            <span
                                                x-text="user.username ? user.username.charAt(0).toUpperCase() : 'U'"></span>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900" x-text="user.username"></p>
                                            <p class="text-xs text-gray-500" x-text="user.email"></p>
                                        </div>
                                    </div>
                                    <a href="/dashboard"
                                        class="block px-3 py-2 text-gray-700 hover:text-teal-600 font-medium">Dashboard</a>
                                    <a href="/wishlist"
                                        class="block px-3 py-2 text-gray-700 hover:text-teal-600 font-medium">Wishlist</a>
                                    <a href="/alerts"
                                        class="block px-3 py-2 text-gray-700 hover:text-teal-600 font-medium">Price
                                        Alerts</a>
                                    <button @click="logout()"
                                        class="w-full text-left block px-3 py-2 text-red-600 hover:bg-red-50 font-medium rounded-lg mt-2">
                                        Logout
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-grow">
            @yield('content')
        </main>

        <footer class="bg-white/50 backdrop-blur-lg border-t border-white/20 mt-auto">
            <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div class="md:col-span-2">
                        <div class="mb-4">
                            <img src="/images/logo2.png" alt="ScentCents" class="h-16 object-contain">
                        </div>
                        <p class="text-gray-600 mb-4 max-w-md">
                            Your ultimate destination for comparing perfume prices and finding the best deals on luxury
                            fragrances from top brands worldwide.
                        </p>
                        <div class="flex space-x-4">
                            <a href="#" class="text-gray-400 hover:text-teal-500 transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z" />
                                </svg>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-teal-500 transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M22.46 6c-.77.35-1.6.58-2.46.69.88-.53 1.56-1.37 1.88-2.38-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29 0 .34.04.67.11.98C8.28 9.09 5.11 7.38 3 4.79c-.37.63-.58 1.37-.58 2.15 0 1.49.75 2.81 1.91 3.56-.71 0-1.37-.2-1.95-.5v.03c0 2.08 1.48 3.82 3.44 4.21a4.22 4.22 0 0 1-1.93.07 4.28 4.28 0 0 0 4 2.98 8.521 8.521 0 0 1-5.33 1.84c-.34 0-.68-.02-1.02-.06C3.44 20.29 5.7 21 8.12 21 16 21 20.33 14.46 20.33 8.79c0-.19 0-.37-.01-.56.84-.6 1.56-1.36 2.14-2.23z" />
                                </svg>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-teal-500 transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.174-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.72-.359-1.781c0-1.663.967-2.911 2.168-2.911 1.024 0 1.518.769 1.518 1.688 0 1.029-.653 2.567-.992 3.992-.285 1.193.6 2.165 1.775 2.165 2.128 0 3.768-2.245 3.768-5.487 0-2.861-2.063-4.869-5.008-4.869-3.41 0-5.409 2.562-5.409 5.199 0 1.033.394 2.143.889 2.741.099.12.112.225.085.345-.09.375-.293 1.199-.334 1.363-.053.225-.172.271-.402.165-1.495-.69-2.433-2.878-2.433-4.646 0-3.776 2.748-7.252 7.92-7.252 4.158 0 7.392 2.967 7.392 6.923 0 4.135-2.607 7.462-6.233 7.462-1.214 0-2.357-.629-2.746-1.378l-.747 2.848c-.269 1.045-1.004 2.352-1.498 3.146 1.123.345 2.306.535 3.55.535 6.624 0 11.99-5.367 11.99-11.986C24.007 5.367 18.641.001 12.017.001z" />
                                </svg>
                            </a>
                        </div>
                    </div>

                    <div>
                        <h3 class="font-semibold text-gray-900 mb-4">Quick Links</h3>
                        <ul class="space-y-2 text-sm">
                            <li><a href="#" class="text-gray-600 hover:text-teal-600 transition-colors">Browse
                                    Perfumes</a></li>
                            <li><a href="#" class="text-gray-600 hover:text-teal-600 transition-colors">Price
                                    Comparison</a></li>
                            <li><a href="#" class="text-gray-600 hover:text-teal-600 transition-colors">Best Deals</a>
                            </li>
                            <li><a href="#" class="text-gray-600 hover:text-teal-600 transition-colors">New Arrivals</a>
                            </li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="font-semibold text-gray-900 mb-4">Support</h3>
                        <ul class="space-y-2 text-sm">
                            <li><a href="#" class="text-gray-600 hover:text-teal-600 transition-colors">Help Center</a>
                            </li>
                            <li><a href="#" class="text-gray-600 hover:text-teal-600 transition-colors">Contact Us</a>
                            </li>
                            <li><a href="#" class="text-gray-600 hover:text-teal-600 transition-colors">Privacy
                                    Policy</a></li>
                            <li><a href="#" class="text-gray-600 hover:text-teal-600 transition-colors">Terms of
                                    Service</a></li>
                        </ul>
                    </div>
                </div>

                <div class="border-t border-gray-200 mt-8 pt-8 text-center text-sm text-gray-600">
                    &copy; {{ date('Y') }} ScentCents. All rights reserved.
                </div>
            </div>
        </footer>
    </div>
</body>

</html>