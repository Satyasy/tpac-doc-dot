<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { Icon } from '@iconify/vue';
import { ref, computed } from 'vue';
import type { AppPageProps } from '@/types';

const page = usePage<AppPageProps>();
const success = computed(() => page.props.flash?.success);

const showPassword = ref(false);

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post('/login');
};
</script>

<template>
    <Head title="Login" />
    <div class="flex min-h-screen overflow-x-hidden" style="background: linear-gradient(to left, rgba(141, 208, 252, 0.6) 0%, rgba(221, 180, 246, 0.6) 100%);">
        <!-- Left Side - Logo -->
        <div class="relative hidden flex-1 items-center justify-center lg:flex">
            <!-- Decorative circles -->
            <div class="absolute h-[400px] w-[400px] rounded-full bg-[#DDB4F6]/30"></div>
            <div class="absolute h-[300px] w-[300px] rounded-full bg-[#8DD0FC]/20"></div>
            <img src="/images/logo.png" alt="DocDot" class="relative z-10 h-[280px] w-auto drop-shadow-2xl" />
        </div>

        <!-- Right Side - Login Form -->
        <div class="flex flex-1 items-center justify-center px-4 py-8 sm:px-6 lg:justify-start lg:pl-12">
            <div class="w-full max-w-[600px] rounded-2xl bg-white/50 p-6 sm:rounded-[30px] sm:p-8 lg:-ml-16 lg:p-10">
                <!-- Mobile Logo -->
                <div class="mb-6 flex justify-center lg:hidden">
                    <img src="/images/logo.png" alt="DocDot" class="h-20 w-auto sm:h-24" />
                </div>
                
                <h1 class="text-[24px] font-bold text-[#1b1b18] sm:text-[28px] lg:text-[36px]">Login</h1>
                <p class="mt-1 text-[13px] text-[#1b1b18]/70 sm:mt-2 sm:text-[14px]">Login to access your DocDot account</p>

                <!-- Success Message -->
                <div v-if="success" class="mt-4 rounded-lg bg-green-50 border border-green-200 p-3 sm:mt-6 sm:p-4">
                    <div class="flex items-start gap-2 sm:gap-3">
                        <Icon icon="mdi:check-circle" class="h-4 w-4 text-green-500 flex-shrink-0 mt-0.5 sm:h-5 sm:w-5" />
                        <p class="text-[13px] text-green-800 sm:text-[14px]">{{ success }}</p>
                    </div>
                </div>

                <form @submit.prevent="submit" class="mt-6 space-y-4 sm:mt-8 sm:space-y-5">
                    <!-- Email -->
                    <div>
                        <label class="mb-1 block text-[12px] text-[#1b1b18]/60">Email</label>
                        <input 
                            v-model="form.email"
                            type="email" 
                            placeholder="example@gmail.com"
                            class="w-full rounded-lg border border-[#1b1b18]/20 bg-transparent px-4 py-3 text-[14px] text-[#1b1b18] outline-none focus:border-[#43B3FC] focus:ring-0"
                        />
                        <p v-if="form.errors.email" class="mt-1 text-[12px] text-red-500">{{ form.errors.email }}</p>
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="mb-1 block text-[12px] text-[#1b1b18]/60">Password</label>
                        <div class="relative">
                            <input 
                                v-model="form.password"
                                :type="showPassword ? 'text' : 'password'" 
                                placeholder="••••••••••••••••••••"
                                class="w-full rounded-lg border border-[#1b1b18]/20 bg-transparent px-4 py-3 pr-12 text-[14px] text-[#1b1b18] outline-none focus:border-[#43B3FC] focus:ring-0"
                            />
                            <button 
                                type="button"
                                @click="showPassword = !showPassword"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-[#1b1b18]/40"
                            >
                                <Icon :icon="showPassword ? 'mdi:eye' : 'mdi:eye-off'" class="h-5 w-5" />
                            </button>
                        </div>
                        <p v-if="form.errors.password" class="mt-1 text-[12px] text-red-500">{{ form.errors.password }}</p>
                    </div>

                    <!-- Remember me & Forgot Password -->
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 text-[13px] text-[#1b1b18]">
                            <input 
                                v-model="form.remember"
                                type="checkbox" 
                                class="h-4 w-4 rounded border-[#1b1b18]/20"
                            />
                            Remember me
                        </label>
                        <Link href="/forgot-password" class="text-[13px] text-[#FF7CEA] hover:underline">Forgot Password</Link>
                    </div>

                    <!-- Login Button -->
                    <button 
                        type="submit"
                        :disabled="form.processing"
                        class="w-full rounded-lg bg-gradient-to-r from-[#F4AFE9] to-[#8DD0FC] py-3 text-[16px] font-medium text-white shadow-lg transition-all duration-300 hover:shadow-xl hover:scale-[1.02] active:scale-[0.98] disabled:opacity-50 disabled:hover:scale-100"
                    >
                        {{ form.processing ? 'Loading...' : 'Login' }}
                    </button>

                    <!-- Sign up link -->
                    <p class="text-center text-[13px] text-[#1b1b18]">
                        Don't have an account? <Link href="/register" class="text-[#FF7CEA]">Sign up</Link>
                    </p>

                    <!-- Divider -->
                    <div class="flex items-center gap-4">
                        <div class="h-px flex-1 bg-[#1b1b18]/10"></div>
                        <span class="text-[12px] text-[#1b1b18]/40">Or login with</span>
                        <div class="h-px flex-1 bg-[#1b1b18]/10"></div>
                    </div>

                    <!-- Social Login -->
                    <div class="flex gap-4">
                        <a 
                            href="/auth/google" 
                            class="flex flex-1 items-center justify-center gap-3 rounded-lg border border-[#1b1b18]/10 bg-white py-3 transition-all hover:bg-[#f5f5f5] hover:shadow-md"
                        >
                            <Icon icon="logos:google-icon" class="h-5 w-5" />
                            <span class="text-[14px] font-medium text-[#1b1b18]">Google</span>
                        </a>
                    </div>

                    <!-- Google Error -->
                    <p v-if="form.errors.google" class="text-center text-[12px] text-red-500">{{ form.errors.google }}</p>
                </form>
            </div>
        </div>
    </div>
</template>
