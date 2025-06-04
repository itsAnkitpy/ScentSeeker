@extends('layouts.app')

@section('title', 'Sign In')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
    <!-- Background Elements -->
    <div class="absolute inset-0 bg-gradient-to-br from-pink-100 via-purple-50 to-indigo-100"></div>
    <div class="absolute top-10 right-10 w-32 h-32 bg-pink-200 rounded-full opacity-20 animate-pulse"></div>
    <div class="absolute bottom-20 left-10 w-24 h-24 bg-purple-200 rounded-full opacity-30 animate-pulse delay-1000"></div>
    <div class="absolute top-1/2 right-1/4 w-16 h-16 bg-indigo-200 rounded-full opacity-25 animate-pulse delay-500"></div>
    
    <div class="relative w-full max-w-md space-y-8"
         x-data="{
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
                    // Store token (e.g., in localStorage)
                    localStorage.setItem('auth_token', data.access_token);
                    localStorage.setItem('user', JSON.stringify(data.user));
                    // Redirect to a dashboard or home page
                    // For now, just log and clear form
                    console.log('Token:', data.access_token);
                    console.log('User:', data.user);
                    this.formData.email = '';
                    this.formData.password = '';
                    // window.location.href = '/dashboard'; // Or appropriate redirect
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
                    <div class="w-16 h-16 bg-gradient-to-r from-pink-500 to-purple-600 rounded-2xl flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2L13.09 8.26L20 9L13.09 9.74L12 16L10.91 9.74L4 9L10.91 8.26L12 2Z"/>
                        </svg>
                    </div>
                </div>
                <h2 class="text-3xl font-bold font-playfair">
                    <span class="bg-gradient-to-r from-pink-600 to-purple-600 bg-clip-text text-transparent">
                        Welcome Back
                    </span>
                </h2>
                <p class="mt-2 text-gray-600">Sign in to your ScentSeeker account</p>
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
                            <input id="email-address" 
                                   name="email" 
                                   type="email" 
                                   x-model="formData.email" 
                                   autocomplete="email" 
                                   required
                                   class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-white/90 backdrop-blur-sm focus:border-pink-400 focus:ring-2 focus:ring-pink-200 transition-all text-gray-900 placeholder-gray-500"
                                   placeholder="Enter your email">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                                </svg>
                            </div>
                        </div>
                        <template x-if="errors.email">
                            <p class="mt-2 text-sm text-red-600 bg-red-50 rounded-lg px-3 py-2" x-text="errors.email[0]"></p>
                        </template>
                    </div>

                    <!-- Password Field -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            Password
                        </label>
                        <div class="relative">
                            <input id="password" 
                                   name="password" 
                                   type="password" 
                                   x-model="formData.password" 
                                   autocomplete="current-password" 
                                   required
                                   class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-white/90 backdrop-blur-sm focus:border-pink-400 focus:ring-2 focus:ring-pink-200 transition-all text-gray-900 placeholder-gray-500"
                                   placeholder="Enter your password">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                        </div>
                        <template x-if="errors.password">
                            <p class="mt-2 text-sm text-red-600 bg-red-50 rounded-lg px-3 py-2" x-text="errors.password[0]"></p>
                        </template>
                    </div>
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember-me" 
                               name="remember-me" 
                               type="checkbox" 
                               x-model="formData.remember"
                               class="h-4 w-4 rounded border-gray-300 text-pink-600 focus:ring-pink-500">
                        <label for="remember-me" class="ml-2 block text-sm text-gray-700">
                            Remember me
                        </label>
                    </div>

                    <div class="text-sm">
                        <a href="#" class="font-medium text-pink-600 hover:text-pink-500 transition-colors">
                            Forgot your password?
                        </a>
                    </div>
                </div>
                
                <!-- Message Display -->
                <template x-if="message">
                    <div class="rounded-2xl px-4 py-3" 
                         :class="{ 
                             'bg-green-50 border border-green-200 text-green-800': message.includes('successful'), 
                             'bg-red-50 border border-red-200 text-red-800': !message.includes('successful')
                         }">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" x-show="message.includes('successful')">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" x-show="!message.includes('successful')">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <span x-text="message"></span>
                        </div>
                    </div>
                </template>

                <!-- Submit Button -->
                <div>
                    <button type="submit" 
                            :disabled="isLoading"
                            class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-lg font-semibold rounded-2xl text-white bg-gradient-to-r from-pink-500 to-purple-600 hover:from-pink-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500 transition-all transform hover:scale-105 shadow-lg hover:shadow-xl"
                            :class="{'opacity-50 cursor-not-allowed transform-none': isLoading}">
                        
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3" x-show="isLoading">
                            <div class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                        </span>
                        
                        <span x-text="isLoading ? 'Signing in...' : 'Sign in'"></span>
                    </button>
                </div>
            </form>

            <!-- Sign Up Link -->
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Don't have an account?
                    <a href="{{ route('register') }}" class="font-semibold text-pink-600 hover:text-pink-500 transition-colors">
                        Sign up for free
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection