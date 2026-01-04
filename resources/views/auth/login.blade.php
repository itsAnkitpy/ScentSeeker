@extends('layouts.app')

@section('title', 'Sign In')

@section('content')
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
        <!-- Background Elements -->
        <div class="absolute inset-0 bg-gradient-to-br from-teal-100 via-cyan-50 to-white"></div>
        <div class="absolute top-10 right-10 w-32 h-32 bg-teal-200 rounded-full opacity-20 animate-pulse"></div>
        <div class="absolute bottom-20 left-10 w-24 h-24 bg-cyan-200 rounded-full opacity-30 animate-pulse delay-1000">
        </div>
        <div class="absolute top-1/2 right-1/4 w-16 h-16 bg-amber-200 rounded-full opacity-25 animate-pulse delay-500">
        </div>

        <div class="relative w-full max-w-md space-y-8" x-data="{
                        formData: {
                            email: '',
                            password: '',
                            remember: false
                        },
                        errors: {},
                        message: '',
                        isLoading: false,
                        submitLogin() {
                            this.isLoading = true;
                            this.message = '';
                            this.errors = {};
                            fetch('/api/v1/login', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content')
                                },
                                body: JSON.stringify(this.formData)
                            })
                            .then(response => {
                                if (!response.ok) {
                                    return response.json().then(err => { throw err; });
                                }
                                return response.json();
                            })
                            .then(data => {
                                this.message = 'Login successful! Redirecting...';
                                // Store token in localStorage
                                localStorage.setItem('auth_token', data.access_token);
                                localStorage.setItem('user', JSON.stringify(data.user));
                                // Redirect to home page after short delay to show success message
                                setTimeout(() => {
                                    window.location.href = '/';
                                }, 500);
                            })
                            .catch(errorData => {
                                if (errorData.errors) {
                                    this.errors = errorData.errors;
                                    this.message = 'Please correct the errors below.';
                                } else {
                                    this.message = errorData.message || 'Invalid credentials or an unexpected error occurred.';
                                }
                                console.error('Login error:', errorData);
                            })
                            .finally(() => {
                                this.isLoading = false;
                            });
                        }
                    }">

            <!-- Card Container -->
            <div class="bg-white/80 backdrop-blur-lg rounded-3xl shadow-2xl border border-white/50 p-8">
                <!-- Header -->
                <div class="text-center mb-8">
                    <div class="flex justify-center mb-6">
                        <div
                            class="w-16 h-16 bg-gradient-to-r from-teal-500 to-teal-700 rounded-2xl flex items-center justify-center">
                            <span class="text-white font-bold text-2xl">S</span>
                        </div>
                    </div>
                    <h2 class="text-3xl font-bold font-playfair">
                        <span class="bg-gradient-to-r from-teal-600 to-teal-700 bg-clip-text text-transparent">
                            Welcome Back
                        </span>
                    </h2>
                    <p class="mt-2 text-gray-600">Sign in to your ScentCents account</p>
                </div>

                <!-- Form -->
                <form class="space-y-6" @submit.prevent="submitLogin">
                    <div class="space-y-4">
                        <!-- Email Field -->
                        <div>
                            <label for="email-address" class="block text-sm font-medium text-gray-700 mb-2">
                                Email address
                            </label>
                            <div class="relative">
                                <input id="email-address" name="email" type="email" x-model="formData.email"
                                    autocomplete="email" required
                                    class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-white/90 backdrop-blur-sm focus:border-teal-400 focus:ring-2 focus:ring-teal-200 transition-all text-gray-900 placeholder-gray-500"
                                    placeholder="Enter your email">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                    </svg>
                                </div>
                            </div>
                            <template x-if="errors.email">
                                <p class="mt-2 text-sm text-red-600 bg-red-50 rounded-lg px-3 py-2"
                                    x-text="errors.email[0]"></p>
                            </template>
                        </div>

                        <!-- Password Field -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                Password
                            </label>
                            <div class="relative">
                                <input id="password" name="password" type="password" x-model="formData.password"
                                    autocomplete="current-password" required
                                    class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-white/90 backdrop-blur-sm focus:border-teal-400 focus:ring-2 focus:ring-teal-200 transition-all text-gray-900 placeholder-gray-500"
                                    placeholder="Enter your password">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </div>
                            </div>
                            <template x-if="errors.password">
                                <p class="mt-2 text-sm text-red-600 bg-red-50 rounded-lg px-3 py-2"
                                    x-text="errors.password[0]"></p>
                            </template>
                        </div>
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember-me" name="remember-me" type="checkbox" x-model="formData.remember"
                                class="h-4 w-4 rounded border-gray-300 text-teal-600 focus:ring-teal-500">
                            <label for="remember-me" class="ml-2 block text-sm text-gray-700">
                                Remember me
                            </label>
                        </div>

                        <div class="text-sm" x-data="{ 
                                    showForgotModal: false,
                                    resetEmail: '',
                                    resetMessage: '',
                                    resetLoading: false,
                                    async sendResetLink() {
                                        this.resetLoading = true;
                                        this.resetMessage = '';
                                        try {
                                            const response = await fetch('/api/v1/forgot-password', {
                                                method: 'POST',
                                                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                                                body: JSON.stringify({ email: this.resetEmail })
                                            });
                                            const data = await response.json();
                                            this.resetMessage = data.message || 'If your email exists, you will receive a reset link.';
                                        } catch (e) {
                                            this.resetMessage = 'An error occurred. Please try again.';
                                        }
                                        this.resetLoading = false;
                                    }
                                }">
                            <button type="button" @click="showForgotModal = true"
                                class="font-medium text-teal-600 hover:text-teal-500 transition-colors">
                                Forgot your password?
                            </button>

                            <!-- Forgot Password Modal -->
                            <div x-show="showForgotModal" x-cloak
                                class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
                                @click.self="showForgotModal = false">
                                <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-2xl" @click.stop>
                                    <h3 class="text-xl font-bold text-gray-900 mb-2">Reset Password</h3>
                                    <p class="text-gray-600 text-sm mb-4">Enter your email and we'll send you a reset link.
                                    </p>

                                    <input type="email" x-model="resetEmail" placeholder="your@email.com"
                                        class="w-full px-4 py-3 rounded-xl border border-gray-200 mb-4 focus:border-teal-400 focus:ring-2 focus:ring-teal-200">

                                    <template x-if="resetMessage">
                                        <p class="text-sm text-green-600 bg-green-50 rounded-lg px-3 py-2 mb-4"
                                            x-text="resetMessage"></p>
                                    </template>

                                    <div class="flex gap-3">
                                        <button type="button" @click="sendResetLink()" :disabled="resetLoading"
                                            class="flex-1 bg-gradient-to-r from-teal-500 to-teal-600 text-white py-2 rounded-xl font-medium hover:from-teal-600 hover:to-teal-700 disabled:opacity-50">
                                            <span x-show="!resetLoading">Send Reset Link</span>
                                            <span x-show="resetLoading">Sending...</span>
                                        </button>
                                        <button type="button" @click="showForgotModal = false"
                                            class="px-4 py-2 border border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50">
                                            Cancel
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- Message Display -->
                    <template x-if="message">
                        <div class="rounded-2xl px-4 py-3" :class="{ 
                                         'bg-green-50 border border-green-200 text-green-800': message.includes('successful'), 
                                         'bg-red-50 border border-red-200 text-red-800': !message.includes('successful')
                                     }">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"
                                    x-show="message.includes('successful')">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"
                                    x-show="!message.includes('successful')">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span x-text="message"></span>
                            </div>
                        </div>
                    </template>

                    <!-- Submit Button -->
                    <div>
                        <button type="submit" :disabled="isLoading"
                            class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-lg font-semibold rounded-2xl text-white bg-gradient-to-r from-teal-500 to-teal-600 hover:from-teal-600 hover:to-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 transition-all transform hover:scale-105 shadow-lg hover:shadow-xl"
                            :class="{'opacity-50 cursor-not-allowed transform-none': isLoading}">

                            <span class="absolute left-0 inset-y-0 flex items-center pl-3" x-show="isLoading">
                                <div class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin">
                                </div>
                            </span>

                            <span x-text="isLoading ? 'Signing in...' : 'Sign in'"></span>
                        </button>
                    </div>
                </form>

                <!-- Sign Up Link -->
                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600">
                        Don't have an account?
                        <a href="{{ route('register') }}"
                            class="font-semibold text-teal-600 hover:text-teal-500 transition-colors">
                            Sign up for free
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection