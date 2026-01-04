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
            --color-primary: #0D9488;
            --color-primary-dark: #0F766E;
            --color-accent: #F59E0B;
            --color-text: #1C1917;
            --color-text-muted: #57534E;
            --color-bg-light: #F5F5F4;
            --color-bg-cream: #FFFBEB;
        }

        .font-inter {
            font-family: 'Inter', sans-serif;
        }

        /* Smooth scroll */
        html {
            scroll-behavior: smooth;
        }

        /* Gradient text */
        .gradient-text {
            background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Primary button */
        .btn-primary {
            background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 100%);
            box-shadow: 0 4px 14px rgba(13, 148, 136, 0.4);
            transition: all 0.2s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(13, 148, 136, 0.5);
        }

        /* Secondary/Accent button */
        .btn-accent {
            background: linear-gradient(135deg, var(--color-accent) 0%, #D97706 100%);
            box-shadow: 0 4px 14px rgba(245, 158, 11, 0.4);
        }

        .btn-accent:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(245, 158, 11, 0.5);
        }

        /* Hero background */
        .hero-bg {
            background: linear-gradient(180deg, #FFFBEB 0%, #FFFFFF 100%);
        }

        /* Subtle decorative elements */
        .hero-decoration {
            position: absolute;
            opacity: 0.05;
            pointer-events: none;
        }

        /* CTA section gradient */
        .cta-gradient {
            background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 100%);
        }

        /* Pulse animation for urgency */
        @keyframes pulse-subtle {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.7;
            }
        }

        .pulse-subtle {
            animation: pulse-subtle 2s ease-in-out infinite;
        }
    </style>
</head>

