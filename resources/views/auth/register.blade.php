@extends('layouts.app')

@section('title', 'Join ScentSeeker')

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
                username: '',
                email: '',
                password: '',
                password_confirmation: ''
            },
            errors: {},
            message: '',
            isLoading: false,
            submitRegistration() {
                this.isLoading = true;
                this.message = '';
                this.errors = {};
                fetch('/api/v1/register', {
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
                    this.message = 'Registration successful! You can now log in.';
                    // Optionally, store token and redirect or update UI
                    // For now, just clear form and show message
                    console.log('Token:', data.access_token);
                    this.formData.username = '';
                    this.formData.email = '';
                    this.formData.password = '';
                    this.formData.password_confirmation = '';
                    // Consider redirecting to login or dashboard:
                    // window.location.href = '/login'; 
                })
                .catch(errorData => {
                    if (errorData.errors) {
                        this.errors = errorData.errors;
                        this.message = 'Please correct the errors below.';
                    } else {
                        this.message = errorData.message || 'An unexpected error occurred.';
                    }
                    console.error('Registration error:', errorData);
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
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                    </div>
                </div>
                <h2 class="text-3xl font-bold font-playfair">
                    <span class="bg-gradient-to-r from-pink-600 to-purple-600 bg-clip-text text-transparent">
                        Join ScentSeeker
                    </span>
                </h2>
                <p class="mt-2 text-gray-600">Create your account and start discovering amazing fragrances</p>
            </div>

            <!-- Form -->
            <form class="space-y-6" @submit.prevent="submitRegistration">
                <div class="space-y-4">
                    <!-- Username Field -->
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                            Username
                        </label>
                        <div class="relative">
                            <input id="username" 
                                   name="username" 
                                   type="text" 
                                   x-model="formData.username" 
                                   required
                                   class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-white/90 backdrop-blur-sm focus:border-pink-400 focus:ring-2 focus:ring-pink-200 transition-all text-gray-900 placeholder-gray-500"
                                   placeholder="Choose a username">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                        </div>
                        <template x-if="errors.username">
                            <p class="mt-2 text-sm text-red-600 bg-red-50 rounded-lg px-3 py-2" x-text="errors.username[0]"></p>
                        </template>
                    </div>

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
                                   autocomplete="new-password" 
                                   required
                                   class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-white/90 backdrop-blur-sm focus:border-pink-400 focus:ring-2 focus:ring-pink-200 transition-all text-gray-900 placeholder-gray-500"
                                   placeholder="Create a password">
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

                    <!-- Password Confirmation Field -->
                    <div>
                        <label for="password-confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                            Confirm Password
                        </label>
                        <div class="relative">
                            <input id="password-confirmation" 
                                   name="password_confirmation" 
                                   type="password" 
                                   x-model="formData.password_confirmation" 
                                   autocomplete="new-password" 
                                   required
                                   class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-white/90 backdrop-blur-sm focus:border-pink-400 focus:ring-2 focus:ring-pink-200 transition-all text-gray-900 placeholder-gray-500"
                                   placeholder="Confirm your password">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
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
                        
                        <span x-text="isLoading ? 'Creating account...' : 'Create account'"></span>
                    </button>
                </div>
            </form>

            <!-- Sign In Link -->
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Already have an account?
                    <a href="{{ route('login') }}" class="font-semibold text-pink-600 hover:text-pink-500 transition-colors">
                        Sign in here
                    </a>
                </p>
            </div>

            <!-- Terms & Privacy -->
            <div class="mt-4 text-center">
                <p class="text-xs text-gray-500">
                    By creating an account, you agree to our 
                    <a href="#" class="text-pink-600 hover:text-pink-500">Terms of Service</a>
                    and 
                    <a href="#" class="text-pink-600 hover:text-pink-500">Privacy Policy</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection