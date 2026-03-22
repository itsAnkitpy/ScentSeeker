<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description"
        content="Compare perfume prices from 50+ verified sellers including Reddit's most trusted fragrance sellers. Track prices, get alerts, and never overpay for perfumes again.">

    <title>ScentCents - Stop Overpaying for Perfumes | Compare Prices from 50+ Sellers</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            /* Premium Teal/Emerald Palette */
            --color-primary: #0F766E; /* teal-700 */
            --color-primary-light: #14B8A6; /* teal-500 */
            --color-primary-dark: #115E59; /* teal-800 */
            
            /* Luxury Accent */
            --color-accent: #F59E0B; /* amber-500 */
            --color-accent-light: #FCD34D; /* amber-300 */
            
            /* Neutral/Glass */
            --glass-border: rgba(255, 255, 255, 0.4);
            --glass-bg: rgba(255, 255, 255, 0.7);
            --glass-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07);
        }

        .font-inter {
            font-family: 'Inter', sans-serif;
        }

        /* Smooth scroll */
        html {
            scroll-behavior: smooth;
        }

        /* Premium Gradient Text */
        .gradient-text {
            background: linear-gradient(135deg, var(--color-primary-dark) 0%, var(--color-primary-light) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .gradient-text-gold {
            background: linear-gradient(135deg, #B45309 0%, #F59E0B 50%, #D97706 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Glassmorphism Utilities */
        .glass-panel {
            background: var(--glass-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid var(--glass-border);
            box-shadow: var(--glass-shadow);
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.6);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .glass-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 20px -3px rgba(0, 0, 0, 0.08);
            border-color: rgba(20, 184, 166, 0.3); /* teal-500/30 */
        }

        /* Primary Button - Premium Feel */
        .btn-primary {
            background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 100%);
            box-shadow: 0 4px 14px rgba(13, 148, 136, 0.3);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .btn-primary::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: 0.5s;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(13, 148, 136, 0.4);
        }
        
        .btn-primary:hover::after {
            left: 100%;
        }

        /* Secondary/Accent button */
        .btn-accent {
            background: linear-gradient(135deg, var(--color-accent) 0%, #D97706 100%);
            box-shadow: 0 4px 14px rgba(245, 158, 11, 0.3);
            transition: all 0.3s ease;
        }

        .btn-accent:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(245, 158, 11, 0.4);
        }

        /* Hero background */
        .hero-bg {
            background-color: #F0FDFA; /* teal-50 */
            background-image: 
                radial-gradient(at 0% 0%, rgba(252, 211, 77, 0.15) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(20, 184, 166, 0.15) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(252, 211, 77, 0.1) 0px, transparent 50%),
                radial-gradient(at 0% 100%, rgba(20, 184, 166, 0.1) 0px, transparent 50%);
        }
        
        /* Mesh Grid Background for Texture */
        .mesh-grid {
            background-size: 40px 40px;
            background-image: linear-gradient(to right, rgba(13, 148, 136, 0.03) 1px, transparent 1px),
                              linear-gradient(to bottom, rgba(13, 148, 136, 0.03) 1px, transparent 1px);
        }

        /* CTA section gradient */
        .cta-gradient {
            background: linear-gradient(135deg, #0F766E 0%, #115E59 100%);
            position: relative;
            overflow: hidden;
        }
        
        .cta-gradient::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at top right, rgba(252, 211, 77, 0.1), transparent 60%);
        }

        /* Animations */
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
        
        .animate-float {
            animation: float 4s ease-in-out infinite;
        }
        
        .animate-float-delayed {
            animation: float 4s ease-in-out infinite 2s;
        }

        @keyframes pulse-ring {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(20, 184, 166, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(20, 184, 166, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(20, 184, 166, 0); }
        }
        
        .pulse-ring {
            animation: pulse-ring 2s infinite;
        }
    </style>
</head>

<body class="font-inter antialiased text-gray-900">

    <!-- ============================================ -->
    <!-- NAVIGATION -->
    <!-- ============================================ -->
    <header class="fixed top-0 left-0 right-0 z-50 transition-all duration-300" 
        x-data="{ scrolled: false }" 
        @scroll.window="scrolled = (window.pageYOffset > 20)"
        :class="{ 'glass-panel py-2': scrolled, 'bg-transparent py-4': !scrolled }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16" x-data="{
                user: null,
                mobileMenuOpen: false,
                dropdownOpen: false,
                init() {
                    const userJson = localStorage.getItem('user');
                    if (userJson) {
                        try { this.user = JSON.parse(userJson); } catch (e) { this.user = null; }
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
                            headers: { 'Accept': 'application/json', 'Authorization': 'Bearer ' + token }
                        }).catch(() => {});
                    }
                    window.location.href = '/';
                }
            }">
                <!-- Logo -->
                <a href="/" class="group">
                    <img src="/images/logo2.png" alt="ScentCents" class="h-14 object-contain group-hover:opacity-90 transition-all duration-300">
                </a>
                
                <!-- Desktop Navigation -->
                <nav class="hidden md:flex items-center space-x-8">
                    <!-- Guest Navigation -->
                    <template x-if="!user">
                        <div class="flex items-center space-x-6">
                            <a href="{{ route('login') }}" class="text-gray-600 hover:text-teal-700 font-medium transition-colors text-sm">
                                Login
                            </a>
                            <a href="{{ route('register') }}" class="text-gray-600 hover:text-teal-700 font-medium transition-colors text-sm">
                                Register
                            </a>
                            <a href="{{ route('perfumes.index') }}" class="btn-primary text-white px-6 py-2.5 rounded-full font-semibold text-sm shadow-xl shadow-teal-500/20">
                                Browse Perfumes
                            </a>
                        </div>
                    </template>
                    
                    <!-- Authenticated Navigation -->
                    <template x-if="user">
                        <div class="flex items-center space-x-6">
                            <a href="/wishlist" class="text-gray-600 hover:text-teal-700 font-medium transition-colors text-sm flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                Wishlist
                            </a>
                            <a href="/alerts" class="text-gray-600 hover:text-teal-700 font-medium transition-colors text-sm flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                My Alerts
                            </a>
                            <a href="{{ route('perfumes.index') }}" class="btn-primary text-white px-6 py-2.5 rounded-full font-semibold text-sm shadow-xl shadow-teal-500/20">
                                Browse Perfumes
                            </a>
                            <div class="relative">
                                <button @click="dropdownOpen = !dropdownOpen" class="flex items-center space-x-2 text-gray-600 hover:text-teal-600 group">
                                    <div class="w-9 h-9 bg-gradient-to-br from-teal-500 to-teal-700 rounded-full flex items-center justify-center text-white font-semibold text-sm ring-2 ring-transparent group-hover:ring-teal-200 transition-all">
                                        <span x-text="user.username ? user.username.charAt(0).toUpperCase() : 'U'"></span>
                                    </div>
                                </button>
                                <div x-show="dropdownOpen" @click.outside="dropdownOpen = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95"
                                     class="absolute right-0 mt-3 w-56 bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden z-50">
                                    <div class="px-6 py-4 border-b border-gray-50 bg-gray-50/50">
                                        <p class="text-sm font-semibold text-gray-900" x-text="user.username"></p>
                                        <p class="text-xs text-gray-500 truncate" x-text="user.email"></p>
                                    </div>
                                    <button @click="logout()" class="w-full text-left px-6 py-3 text-red-600 hover:bg-red-50 text-sm font-medium transition-colors flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                        Sign Out
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </nav>
                
                <!-- Mobile Menu Button -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden text-gray-600 hover:text-teal-600 p-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        <path x-show="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <!-- Mobile Menu -->
            <div x-show="mobileMenuOpen" x-collapse class="md:hidden bg-white/95 backdrop-blur-md rounded-2xl border border-gray-100 shadow-xl overflow-hidden mb-4 absolute top-16 left-4 right-4" x-data="{
                user: null,
                init() {
                    const userJson = localStorage.getItem('user');
                    if (userJson) {
                        try { this.user = JSON.parse(userJson); } catch (e) { this.user = null; }
                    }
                },
                logout() {
                    const token = localStorage.getItem('auth_token');
                    localStorage.removeItem('auth_token');
                    localStorage.removeItem('user');
                    if (token) {
                        fetch('/api/v1/logout', { method: 'POST', headers: { 'Accept': 'application/json', 'Authorization': 'Bearer ' + token } }).catch(() => {});
                    }
                    window.location.href = '/';
                }
            }">
                <div class="p-4 space-y-3">
                    <template x-if="!user">
                        <div class="flex flex-col space-y-2">
                            <a href="{{ route('login') }}" class="px-4 py-3 text-gray-600 hover:text-teal-700 hover:bg-teal-50 rounded-xl font-medium transition-colors">Login</a>
                            <a href="{{ route('register') }}" class="px-4 py-3 text-gray-600 hover:text-teal-700 hover:bg-teal-50 rounded-xl font-medium transition-colors">Register</a>
                            <a href="{{ route('perfumes.index') }}" class="mt-2 btn-primary text-white px-5 py-3 rounded-xl font-semibold text-center">
                                Browse Perfumes
                            </a>
                        </div>
                    </template>
                    <template x-if="user">
                        <div class="flex flex-col space-y-2">
                            <div class="px-4 py-2 border-b border-gray-100 mb-2">
                                <p class="text-sm font-semibold text-teal-800">Hi, <span x-text="user.username"></span></p>
                            </div>
                            <a href="/wishlist" class="px-4 py-3 text-gray-600 hover:text-teal-700 hover:bg-teal-50 rounded-xl font-medium transition-colors">Wishlist</a>
                            <a href="/alerts" class="px-4 py-3 text-gray-600 hover:text-teal-700 hover:bg-teal-50 rounded-xl font-medium transition-colors">My Alerts</a>
                            <a href="{{ route('perfumes.index') }}" class="mt-2 btn-primary text-white px-5 py-3 rounded-xl font-semibold text-center">
                                Browse Perfumes
                            </a>
                            <button @click="logout()" class="mt-2 w-full text-left px-4 py-3 text-red-600 hover:bg-red-50 rounded-xl font-medium transition-colors">
                                Sign Out
                            </button>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </header>

    <main>
        <!-- ============================================ -->
        <!-- SECTION 1: HERO (Above the Fold) -->
        <!-- ============================================ -->
        <section class="hero-bg pt-32 pb-20 lg:pt-40 lg:pb-32 relative overflow-hidden mesh-grid">
            <!-- Decorative elements (Glass balls) -->
            <div class="absolute top-20 right-0 lg:right-20 w-72 h-72 bg-teal-300 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-float"></div>
            <div class="absolute bottom-20 -left-10 lg:left-10 w-72 h-72 bg-amber-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-float-delayed"></div>

            <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center z-10">
                <!-- Social Proof Badge -->
                <div class="inline-flex items-center gap-2 bg-white/60 backdrop-blur-md border border-teal-100 rounded-full px-4 py-1.5 mb-8 shadow-sm animate-fade-in-up">
                    <span class="flex h-2 w-2 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-teal-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-teal-500"></span>
                    </span>
                    <span class="text-sm font-medium text-teal-800">Trusted by 12,000+ Fragrance Lovers</span>
                </div>

                <!-- Headline -->
                <h1 class="text-5xl sm:text-6xl lg:text-7xl font-bold text-gray-900 mb-8 leading-tight tracking-tight">
                    Stop Overpaying <br class="hidden sm:block" /> for <span class="gradient-text">Signatures Scents</span>
                </h1>

                <!-- Subheadline -->
                <p class="text-xl text-gray-600 mb-12 max-w-2xl mx-auto leading-relaxed font-light">
                    Compare prices from <span class="font-semibold text-teal-700">50+ verified sellers</span>—including Reddit's most trusted. 
                    Track prices. Get alerts. <span class="text-amber-600 font-medium">Never pay retail again.</span>
                </p>

                <!-- Search Box -->
                <form action="{{ route('perfumes.index') }}" method="GET" class="max-w-2xl mx-auto mb-16 relative z-20">
                    <div class="flex flex-col sm:flex-row gap-2 bg-white p-2 rounded-2xl shadow-2xl shadow-teal-900/10 border border-gray-100 ring-4 ring-white/50 backdrop-blur-sm">
                        <div class="flex-1 relative group">
                            <span class="absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-teal-500 transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </span>
                            <input type="text" name="search" placeholder="Search perfume (e.g., Dior Sauvage Elixir)"
                                class="w-full pl-14 pr-4 py-4 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none text-lg bg-gray-50/50 focus:bg-white transition-all border-none focus:ring-0">
                        </div>
                        <button type="submit"
                            class="btn-primary text-white px-10 py-4 rounded-xl font-bold text-lg whitespace-nowrap shadow-lg shadow-teal-600/20 active:scale-95 transition-transform">
                            Find Best Price
                        </button>
                    </div>
                    <!-- Trending searches -->
                    <div class="mt-4 text-sm text-gray-500 flex justify-center gap-3 items-center">
                        <span class="opacity-75">Trending:</span>
                        <a href="{{ route('perfumes.index', ['search' => 'Rasasi Hawas']) }}" class="hover:text-teal-600 underline decoration-teal-300/50 hover:decoration-teal-500 decoration-2 underline-offset-4 transition-all">Rasasi Hawas</a>
                        <a href="{{ route('perfumes.index', ['search' => 'Club de Nuit']) }}" class="hidden sm:inline hover:text-teal-600 underline decoration-teal-300/50 hover:decoration-teal-500 decoration-2 underline-offset-4 transition-all">Club de Nuit</a>
                        <a href="{{ route('perfumes.index', ['search' => 'Versace Eros']) }}" class="hover:text-teal-600 underline decoration-teal-300/50 hover:decoration-teal-500 decoration-2 underline-offset-4 transition-all">Versace Eros</a>
                    </div>
                </form>

                <!-- Trust Bar -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 max-w-4xl mx-auto border-t border-gray-200/60 pt-8">
                    <div class="flex flex-col items-center">
                        <span class="text-3xl font-bold text-teal-700">₹24L+</span>
                        <span class="text-sm text-gray-500 font-medium">User Savings Generated</span>
                    </div>
                    <div class="flex flex-col items-center border-x-0 sm:border-x border-gray-200/60">
                        <span class="text-3xl font-bold text-teal-700">50+</span>
                        <span class="text-sm text-gray-500 font-medium">Verified Community Sellers</span>
                    </div>
                    <div class="flex flex-col items-center">
                        <span class="text-3xl font-bold text-teal-700">100%</span>
                        <span class="text-sm text-gray-500 font-medium">Authenticity Vetted</span>
                    </div>
                </div>
            </div>
            
            <!-- Bottom Fade -->
            <div class="absolute bottom-0 left-0 right-0 h-24 bg-gradient-to-t from-gray-50 to-transparent"></div>
        </section>

        <!-- ============================================ -->
        <!-- SECTION 2: PROBLEM / AGITATION -->
        <!-- ============================================ -->
        <section class="py-20 lg:py-32 bg-white relative">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Section Header -->
                <div class="text-center mb-16 lg:mb-20">
                    <span class="text-amber-600 font-bold tracking-wider uppercase text-sm mb-2 block">The Problem</span>
                    <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-900 mb-6">
                        Tired of the Perfume <br/><span class="text-teal-700">Price Hunt?</span>
                    </h2>
                    <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                        Finding the best deal shouldn't feel like a full-time job. We've all been there.
                    </p>
                </div>

                <!-- Pain Point Cards - Modern Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                    <!-- Pain Point 1 -->
                    <div class="group p-8 rounded-3xl bg-gray-50 border border-gray-100 hover:bg-white hover:shadow-xl hover:shadow-gray-200/50 transition-all duration-300 relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-red-50 rounded-bl-full -mr-16 -mt-16 transition-transform group-hover:scale-110"></div>
                        <div class="w-14 h-14 bg-white rounded-2xl shadow-sm text-2xl flex items-center justify-center mb-6 relative z-10 group-hover:scale-110 transition-transform duration-300">🔍</div>
                        <h3 class="text-xl font-bold text-gray-900 mb-4 relative z-10">Endless Tab Switching</h3>
                        <p class="text-gray-600 leading-relaxed relative z-10">
                            Jumping between 10+ websites just to find who has the best price. Copy-paste. Compare.
                            Repeat. There goes your evening.
                        </p>
                    </div>

                    <!-- Pain Point 2 -->
                    <div class="group p-8 rounded-3xl bg-gray-50 border border-gray-100 hover:bg-white hover:shadow-xl hover:shadow-gray-200/50 transition-all duration-300 relative overflow-hidden">
                         <div class="absolute top-0 right-0 w-32 h-32 bg-orange-50 rounded-bl-full -mr-16 -mt-16 transition-transform group-hover:scale-110"></div>
                        <div class="w-14 h-14 bg-white rounded-2xl shadow-sm text-2xl flex items-center justify-center mb-6 relative z-10 group-hover:scale-110 transition-transform duration-300">🚨</div>
                        <h3 class="text-xl font-bold text-gray-900 mb-4 relative z-10">Missing the Sales</h3>
                        <p class="text-gray-600 leading-relaxed relative z-10">
                            Flash deals. Limited-time offers. Gone before you even knew they existed. And you paid full
                            price two days later.
                        </p>
                    </div>

                    <!-- Pain Point 3 -->
                    <div class="group p-8 rounded-3xl bg-gray-50 border border-gray-100 hover:bg-white hover:shadow-xl hover:shadow-gray-200/50 transition-all duration-300 relative overflow-hidden">
                         <div class="absolute top-0 right-0 w-32 h-32 bg-blue-50 rounded-bl-full -mr-16 -mt-16 transition-transform group-hover:scale-110"></div>
                        <div class="w-14 h-14 bg-white rounded-2xl shadow-sm text-2xl flex items-center justify-center mb-6 relative z-10 group-hover:scale-110 transition-transform duration-300">❓</div>
                        <h3 class="text-xl font-bold text-gray-900 mb-4 relative z-10">Seller Trust Issues</h3>
                        <p class="text-gray-600 leading-relaxed relative z-10">
                            "Is this seller legit? Will I get an authentic bottle or a knockoff?" The anxiety is
                            real—especially with unfamiliar sellers.
                        </p>
                    </div>
                </div>

                <!-- Pain Point 4 - Featured / Full width -->
                <div class="relative rounded-3xl p-8 lg:p-12 overflow-hidden bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-100 shadow-lg shadow-amber-100/50">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-amber-100/50 rounded-full blur-3xl -mr-32 -mt-32"></div>
                    <div class="flex flex-col md:flex-row md:items-center gap-6 md:gap-12 relative z-10">
                        <div class="w-20 h-20 bg-white rounded-2xl shadow-md text-4xl flex items-center justify-center flex-shrink-0 text-amber-500">
                           💸
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-3">Overpaying Without Knowing</h3>
                            <p class="text-lg text-gray-700 max-w-2xl">
                                Paid ₹8,000 for that bottle? Another seller had it for ₹5,500. But how would you know?
                                You can't check everywhere. <span class="font-semibold text-amber-700">We fix this.</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ============================================ -->
        <!-- SECTION 3: SOLUTION / BENEFITS -->
        <!-- ============================================ -->
        <section class="py-20 lg:py-32 bg-gray-50 relative overflow-hidden">
            <!-- Background Decoration -->
            <div class="absolute top-1/4 left-0 w-full h-px bg-gradient-to-r from-transparent via-teal-200 to-transparent opacity-50"></div>
            
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <!-- Section Header -->
                <div class="text-center mb-16 lg:mb-24">
                    <span class="text-teal-600 font-bold tracking-wider uppercase text-sm mb-2 block">The Solution</span>
                    <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-900 mb-6">
                        One Search. <span class="gradient-text">Every Seller.</span><br/> Best Price.
                    </h2>
                    <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                        ScentCents does the heavy lifting so you don't have to.
                    </p>
                </div>

                <!-- Benefit Cards - 2x2 grid with Glassmorphism -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-10 mb-16">
                    <!-- Benefit 1 -->
                    <div class="glass-card rounded-3xl p-8 lg:p-10 relative overflow-hidden group">
                        <div class="absolute top-0 right-0 w-40 h-40 bg-teal-100/50 rounded-bl-full -mr-20 -mt-20 transition-all duration-500 group-hover:scale-125 group-hover:bg-teal-100"></div>
                        <div class="w-16 h-16 bg-teal-50 text-teal-600 rounded-2xl flex items-center justify-center text-3xl mb-8 group-hover:scale-110 transition-transform duration-300 shadow-sm">
                            📊
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">Compare in Seconds</h3>
                        <p class="text-gray-600 mb-8 leading-relaxed">
                            See prices from 50+ sellers instantly. Official retailers AND trusted Reddit sellers—all in
                            one place. No more tab juggling.
                        </p>
                        <div class="flex items-center gap-3 text-teal-700 font-semibold bg-teal-50/50 w-fit px-4 py-2 rounded-full border border-teal-100">
                            <span>⚡️ Save 3+ hours per purchase</span>
                        </div>
                    </div>

                    <!-- Benefit 2 -->
                    <div class="glass-card rounded-3xl p-8 lg:p-10 relative overflow-hidden group">
                        <div class="absolute top-0 right-0 w-40 h-40 bg-amber-100/50 rounded-bl-full -mr-20 -mt-20 transition-all duration-500 group-hover:scale-125 group-hover:bg-amber-100"></div>
                        <div class="w-16 h-16 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center text-3xl mb-8 group-hover:scale-110 transition-transform duration-300 shadow-sm">
                            🔔
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">Price Drop Alerts</h3>
                        <p class="text-gray-600 mb-8 leading-relaxed">
                            Set your target price. We'll notify you the moment it drops. Buy when you're ready, at the
                            price you want.
                        </p>
                        <div class="flex items-center gap-3 text-amber-700 font-semibold bg-amber-50/50 w-fit px-4 py-2 rounded-full border border-amber-100">
                            <span>💰 Average user saves ₹2,400/year</span>
                        </div>
                    </div>

                    <!-- Benefit 3 -->
                    <div class="glass-card rounded-3xl p-8 lg:p-10 relative overflow-hidden group">
                        <div class="absolute top-0 right-0 w-40 h-40 bg-blue-100/50 rounded-bl-full -mr-20 -mt-20 transition-all duration-500 group-hover:scale-125 group-hover:bg-blue-100"></div>
                        <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center text-3xl mb-8 group-hover:scale-110 transition-transform duration-300 shadow-sm">
                            ✅
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">Verified Sellers Only</h3>
                        <p class="text-gray-600 mb-8 leading-relaxed">
                            Every seller is vetted for authenticity. Reddit sellers verified by community reputation and
                            transaction history.
                        </p>
                        <div class="flex items-center gap-3 text-blue-700 font-semibold bg-blue-50/50 w-fit px-4 py-2 rounded-full border border-blue-100">
                            <span>🛡️ Buy without anxiety</span>
                        </div>
                    </div>

                    <!-- Benefit 4 -->
                    <div class="glass-card rounded-3xl p-8 lg:p-10 relative overflow-hidden group">
                        <div class="absolute top-0 right-0 w-40 h-40 bg-purple-100/50 rounded-bl-full -mr-20 -mt-20 transition-all duration-500 group-hover:scale-125 group-hover:bg-purple-100"></div>
                        <div class="w-16 h-16 bg-purple-50 text-purple-600 rounded-2xl flex items-center justify-center text-3xl mb-8 group-hover:scale-110 transition-transform duration-300 shadow-sm">
                            📈
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">Price History</h3>
                        <p class="text-gray-600 mb-8 leading-relaxed">
                            See if today's "sale" is actually a good deal. Track prices over time and never fall for
                            fake discounts again.
                        </p>
                        <div class="flex items-center gap-3 text-purple-700 font-semibold bg-purple-50/50 w-fit px-4 py-2 rounded-full border border-purple-100">
                            <span>🧠 Smart purchase decisions</span>
                        </div>
                    </div>
                </div>

                <!-- CTA -->
                <div class="text-center">
                    <a href="{{ route('perfumes.index') }}"
                        class="inline-block btn-primary text-white px-10 py-5 rounded-full font-bold text-lg shadow-xl shadow-teal-700/20 hover:shadow-2xl hover:shadow-teal-700/30 transition-all transform hover:-translate-y-1">
                        Start Comparing Prices →
                    </a>
                </div>
            </div>
        </section>

        <!-- ============================================ -->
        <!-- SECTION 4: SOCIAL PROOF -->
        <!-- ============================================ -->
        <section class="py-20 lg:py-32 bg-white relative">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

                <!-- Statistics Bar -->
                <div class="glass-panel rounded-3xl p-8 lg:p-12 mb-16 relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-r from-teal-50/50 to-amber-50/50 opacity-50"></div>
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-12 text-center relative z-10">
                        <div>
                            <div class="text-4xl lg:text-5xl font-bold gradient-text mb-2">12k+</div>
                            <div class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Active Users</div>
                        </div>
                        <div>
                            <div class="text-4xl lg:text-5xl font-bold gradient-text-gold mb-2">₹24L+</div>
                            <div class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Saved This Month</div>
                        </div>
                        <div>
                            <div class="text-4xl lg:text-5xl font-bold gradient-text mb-2">53</div>
                            <div class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Verified Sellers</div>
                        </div>
                        <div>
                            <div class="text-4xl lg:text-5xl font-bold gradient-text mb-2">2.3k</div>
                            <div class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Perfumes Tracked</div>
                        </div>
                    </div>
                </div>

                <!-- Testimonials -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-16">
                    <!-- Testimonial 1 -->
                    <div class="bg-white rounded-3xl p-8 shadow-lg shadow-gray-100 border border-gray-100 relative group hover:-translate-y-2 transition-all duration-300">
                        <div class="absolute -top-4 -right-4 w-12 h-12 bg-amber-100 rounded-full flex items-center justify-center text-2xl rotate-12 group-hover:rotate-0 transition-transform">💬</div>
                        <div class="flex text-amber-400 mb-6 space-x-1">
                            <span>★</span><span>★</span><span>★</span><span>★</span><span>★</span>
                        </div>
                        <blockquote class="text-gray-700 mb-8 leading-relaxed text-lg">
                            "I was about to pay ₹8,500 for Dior Sauvage. ScentCents showed me a Reddit seller with the
                            same bottle for ₹5,200. <span class="bg-teal-50 text-teal-800 font-semibold px-1">Saved ₹3,300</span> on one purchase."
                        </blockquote>
                        <div class="flex items-center gap-4 border-t border-gray-50 pt-6">
                            <div class="w-12 h-12 bg-gradient-to-br from-teal-500 to-teal-700 rounded-full flex items-center justify-center text-white font-bold text-lg shadow-md">
                                R
                            </div>
                            <div>
                                <div class="font-bold text-gray-900">Rahul M.</div>
                                <div class="text-sm text-teal-600 font-medium">Saved ₹12K+ in 6 months</div>
                            </div>
                        </div>
                    </div>

                    <!-- Testimonial 2 -->
                    <div class="bg-white rounded-3xl p-8 shadow-lg shadow-gray-100 border border-gray-100 relative group hover:-translate-y-2 transition-all duration-300">
                         <div class="absolute -top-4 -right-4 w-12 h-12 bg-teal-100 rounded-full flex items-center justify-center text-2xl rotate-12 group-hover:rotate-0 transition-transform">🔔</div>
                        <div class="flex text-amber-400 mb-6 space-x-1">
                            <span>★</span><span>★</span><span>★</span><span>★</span><span>★</span>
                        </div>
                        <blockquote class="text-gray-700 mb-8 leading-relaxed text-lg">
                            "The price alerts are a game-changer. Set one for Bleu de Chanel, got notified 2 weeks later
                            when it dropped 30%. This app pays for itself."
                        </blockquote>
                        <div class="flex items-center gap-4 border-t border-gray-50 pt-6">
                            <div class="w-12 h-12 bg-gradient-to-br from-amber-500 to-amber-700 rounded-full flex items-center justify-center text-white font-bold text-lg shadow-md">
                                P
                            </div>
                            <div>
                                <div class="font-bold text-gray-900">Priya S.</div>
                                <div class="text-sm text-teal-600 font-medium">8 alerts active</div>
                            </div>
                        </div>
                    </div>

                    <!-- Testimonial 3 -->
                    <div class="bg-white rounded-3xl p-8 shadow-lg shadow-gray-100 border border-gray-100 relative group hover:-translate-y-2 transition-all duration-300">
                        <div class="absolute -top-4 -right-4 w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center text-2xl rotate-12 group-hover:rotate-0 transition-transform">🔥</div>
                        <div class="flex text-amber-400 mb-6 space-x-1">
                            <span>★</span><span>★</span><span>★</span><span>★</span><span>★</span>
                        </div>
                        <blockquote class="text-gray-700 mb-8 leading-relaxed text-lg">
                            "Finally, a site that actually includes Reddit sellers! That's where all the good decant
                            deals are. Been using it for 3 months—absolute gem."
                        </blockquote>
                        <div class="flex items-center gap-4 border-t border-gray-50 pt-6">
                            <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-700 rounded-full flex items-center justify-center text-white font-bold text-lg shadow-md">
                                V
                            </div>
                            <div>
                                <div class="font-bold text-gray-900">Vikram T.</div>
                                <div class="text-sm text-teal-600 font-medium">Member since Oct 2025</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Community Trust -->
                <div class="text-center">
                    <p class="text-gray-400 font-medium tracking-wide uppercase text-sm mb-6">Trusted by the best communities</p>
                    <div class="flex flex-wrap justify-center gap-4 opacity-70 grayscale hover:grayscale-0 transition-all duration-500">
                        <span class="px-6 py-3 rounded-full bg-gray-50 border border-gray-100 text-gray-600 font-semibold shadow-sm">r/desifragranceaddicts</span>
                        <span class="px-6 py-3 rounded-full bg-gray-50 border border-gray-100 text-gray-600 font-semibold shadow-sm">r/fragranceswap</span>
                        <span class="px-6 py-3 rounded-full bg-gray-50 border border-gray-100 text-gray-600 font-semibold shadow-sm">r/Perfumes</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- ============================================ -->
        <!-- SECTION 5: HOW IT WORKS -->
        <!-- ============================================ -->
        <section class="py-20 lg:py-32 bg-gray-50 relative overflow-hidden">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <!-- Section Header -->
                <div class="text-center mb-16 lg:mb-24">
                    <span class="text-teal-600 font-bold tracking-wider uppercase text-sm mb-2 block">Simple Process</span>
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-6">
                        From Search to Savings
                    </h2>
                    <p class="text-lg text-gray-600">
                        In just 30 seconds.
                    </p>
                </div>

                <!-- 3 Steps -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-12 relative mb-16">
                    <!-- Connector Line (Desktop) -->
                    <div class="hidden md:block absolute top-12 left-[16%] right-[16%] h-0.5 bg-gradient-to-r from-teal-200 via-teal-200 to-teal-200" style="z-index: 0;"></div>

                    <!-- Step 1 -->
                    <div class="text-center relative z-10 group">
                        <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center text-3xl font-bold text-teal-600 shadow-xl shadow-teal-100 border-4 border-teal-50 mx-auto mb-8 group-hover:scale-110 transition-transform duration-300">
                            1
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-4">Search Any Perfume</h3>
                        <p class="text-gray-600 leading-relaxed px-4">
                            Type any fragrance name. We instantly search 50+ sellers—official retailers AND
                            community-verified Reddit sellers.
                        </p>
                    </div>

                    <!-- Step 2 -->
                    <div class="text-center relative z-10 group">
                        <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center text-3xl font-bold text-teal-600 shadow-xl shadow-teal-100 border-4 border-teal-50 mx-auto mb-8 group-hover:scale-110 transition-transform duration-300">
                            2
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-4">Compare Prices</h3>
                        <p class="text-gray-600 leading-relaxed px-4">
                            See all prices side by side. Filter by size, seller type, or stock status. Spot the best
                            deal immediately.
                        </p>
                    </div>

                    <!-- Step 3 -->
                    <div class="text-center relative z-10 group">
                        <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center text-3xl font-bold text-teal-600 shadow-xl shadow-teal-100 border-4 border-teal-50 mx-auto mb-8 group-hover:scale-110 transition-transform duration-300">
                            3
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-4">Save or Set Alert</h3>
                        <p class="text-gray-600 leading-relaxed px-4">
                            Found a deal? Buy it. Not ready? Set a price alert and we'll notify you the moment it
                            drops to your target.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ============================================ -->
        <!-- SECTION 6: FAQ -->
        <!-- ============================================ -->
        <section class="py-20 lg:py-32 bg-white">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Section Header -->
                <div class="text-center mb-16">
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">
                        Questions? We've Got Answers.
                    </h2>
                </div>

                <!-- FAQ Items -->
                <div class="space-y-6" x-data="{ active: null }">
                    <!-- FAQ 1 -->
                    <div class="border border-gray-100 rounded-2xl overflow-hidden hover:border-teal-200 transition-colors">
                        <button @click="active = (active === 1 ? null : 1)" class="flex justify-between items-center w-full p-6 text-left bg-gray-50/50 hover:bg-gray-50 focus:outline-none">
                            <span class="text-lg font-semibold text-gray-900">Is ScentCents really free?</span>
                            <span class="transform transition-transform duration-200" :class="active === 1 ? 'rotate-180' : ''">
                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </span>
                        </button>
                        <div x-show="active === 1" x-collapse>
                            <div class="p-6 pt-0 text-gray-600 leading-relaxed">
                                Yes, 100% free. No premium tiers, no hidden fees. We earn a small commission when you click through to a seller and make a purchase—you never pay extra.
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 2 -->
                    <div class="border border-gray-100 rounded-2xl overflow-hidden hover:border-teal-200 transition-colors">
                        <button @click="active = (active === 2 ? null : 2)" class="flex justify-between items-center w-full p-6 text-left bg-gray-50/50 hover:bg-gray-50 focus:outline-none">
                            <span class="text-lg font-semibold text-gray-900">Are Reddit sellers safe?</span>
                            <span class="transform transition-transform duration-200" :class="active === 2 ? 'rotate-180' : ''">
                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </span>
                        </button>
                        <div x-show="active === 2" x-collapse>
                            <div class="p-6 pt-0 text-gray-600 leading-relaxed">
                                We only list Reddit sellers with established reputations, verified transaction history, and community vouches. Look for our <span class="bg-teal-50 text-teal-700 px-2 py-0.5 rounded text-xs font-bold uppercase tracking-wide">Verified</span> badge. We never list unvetted sellers.
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 3 -->
                    <div class="border border-gray-100 rounded-2xl overflow-hidden hover:border-teal-200 transition-colors">
                        <button @click="active = (active === 3 ? null : 3)" class="flex justify-between items-center w-full p-6 text-left bg-gray-50/50 hover:bg-gray-50 focus:outline-none">
                            <span class="text-lg font-semibold text-gray-900">How often are prices updated?</span>
                            <span class="transform transition-transform duration-200" :class="active === 3 ? 'rotate-180' : ''">
                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </span>
                        </button>
                        <div x-show="active === 3" x-collapse>
                            <div class="p-6 pt-0 text-gray-600 leading-relaxed">
                                Official retailer prices refresh every 4 hours. Reddit seller prices update when they publish new inventory—typically weekly. We timestamp everything.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ============================================ -->
        <!-- SECTION 7: FINAL CTA -->
        <!-- ============================================ -->
        <section class="py-20 lg:py-32 cta-gradient relative overflow-hidden">
             <!-- Abstract Shapes -->
            <div class="absolute top-0 right-0 w-96 h-96 bg-white/10 rounded-full blur-3xl -mr-32 -mt-32"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-amber-400/20 rounded-full blur-3xl -ml-20 -mb-20"></div>
            
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
                <!-- Headline -->
                <h2 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-white mb-6">
                    Ready to Start Saving?
                </h2>
                <p class="text-xl text-teal-50 mb-10 max-w-2xl mx-auto">
                    Join 12,000+ smart fragrance lovers. Create your free account and never overpay again.
                </p>

                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center mb-10">
                    <a href="{{ route('register') }}" class="btn-accent text-gray-900 font-bold px-10 py-5 rounded-xl text-lg shadow-xl shadow-amber-900/20 active:scale-95 transition-transform">
                        Create Free Account →
                    </a>
                    <a href="{{ route('perfumes.index') }}" class="bg-white/10 backdrop-blur-md border border-white/20 hover:bg-white/20 text-white font-semibold px-10 py-5 rounded-xl text-lg transition-all">
                        Browse Perfumes First
                    </a>
                </div>

                <!-- Urgency Element (Ethical) -->
                <div class="inline-flex items-center gap-3 bg-white/10 backdrop-blur-md px-6 py-3 rounded-full text-white text-sm border border-white/10">
                    <span class="flex h-2 w-2 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                    </span>
                    <span><strong>143 price alerts</strong> triggered today</span>
                </div>
            </div>
        </section>
    </main>

    <!-- ============================================ -->
    <!-- FOOTER -->
    <!-- ============================================ -->
    <footer class="bg-gray-900 text-white pt-20 pb-10 border-t border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-16">
                <div class="col-span-1 md:col-span-2">
                     <a href="/" class="inline-block mb-6">
                        <img src="/images/logo2.png" alt="ScentCents" class="h-16 object-contain brightness-0 invert">
                    </a>
                    <p class="text-gray-400 max-w-sm mb-6 leading-relaxed">
                        The smartest way to buy perfumes in India. Compare prices, track deals, and save money with verified sellers.
                    </p>
                    <div class="flex space-x-4">
                        <!-- Social Icons (Placeholder) -->
                        <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center text-gray-400 hover:bg-teal-600 hover:text-white transition-colors">𝕏</a>
                        <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center text-gray-400 hover:bg-teal-600 hover:text-white transition-colors">📸</a>
                        <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center text-gray-400 hover:bg-teal-600 hover:text-white transition-colors">👽</a>
                    </div>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold text-white mb-6">Platform</h4>
                    <ul class="space-y-4">
                        <li><a href="{{ route('perfumes.index') }}" class="text-gray-400 hover:text-teal-400 transition-colors">Browse Perfumes</a></li>
                        <li><a href="/login" class="text-gray-400 hover:text-teal-400 transition-colors">Login</a></li>
                        <li><a href="/register" class="text-gray-400 hover:text-teal-400 transition-colors">Register</a></li>
                    </ul>
                </div>
                
                <div>
                     <h4 class="text-lg font-semibold text-white mb-6">Legal</h4>
                    <ul class="space-y-4">
                        <li><a href="#" class="text-gray-400 hover:text-teal-400 transition-colors">Privacy Policy</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-teal-400 transition-colors">Terms of Service</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-teal-400 transition-colors">Cookie Policy</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-800 pt-8 flex flex-col md:flex-row justify-between items-center text-sm text-gray-500">
                <p>&copy; {{ date('Y') }} ScentCents. All rights reserved.</p>
                <p class="mt-2 md:mt-0">Made with ❤️ for fragrance lovers.</p>
            </div>
        </div>
    </footer>


</body>

</html>