<body class="font-inter antialiased text-gray-900">

    <!-- ============================================ -->
    <!-- NAVIGATION -->
    <!-- ============================================ -->
    <header class="fixed top-0 left-0 right-0 z-50 bg-white/90 backdrop-blur-md border-b border-gray-100">
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
                <a href="/" class="flex items-center space-x-2">
                    <div class="w-8 h-8 bg-gradient-to-br from-teal-500 to-teal-700 rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold text-lg">S</span>
                    </div>
                    <span class="text-xl font-bold gradient-text">ScentCents</span>
                </a>
                
                <!-- Desktop Navigation -->
                <nav class="hidden md:flex items-center space-x-6">
                    <!-- Guest Navigation -->
                    <template x-if="!user">
                        <div class="flex items-center space-x-4">
                            <a href="{{ route('login') }}" class="text-gray-600 hover:text-teal-600 font-medium transition-colors">
                                Login
                            </a>
                            <a href="{{ route('register') }}" class="text-gray-600 hover:text-teal-600 font-medium transition-colors">
                                Register
                            </a>
                            <a href="{{ route('perfumes.index') }}" class="btn-primary text-white px-5 py-2.5 rounded-lg font-semibold text-sm">
                                Browse Perfumes →
                            </a>
                        </div>
                    </template>
                    
                    <!-- Authenticated Navigation -->
                    <template x-if="user">
                        <div class="flex items-center space-x-4">
                            <a href="/wishlist" class="text-gray-600 hover:text-teal-600 font-medium transition-colors">
                                Wishlist
                            </a>
                            <a href="/alerts" class="text-gray-600 hover:text-teal-600 font-medium transition-colors">
                                My Alerts
                            </a>
                            <a href="{{ route('perfumes.index') }}" class="btn-primary text-white px-5 py-2.5 rounded-lg font-semibold text-sm">
                                Browse Perfumes →
                            </a>
                            <div class="relative">
                                <button @click="dropdownOpen = !dropdownOpen" class="flex items-center space-x-2 text-gray-600 hover:text-teal-600">
                                    <div class="w-8 h-8 bg-gradient-to-br from-teal-500 to-teal-700 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                                        <span x-text="user.username ? user.username.charAt(0).toUpperCase() : 'U'"></span>
                                    </div>
                                    <svg class="w-4 h-4" :class="{'rotate-180': dropdownOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                                <div x-show="dropdownOpen" @click.outside="dropdownOpen = false" x-transition
                                     class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-2 z-50">
                                    <div class="px-4 py-2 border-b border-gray-100">
                                        <p class="text-sm font-medium text-gray-900" x-text="user.username"></p>
                                        <p class="text-xs text-gray-500" x-text="user.email"></p>
                                    </div>
                                    <button @click="logout()" class="w-full text-left px-4 py-2 text-red-600 hover:bg-red-50 text-sm">
                                        Logout
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </nav>
                
                <!-- Mobile Menu Button -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden text-gray-600 hover:text-teal-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        <path x-show="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <!-- Mobile Menu -->
            <div x-show="mobileMenuOpen" x-transition class="md:hidden pb-4" x-data="{
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
                <template x-if="!user">
                    <div class="flex flex-col space-y-2 pt-2">
                        <a href="{{ route('login') }}" class="px-4 py-2 text-gray-600 hover:text-teal-600 font-medium">Login</a>
                        <a href="{{ route('register') }}" class="px-4 py-2 text-gray-600 hover:text-teal-600 font-medium">Register</a>
                        <a href="{{ route('perfumes.index') }}" class="mx-4 btn-primary text-white px-5 py-3 rounded-lg font-semibold text-center">
                            Browse Perfumes →
                        </a>
                    </div>
                </template>
                <template x-if="user">
                    <div class="flex flex-col space-y-2 pt-2">
                        <a href="/wishlist" class="px-4 py-2 text-gray-600 hover:text-teal-600 font-medium">Wishlist</a>
                        <a href="/alerts" class="px-4 py-2 text-gray-600 hover:text-teal-600 font-medium">My Alerts</a>
                        <a href="{{ route('perfumes.index') }}" class="mx-4 btn-primary text-white px-5 py-3 rounded-lg font-semibold text-center">
                            Browse Perfumes →
                        </a>
                        <button @click="logout()" class="mx-4 mt-2 py-2 text-red-600 hover:bg-red-50 rounded-lg font-medium">
                            Logout
                        </button>
                    </div>
                </template>
            </div>
        </div>
    </header>

    <main>
        <!-- ============================================ -->
        <!-- SECTION 1: HERO (Above the Fold) -->
        <!-- ============================================ -->
        <section class="hero-bg pt-24 pb-16 lg:pt-32 lg:pb-24 relative overflow-hidden">
            <!-- Decorative elements -->
            <div class="hero-decoration top-20 right-10 w-32 h-32 border-2 border-teal-600 rounded-full"></div>
            <div class="hero-decoration bottom-10 left-10 w-24 h-24 border-2 border-amber-500 rounded-full"></div>

            <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <!-- Headline -->
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-gray-900 mb-6 leading-tight">
                    Stop Overpaying for Perfumes
                </h1>

                <!-- Subheadline -->
                <p class="text-lg sm:text-xl text-gray-600 mb-10 max-w-2xl mx-auto leading-relaxed">
                    Compare prices from 50+ verified sellers—including Reddit's most trusted fragrance sellers.
                    Track prices. Get alerts. Save money.
                </p>

                <!-- Search Box -->
                <form action="{{ route('perfumes.index') }}" method="GET" class="max-w-xl mx-auto mb-8">
                    <div
                        class="flex flex-col sm:flex-row gap-3 bg-white rounded-xl sm:rounded-full p-2 shadow-lg border border-gray-100">
                        <div class="flex-1 relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </span>
                            <input type="text" name="search" placeholder="Search any perfume... (e.g., Dior Sauvage)"
                                class="w-full pl-12 pr-4 py-4 rounded-lg sm:rounded-full text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-teal-500 text-base">
                        </div>
                        <button type="submit"
                            class="btn-primary text-white px-8 py-4 rounded-lg sm:rounded-full font-semibold text-base whitespace-nowrap">
                            Find Prices →
                        </button>
                    </div>
                </form>

                <!-- Trust Bar -->
                <div class="flex flex-wrap justify-center gap-6 sm:gap-10 text-sm font-medium text-gray-600">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-teal-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                clip-rule="evenodd" />
                        </svg>
                        <span>12,000+ Users</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-teal-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                clip-rule="evenodd" />
                        </svg>
                        <span>53 Verified Sellers</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-teal-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                clip-rule="evenodd" />
                        </svg>
                        <span>₹24,00,000+ Saved</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- ============================================ -->
        <!-- SECTION 2: PROBLEM / AGITATION -->
        <!-- ============================================ -->
        <section class="py-16 lg:py-24 bg-gray-50">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Section Header -->
                <div class="text-center mb-12 lg:mb-16">
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">
                        Tired of the Perfume Price Hunt?
                    </h2>
                    <p class="text-lg text-gray-600">
                        We've all been there. It shouldn't be this hard.
                    </p>
                </div>

                <!-- Pain Point Cards - 3 + 1 layout -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <!-- Pain Point 1 -->
                    <div class="bg-white rounded-2xl p-6 lg:p-8 shadow-sm border border-gray-100">
                        <div class="text-4xl mb-4">🔍</div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Endless Tab Switching</h3>
                        <p class="text-gray-600">
                            Jumping between 10+ websites just to find who has the best price. Copy-paste. Compare.
                            Repeat. There goes your evening.
                        </p>
                    </div>

                    <!-- Pain Point 2 -->
                    <div class="bg-white rounded-2xl p-6 lg:p-8 shadow-sm border border-gray-100">
                        <div class="text-4xl mb-4">🚨</div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Missing the Sales</h3>
                        <p class="text-gray-600">
                            Flash deals. Limited-time offers. Gone before you even knew they existed. And you paid full
                            price two days later.
                        </p>
                    </div>

                    <!-- Pain Point 3 -->
                    <div class="bg-white rounded-2xl p-6 lg:p-8 shadow-sm border border-gray-100">
                        <div class="text-4xl mb-4">❓</div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Seller Trust Issues</h3>
                        <p class="text-gray-600">
                            "Is this seller legit? Will I get an authentic bottle or a knockoff?" The anxiety is
                            real—especially with unfamiliar sellers.
                        </p>
                    </div>
                </div>

                <!-- Pain Point 4 - Featured / Full width -->
                <div
                    class="bg-white rounded-2xl p-6 lg:p-8 shadow-sm border-2 border-amber-200 bg-gradient-to-r from-amber-50 to-white">
                    <div class="flex flex-col md:flex-row md:items-center gap-4 md:gap-8">
                        <div class="text-5xl">💸</div>
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">Overpaying Without Knowing</h3>
                            <p class="text-gray-600">
                                Paid ₹8,000 for that bottle? Another seller had it for ₹5,500. But how would you know?
                                You can't check everywhere.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ============================================ -->
        <!-- SECTION 3: SOLUTION / BENEFITS -->
        <!-- ============================================ -->
        <section class="py-16 lg:py-24 bg-white">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Section Header -->
                <div class="text-center mb-12 lg:mb-16">
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">
                        One Search. Every Seller. Best Price.
                    </h2>
                    <p class="text-lg text-gray-600">
                        ScentCents does the hard work so you don't have to.
                    </p>
                </div>

                <!-- Benefit Cards - 2x2 grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8 mb-10">
                    <!-- Benefit 1 -->
                    <div class="bg-gradient-to-br from-teal-50 to-white rounded-2xl p-6 lg:p-8 border border-teal-100">
                        <div class="text-4xl mb-4">📊</div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Compare in Seconds</h3>
                        <p class="text-gray-600 mb-4">
                            See prices from 50+ sellers instantly. Official retailers AND trusted Reddit sellers—all in
                            one place. No more tab juggling.
                        </p>
                        <p class="text-teal-600 font-medium flex items-center gap-2">
                            <span>→</span>
                            <span>Save 3+ hours per purchase</span>
                        </p>
                    </div>

                    <!-- Benefit 2 -->
                    <div class="bg-gradient-to-br from-teal-50 to-white rounded-2xl p-6 lg:p-8 border border-teal-100">
                        <div class="text-4xl mb-4">🔔</div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Price Drop Alerts</h3>
                        <p class="text-gray-600 mb-4">
                            Set your target price. We'll notify you the moment it drops. Buy when you're ready, at the
                            price you want.
                        </p>
                        <p class="text-teal-600 font-medium flex items-center gap-2">
                            <span>→</span>
                            <span>Average user saves ₹2,400/year</span>
                        </p>
                    </div>

                    <!-- Benefit 3 -->
                    <div class="bg-gradient-to-br from-teal-50 to-white rounded-2xl p-6 lg:p-8 border border-teal-100">
                        <div class="text-4xl mb-4">✅</div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Verified Sellers Only</h3>
                        <p class="text-gray-600 mb-4">
                            Every seller is vetted for authenticity. Reddit sellers verified by community reputation and
                            transaction history.
                        </p>
                        <p class="text-teal-600 font-medium flex items-center gap-2">
                            <span>→</span>
                            <span>Buy without the fake fragrance anxiety</span>
                        </p>
                    </div>

                    <!-- Benefit 4 -->
                    <div class="bg-gradient-to-br from-teal-50 to-white rounded-2xl p-6 lg:p-8 border border-teal-100">
                        <div class="text-4xl mb-4">📈</div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Price History</h3>
                        <p class="text-gray-600 mb-4">
                            See if today's "sale" is actually a good deal. Track prices over time and never fall for
                            fake discounts again.
                        </p>
                        <p class="text-teal-600 font-medium flex items-center gap-2">
                            <span>→</span>
                            <span>Confidence in every purchase</span>
                        </p>
                    </div>
                </div>

                <!-- CTA -->
                <div class="text-center">
                    <a href="{{ route('perfumes.index') }}"
                        class="inline-block btn-primary text-white px-8 py-4 rounded-xl font-semibold text-lg">
                        Start Comparing Prices →
                    </a>
                </div>
            </div>
        </section>

        <!-- ============================================ -->
        <!-- SECTION 4: SOCIAL PROOF -->
        <!-- ============================================ -->
        <section class="py-16 lg:py-24 bg-gray-50">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

                <!-- Statistics Bar -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 lg:p-8 mb-12 lg:mb-16">
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-8 text-center">
                        <div>
                            <div class="text-3xl lg:text-4xl font-bold text-gray-900">12,437</div>
                            <div class="text-sm text-gray-500 mt-1">Active Users</div>
                        </div>
                        <div>
                            <div class="text-3xl lg:text-4xl font-bold text-gray-900">₹24L+</div>
                            <div class="text-sm text-gray-500 mt-1">Saved This Month</div>
                        </div>
                        <div>
                            <div class="text-3xl lg:text-4xl font-bold text-gray-900">53</div>
                            <div class="text-sm text-gray-500 mt-1">Verified Sellers</div>
                        </div>
                        <div>
                            <div class="text-3xl lg:text-4xl font-bold text-gray-900">2,340</div>
                            <div class="text-sm text-gray-500 mt-1">Perfumes Tracked</div>
                        </div>
                    </div>
                </div>

                <!-- Testimonials -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-12">
                    <!-- Testimonial 1 -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                        <div class="flex text-amber-400 mb-4">
                            <span>★</span><span>★</span><span>★</span><span>★</span><span>★</span>
                        </div>
                        <blockquote class="text-gray-700 mb-6">
                            "I was about to pay ₹8,500 for Dior Sauvage. ScentCents showed me a Reddit seller with the
                            same bottle for ₹5,200. Verified, authentic, and I saved ₹3,300 on one purchase."
                        </blockquote>
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 bg-gradient-to-br from-teal-500 to-teal-700 rounded-full flex items-center justify-center text-white font-semibold">
                                R
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">Rahul M.</div>
                                <div class="text-sm text-gray-500">Mumbai • Saved ₹12K+ in 6 months</div>
                            </div>
                        </div>
                    </div>

                    <!-- Testimonial 2 -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                        <div class="flex text-amber-400 mb-4">
                            <span>★</span><span>★</span><span>★</span><span>★</span><span>★</span>
                        </div>
                        <blockquote class="text-gray-700 mb-6">
                            "The price alerts are a game-changer. Set one for Bleu de Chanel, got notified 2 weeks later
                            when it dropped 30%. This app pays for itself (and it's free!)."
                        </blockquote>
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 bg-gradient-to-br from-amber-500 to-amber-700 rounded-full flex items-center justify-center text-white font-semibold">
                                P
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">Priya S.</div>
                                <div class="text-sm text-gray-500">Bangalore • 8 alerts active</div>
                            </div>
                        </div>
                    </div>

                    <!-- Testimonial 3 -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                        <div class="flex text-amber-400 mb-4">
                            <span>★</span><span>★</span><span>★</span><span>★</span><span>★</span>
                        </div>
                        <blockquote class="text-gray-700 mb-6">
                            "Finally, a site that actually includes Reddit sellers! That's where all the good decant
                            deals are. Been using it for 3 months—game changer."
                        </blockquote>
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 bg-gradient-to-br from-purple-500 to-purple-700 rounded-full flex items-center justify-center text-white font-semibold">
                                V
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">Vikram T.</div>
                                <div class="text-sm text-gray-500">Delhi • Member since Oct 2025</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Community Trust -->
                <div class="text-center">
                    <p class="text-gray-500 mb-4">Trusted by the fragrance community:</p>
                    <div class="flex flex-wrap justify-center gap-4">
                        <span
                            class="bg-white px-4 py-2 rounded-full text-sm font-medium text-gray-700 border border-gray-200">
                            r/desifragranceaddicts
                        </span>
                        <span
                            class="bg-white px-4 py-2 rounded-full text-sm font-medium text-gray-700 border border-gray-200">
                            r/fragranceswap
                        </span>
                        <span
                            class="bg-white px-4 py-2 rounded-full text-sm font-medium text-gray-700 border border-gray-200">
                            r/Perfumes
                        </span>
                    </div>
                </div>
            </div>
        </section>

        <!-- ============================================ -->
        <!-- SECTION 5: HOW IT WORKS -->
        <!-- ============================================ -->
        <section class="py-16 lg:py-24 bg-white">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Section Header -->
                <div class="text-center mb-12 lg:mb-16">
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">
                        How ScentCents Works
                    </h2>
                    <p class="text-lg text-gray-600">
                        From search to savings in 30 seconds.
                    </p>
                </div>

                <!-- 3 Steps -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 lg:gap-12 mb-12">
                    <!-- Step 1 -->
                    <div class="text-center">
                        <div
                            class="w-16 h-16 bg-teal-100 text-teal-600 rounded-2xl flex items-center justify-center text-2xl font-bold mx-auto mb-6">
                            1
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Search Any Perfume</h3>
                        <p class="text-gray-600">
                            Type any fragrance name. We instantly search 50+ sellers—official retailers AND
                            community-verified Reddit sellers.
                        </p>
                    </div>

                    <!-- Arrow (desktop only) -->
                    <div class="hidden md:flex items-center justify-center absolute left-1/3 -ml-4">
                        <!-- Connector would be here -->
                    </div>

                    <!-- Step 2 -->
                    <div class="text-center">
                        <div
                            class="w-16 h-16 bg-teal-100 text-teal-600 rounded-2xl flex items-center justify-center text-2xl font-bold mx-auto mb-6">
                            2
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Compare Prices</h3>
                        <p class="text-gray-600">
                            See all prices side by side. Filter by size, seller type, or stock status. Spot the best
                            deal immediately.
                        </p>
                    </div>

                    <!-- Step 3 -->
                    <div class="text-center">
                        <div
                            class="w-16 h-16 bg-teal-100 text-teal-600 rounded-2xl flex items-center justify-center text-2xl font-bold mx-auto mb-6">
                            3
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Save or Set Alert</h3>
                        <p class="text-gray-600">
                            Found a deal? Go buy it. Not ready? Set a price alert and we'll notify you the moment it
                            drops to your target.
                        </p>
                    </div>
                </div>

                <!-- Differentiator Callout -->
                <div class="bg-teal-50 border-l-4 border-teal-500 rounded-r-2xl p-6 lg:p-8 mb-10">
                    <div class="flex items-start gap-4">
                        <span class="text-2xl">💡</span>
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">What makes us different?</h4>
                            <p class="text-gray-700">
                                Unlike Google or other comparison sites, ScentCents is the <strong>only platform that
                                    searches trusted Reddit sellers</strong>—where the best decant and authentic
                                fragrance deals actually are.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- CTA -->
                <div class="text-center">
                    <a href="{{ route('perfumes.index') }}"
                        class="inline-block btn-primary text-white px-8 py-4 rounded-xl font-semibold text-lg">
                        Start Comparing Prices →
                    </a>
                </div>
            </div>
        </section>

        <!-- ============================================ -->
        <!-- SECTION 6: FAQ / OBJECTION HANDLING -->
        <!-- ============================================ -->
        <section class="py-16 lg:py-24 bg-gray-50">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Section Header -->
                <div class="text-center mb-12">
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">
                        Questions? We've Got Answers.
                    </h2>
                </div>

                <!-- FAQ Items -->
                <div class="space-y-4">
                    <!-- FAQ 1 -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Is ScentCents really free?</h3>
                        <p class="text-gray-600">
                            Yes, 100% free. No premium tiers, no hidden fees. We earn a small commission when you click
                            through to a seller and make a purchase—you never pay extra.
                        </p>
                    </div>

                    <!-- FAQ 2 -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Are Reddit sellers safe to buy from?</h3>
                        <p class="text-gray-600">
                            We only list Reddit sellers with established reputations, verified transaction history, and
                            community vouches. Look for our <span
                                class="inline-flex items-center gap-1 bg-teal-100 text-teal-700 px-2 py-0.5 rounded text-sm font-medium">✓
                                Community Verified</span> badge. We never list unvetted sellers.
                        </p>
                    </div>

                    <!-- FAQ 3 -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">How often are prices updated?</h3>
                        <p class="text-gray-600">
                            Official retailer prices refresh every 4 hours. Reddit seller prices update when they
                            publish new inventory—typically weekly. We timestamp everything so you know how fresh the
                            data is.
                        </p>
                    </div>

                    <!-- FAQ 4 -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Will you spam me with emails?</h3>
                        <p class="text-gray-600">
                            Never. We only send emails when YOUR price alerts trigger. No newsletters. No promotions. No
                            spam. Just the deals you specifically asked for.
                        </p>
                    </div>

                    <!-- FAQ 5 -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">What if a seller doesn't have what I want?
                        </h3>
                        <p class="text-gray-600">
                            Set a price alert! We'll automatically monitor all sellers and notify you when your perfume
                            becomes available or hits your target price.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ============================================ -->
        <!-- SECTION 7: FINAL CTA -->
        <!-- ============================================ -->
        <section id="get-alerts" class="py-16 lg:py-24 cta-gradient">
            <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <!-- Headline -->
                <h2 class="text-3xl sm:text-4xl font-bold text-white mb-4">
                    Ready to Start Saving?
                </h2>
                <p class="text-lg text-white/90 mb-8">
                    Create a free account to set price alerts and never miss a deal.
                </p>

                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center mb-6">
                    <a href="{{ route('register') }}" class="btn-accent text-gray-900 font-semibold px-8 py-4 rounded-xl text-lg">
                        Create Free Account →
                    </a>
                    <a href="{{ route('perfumes.index') }}" class="bg-white/20 hover:bg-white/30 text-white font-semibold px-8 py-4 rounded-xl text-lg transition-colors">
                        Browse Perfumes First
                    </a>
                </div>

                <!-- Reassurance -->
                <div class="flex flex-col sm:flex-row justify-center gap-4 text-white/70 text-sm mb-8">
                    <span class="flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                clip-rule="evenodd" />
                        </svg>
                        No spam. Only price drops you care about.
                    </span>
                    <span class="flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                clip-rule="evenodd" />
                        </svg>
                        Unsubscribe anytime with one click.
                    </span>
                </div>

                <!-- Urgency Element (Ethical) -->
                <div
                    class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm px-4 py-2 rounded-full text-white text-sm">
                    <span class="pulse-subtle">🔥</span>
                    <span><strong>143 price alerts</strong> triggered in the last 24 hours</span>
                </div>
            </div>
        </section>
    </main>

    <!-- ============================================ -->
    <!-- FOOTER (Minimal) -->
    <!-- ============================================ -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <!-- Logo -->
            <div class="flex items-center justify-center space-x-2 mb-4">
                <div
                    class="w-8 h-8 bg-gradient-to-br from-teal-500 to-teal-700 rounded-lg flex items-center justify-center">
                    <span class="text-white font-bold text-lg">S</span>
                </div>
                <span class="text-xl font-bold text-white">ScentCents</span>
            </div>

            <!-- Tagline -->
            <p class="text-gray-400 mb-8">
                The smarter way to find perfume deals.
            </p>

            <!-- Links -->
            <div class="flex justify-center gap-6 text-sm text-gray-400 mb-8">
                <a href="#" class="hover:text-white transition-colors">Privacy Policy</a>
                <span>•</span>
                <a href="#" class="hover:text-white transition-colors">Terms of Service</a>
                <span>•</span>
                <a href="mailto:hello@scentcents.com" class="hover:text-white transition-colors">Contact</a>
            </div>

            <!-- Copyright -->
            <p class="text-gray-500 text-sm">
                © {{ date('Y') }} ScentCents. Made with 💜 for fragrance enthusiasts.
            </p>
        </div>
    </footer>

</body>

</html>