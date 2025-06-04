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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-inter antialiased bg-gradient-to-br from-rose-50 via-pink-50 to-purple-50 text-gray-900">
    <div class="min-h-screen flex flex-col">
        <header class="bg-white/80 backdrop-blur-lg shadow-sm border-b border-white/20 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center">
                        <a href="/" class="flex items-center space-x-2">
                            <div class="w-8 h-8 bg-gradient-to-r from-pink-500 to-purple-600 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2L13.09 8.26L20 9L13.09 9.74L12 16L10.91 9.74L4 9L10.91 8.26L12 2Z"/>
                                </svg>
                            </div>
                            <span class="text-xl font-bold bg-gradient-to-r from-pink-600 to-purple-600 bg-clip-text text-transparent">
                                {{ config('app.name', 'ScentSeeker') }}
                            </span>
                        </a>
                    </div>
                    
                    <nav class="hidden md:flex items-center space-x-8">
                        <a href="/" class="text-gray-700 hover:text-pink-600 font-medium transition-colors">Home</a>
                        <a href="{{ route('perfumes.index') }}" class="text-gray-700 hover:text-pink-600 font-medium transition-colors">Browse</a>
                        <a href="#" class="text-gray-700 hover:text-pink-600 font-medium transition-colors">Compare</a>
                        <a href="#" class="text-gray-700 hover:text-pink-600 font-medium transition-colors">Deals</a>
                    </nav>
                    
                    <div class="flex items-center space-x-4">
                        <button class="text-gray-600 hover:text-pink-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </button>
                        
                        <!-- Auth Links -->
                        <div class="hidden md:flex items-center space-x-3">
                            <a href="{{ route('login') }}" class="text-gray-600 hover:text-pink-600 font-medium transition-colors">
                                Login
                            </a>
                            <a href="{{ route('register') }}" class="bg-gradient-to-r from-pink-500 to-purple-600 text-white px-4 py-2 rounded-lg font-medium hover:from-pink-600 hover:to-purple-700 transition-all transform hover:scale-105">
                                Sign Up
                            </a>
                        </div>
                        
                        <!-- Mobile menu button -->
                        <div class="md:hidden">
                            <button type="button" class="text-gray-600 hover:text-pink-600 transition-colors" x-data x-on:click="$refs.mobileMenu.classList.toggle('hidden')">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Mobile menu -->
                <div class="md:hidden hidden" x-ref="mobileMenu">
                    <div class="px-2 pt-2 pb-3 space-y-1 bg-white/90 backdrop-blur-lg rounded-lg mt-2 border border-white/20">
                        <a href="/" class="block px-3 py-2 text-gray-700 hover:text-pink-600 font-medium">Home</a>
                        <a href="{{ route('perfumes.index') }}" class="block px-3 py-2 text-gray-700 hover:text-pink-600 font-medium">Browse</a>
                        <a href="#" class="block px-3 py-2 text-gray-700 hover:text-pink-600 font-medium">Compare</a>
                        <a href="#" class="block px-3 py-2 text-gray-700 hover:text-pink-600 font-medium">Deals</a>
                        <div class="border-t border-gray-200 pt-2 mt-2">
                            <a href="{{ route('login') }}" class="block px-3 py-2 text-gray-700 hover:text-pink-600 font-medium">Login</a>
                            <a href="{{ route('register') }}" class="block px-3 py-2 bg-gradient-to-r from-pink-500 to-purple-600 text-white rounded-lg font-medium mx-3 text-center">Sign Up</a>
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
                        <div class="flex items-center space-x-2 mb-4">
                            <div class="w-8 h-8 bg-gradient-to-r from-pink-500 to-purple-600 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2L13.09 8.26L20 9L13.09 9.74L12 16L10.91 9.74L4 9L10.91 8.26L12 2Z"/>
                                </svg>
                            </div>
                            <span class="text-xl font-bold bg-gradient-to-r from-pink-600 to-purple-600 bg-clip-text text-transparent">
                                {{ config('app.name', 'ScentSeeker') }}
                            </span>
                        </div>
                        <p class="text-gray-600 mb-4 max-w-md">
                            Your ultimate destination for comparing perfume prices and finding the best deals on luxury fragrances from top brands worldwide.
                        </p>
                        <div class="flex space-x-4">
                            <a href="#" class="text-gray-400 hover:text-pink-500 transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-pink-500 transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M22.46 6c-.77.35-1.6.58-2.46.69.88-.53 1.56-1.37 1.88-2.38-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29 0 .34.04.67.11.98C8.28 9.09 5.11 7.38 3 4.79c-.37.63-.58 1.37-.58 2.15 0 1.49.75 2.81 1.91 3.56-.71 0-1.37-.2-1.95-.5v.03c0 2.08 1.48 3.82 3.44 4.21a4.22 4.22 0 0 1-1.93.07 4.28 4.28 0 0 0 4 2.98 8.521 8.521 0 0 1-5.33 1.84c-.34 0-.68-.02-1.02-.06C3.44 20.29 5.7 21 8.12 21 16 21 20.33 14.46 20.33 8.79c0-.19 0-.37-.01-.56.84-.6 1.56-1.36 2.14-2.23z"/></svg>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-pink-500 transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.174-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.72-.359-1.781c0-1.663.967-2.911 2.168-2.911 1.024 0 1.518.769 1.518 1.688 0 1.029-.653 2.567-.992 3.992-.285 1.193.6 2.165 1.775 2.165 2.128 0 3.768-2.245 3.768-5.487 0-2.861-2.063-4.869-5.008-4.869-3.41 0-5.409 2.562-5.409 5.199 0 1.033.394 2.143.889 2.741.099.12.112.225.085.345-.09.375-.293 1.199-.334 1.363-.053.225-.172.271-.402.165-1.495-.69-2.433-2.878-2.433-4.646 0-3.776 2.748-7.252 7.92-7.252 4.158 0 7.392 2.967 7.392 6.923 0 4.135-2.607 7.462-6.233 7.462-1.214 0-2.357-.629-2.746-1.378l-.747 2.848c-.269 1.045-1.004 2.352-1.498 3.146 1.123.345 2.306.535 3.55.535 6.624 0 11.99-5.367 11.99-11.986C24.007 5.367 18.641.001 12.017.001z"/></svg>
                            </a>
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="font-semibold text-gray-900 mb-4">Quick Links</h3>
                        <ul class="space-y-2 text-sm">
                            <li><a href="#" class="text-gray-600 hover:text-pink-600 transition-colors">Browse Perfumes</a></li>
                            <li><a href="#" class="text-gray-600 hover:text-pink-600 transition-colors">Price Comparison</a></li>
                            <li><a href="#" class="text-gray-600 hover:text-pink-600 transition-colors">Best Deals</a></li>
                            <li><a href="#" class="text-gray-600 hover:text-pink-600 transition-colors">New Arrivals</a></li>
                        </ul>
                    </div>
                    
                    <div>
                        <h3 class="font-semibold text-gray-900 mb-4">Support</h3>
                        <ul class="space-y-2 text-sm">
                            <li><a href="#" class="text-gray-600 hover:text-pink-600 transition-colors">Help Center</a></li>
                            <li><a href="#" class="text-gray-600 hover:text-pink-600 transition-colors">Contact Us</a></li>
                            <li><a href="#" class="text-gray-600 hover:text-pink-600 transition-colors">Privacy Policy</a></li>
                            <li><a href="#" class="text-gray-600 hover:text-pink-600 transition-colors">Terms of Service</a></li>
                        </ul>
                    </div>
                </div>
                
                <div class="border-t border-gray-200 mt-8 pt-8 text-center text-sm text-gray-600">
                    &copy; {{ date('Y') }} {{ config('app.name', 'ScentSeeker') }}. All rights reserved.
                </div>
            </div>
        </footer>
    </div>
</body>
</html>