@extends('layouts.app')

@section('title', 'Reset Password')

@section('content')
    <div
        class="min-h-screen bg-gradient-to-br from-rose-50 via-pink-50 to-purple-50 flex items-center justify-center py-12 px-4">
        <div class="max-w-md w-full" x-data="{
                formData: { token: '{{ $token }}', email: '{{ request()->email }}', password: '', password_confirmation: '' },
                loginUrl: '{{ request()->query('redirect', '/login') }}',
                message: '',
                errors: {},
                isLoading: false,
                async submitReset() {
                    this.isLoading = true;
                    this.message = '';
                    this.errors = {};
                    try {
                        const response = await fetch('/api/v1/reset-password', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                            body: JSON.stringify(this.formData)
                        });
                        const data = await response.json();
                        if (response.ok) {
                            this.message = 'Password reset successfully! Redirecting to login...';
                            setTimeout(() => window.location.href = this.loginUrl, 2000);
                        } else {
                            this.errors = data.errors || {};
                            this.message = data.message || 'Failed to reset password';
                        }
                    } catch (e) {
                        this.message = 'An error occurred. Please try again.';
                    }
                    this.isLoading = false;
                }
             }">

            <div class="bg-white/80 backdrop-blur-lg rounded-3xl shadow-2xl border border-white/50 p-8">
                <div class="text-center mb-8">
                    <div class="flex justify-center mb-6">
                        <div
                            class="w-16 h-16 bg-gradient-to-r from-pink-500 to-purple-600 rounded-2xl flex items-center justify-center">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                            </svg>
                        </div>
                    </div>
                    <h2 class="text-3xl font-bold font-playfair">
                        <span class="bg-gradient-to-r from-pink-600 to-purple-600 bg-clip-text text-transparent">
                            Reset Password
                        </span>
                    </h2>
                    <p class="mt-2 text-gray-600">Enter your new password below</p>
                </div>

                <form class="space-y-6" @submit.prevent="submitReset">
                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" x-model="formData.email" required
                            class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-white/90 focus:border-pink-400 focus:ring-2 focus:ring-pink-200"
                            placeholder="your@email.com">
                    </div>

                    <!-- New Password -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                        <input type="password" x-model="formData.password" required minlength="8"
                            class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-white/90 focus:border-pink-400 focus:ring-2 focus:ring-pink-200"
                            placeholder="Minimum 8 characters">
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                        <input type="password" x-model="formData.password_confirmation" required
                            class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-white/90 focus:border-pink-400 focus:ring-2 focus:ring-pink-200"
                            placeholder="Confirm your password">
                    </div>

                    <!-- Message -->
                    <template x-if="message">
                        <div class="rounded-2xl px-4 py-3" :class="{ 
                            'bg-green-50 border border-green-200 text-green-800': message.includes('successfully'), 
                            'bg-red-50 border border-red-200 text-red-800': !message.includes('successfully') 
                        }">
                            <p x-text="message"></p>
                        </div>
                    </template>

                    <!-- Submit -->
                    <button type="submit" :disabled="isLoading"
                        class="w-full bg-gradient-to-r from-pink-500 to-purple-600 text-white py-3 rounded-2xl font-semibold hover:from-pink-600 hover:to-purple-700 transition-all disabled:opacity-50">
                        <span x-show="!isLoading">Reset Password</span>
                        <span x-show="isLoading">Resetting...</span>
                    </button>

                    <div class="text-center">
                        <a :href="loginUrl" class="text-pink-600 hover:text-pink-500 font-medium">← Back to Login</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection