<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Icon } from '@iconify/vue';
import { ref, computed } from 'vue';

const showPassword = ref(false);
const showConfirmPassword = ref(false);
const agreeTerms = ref(false);

const form = useForm({
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    password: '',
    password_confirmation: '',
});

const canSubmit = computed(() => agreeTerms.value && !form.processing);

const submit = () => {
    if (!agreeTerms.value) return;
    form.post('/register');
};
</script>

<template>
    <Head title="Sign Up" />
    <div class="flex min-h-screen overflow-x-hidden" style="background: linear-gradient(to left, rgba(141, 208, 252, 0.6) 0%, rgba(221, 180, 246, 0.6) 100%);">
        <!-- Left Side - Logo -->
        <div class="relative hidden flex-1 items-center justify-center lg:flex">
            <!-- Decorative circles -->
            <div class="absolute h-[400px] w-[400px] rounded-full bg-[#DDB4F6]/30"></div>
            <div class="absolute h-[300px] w-[300px] rounded-full bg-[#8DD0FC]/20"></div>
            <img src="/images/logo.png" alt="DocDot" class="relative z-10 h-[280px] w-auto drop-shadow-2xl" />
        </div>

        <!-- Right Side - Register Form -->
        <div class="flex flex-1 items-center justify-center px-4 py-6 sm:px-6 sm:py-8 lg:justify-start lg:pl-12">
            <div class="w-full max-w-[600px] rounded-2xl bg-white/50 p-5 sm:rounded-[30px] sm:p-8 lg:-ml-16 lg:p-10">
                <!-- Mobile Logo -->
                <div class="mb-4 flex justify-center lg:hidden sm:mb-6">
                    <img src="/images/logo.png" alt="DocDot" class="h-16 w-auto sm:h-20" />
                </div>
                
                <h1 class="text-[22px] font-bold text-[#1b1b18] sm:text-[28px] lg:text-[36px]">Sign up</h1>
                <p class="mt-1 text-[12px] text-[#1b1b18]/70 sm:mt-2 sm:text-[14px]">Let's get you all set up so you can access your personal account.</p>

                <form @submit.prevent="submit" class="mt-5 space-y-3 sm:mt-8 sm:space-y-5">
                    <!-- First Name & Last Name -->
                    <div class="flex flex-col gap-3 sm:flex-row sm:gap-4">
                        <div class="flex-1">
                            <label class="mb-1 block text-[11px] text-[#1b1b18]/60 sm:text-[12px]">First Name</label>
                            <input 
                                v-model="form.first_name"
                                type="text" 
                                placeholder="John"
                                class="w-full rounded-lg border border-[#1b1b18]/20 bg-transparent px-3 py-2.5 text-[13px] text-[#1b1b18] outline-none focus:border-[#43B3FC] focus:ring-0 sm:px-4 sm:py-3 sm:text-[14px]"
                            />
                            <p v-if="form.errors.first_name" class="mt-1 text-[11px] text-red-500 sm:text-[12px]">{{ form.errors.first_name }}</p>
                        </div>
                        <div class="flex-1">
                            <label class="mb-1 block text-[11px] text-[#1b1b18]/60 sm:text-[12px]">Last Name</label>
                            <input 
                                v-model="form.last_name"
                                type="text" 
                                placeholder="Doe"
                                class="w-full rounded-lg border border-[#1b1b18]/20 bg-transparent px-3 py-2.5 text-[13px] text-[#1b1b18] outline-none focus:border-[#43B3FC] focus:ring-0 sm:px-4 sm:py-3 sm:text-[14px]"
                            />
                            <p v-if="form.errors.last_name" class="mt-1 text-[11px] text-red-500 sm:text-[12px]">{{ form.errors.last_name }}</p>
                        </div>
                    </div>

                    <!-- Email & Phone Number -->
                    <div class="flex flex-col gap-3 sm:flex-row sm:gap-4">
                        <div class="flex-1">
                            <label class="mb-1 block text-[11px] text-[#1b1b18]/60 sm:text-[12px]">Email</label>
                            <input 
                                v-model="form.email"
                                type="email" 
                                placeholder="john.doe@gmail.com"
                                class="w-full rounded-lg border border-[#1b1b18]/20 bg-transparent px-3 py-2.5 text-[13px] text-[#1b1b18] outline-none focus:border-[#43B3FC] focus:ring-0 sm:px-4 sm:py-3 sm:text-[14px]"
                            />
                            <p v-if="form.errors.email" class="mt-1 text-[11px] text-red-500 sm:text-[12px]">{{ form.errors.email }}</p>
                        </div>
                        <div class="flex-1">
                            <label class="mb-1 block text-[11px] text-[#1b1b18]/60 sm:text-[12px]">Phone Number</label>
                            <input 
                                v-model="form.phone"
                                type="tel" 
                                placeholder="08123456789"
                                class="w-full rounded-lg border border-[#1b1b18]/20 bg-transparent px-3 py-2.5 text-[13px] text-[#1b1b18] outline-none focus:border-[#43B3FC] focus:ring-0 sm:px-4 sm:py-3 sm:text-[14px]"
                            />
                            <p v-if="form.errors.phone" class="mt-1 text-[11px] text-red-500 sm:text-[12px]">{{ form.errors.phone }}</p>
                        </div>
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

                    <!-- Confirm Password -->
                    <div>
                        <label class="mb-1 block text-[12px] text-[#1b1b18]/60">Confirm Password</label>
                        <div class="relative">
                            <input 
                                v-model="form.password_confirmation"
                                :type="showConfirmPassword ? 'text' : 'password'" 
                                placeholder="••••••••••••••••••••"
                                class="w-full rounded-lg border border-[#1b1b18]/20 bg-transparent px-4 py-3 pr-12 text-[14px] text-[#1b1b18] outline-none focus:border-[#43B3FC] focus:ring-0"
                            />
                            <button 
                                type="button"
                                @click="showConfirmPassword = !showConfirmPassword"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-[#1b1b18]/40"
                            >
                                <Icon :icon="showConfirmPassword ? 'mdi:eye' : 'mdi:eye-off'" class="h-5 w-5" />
                            </button>
                        </div>
                        <p v-if="form.errors.password_confirmation" class="mt-1 text-[12px] text-red-500">{{ form.errors.password_confirmation }}</p>
                    </div>

                    <!-- Agree Terms -->
                    <div class="flex items-center gap-2">
                        <input 
                            v-model="agreeTerms"
                            type="checkbox" 
                            class="h-4 w-4 rounded border-[#1b1b18]/20 text-[#8DD0FC] focus:ring-[#8DD0FC]"
                        />
                        <span class="text-[13px] text-[#1b1b18]">
                            I agree to all the <Link href="/terms-of-service" class="text-[#FF7CEA]">Terms</Link> and <Link href="/privacy-policy" class="text-[#FF7CEA]">Privacy Policies</Link>
                        </span>
                    </div>

                    <!-- Create Account Button -->
                    <button 
                        type="submit"
                        :disabled="!canSubmit"
                        class="w-full rounded-lg bg-gradient-to-r from-[#F4AFE9] to-[#8DD0FC] py-3 text-[16px] font-medium text-white shadow-lg transition-all duration-300 hover:shadow-xl hover:scale-[1.02] active:scale-[0.98] disabled:opacity-50 disabled:hover:scale-100 disabled:cursor-not-allowed"
                    >
                        {{ form.processing ? 'Loading...' : 'Create account' }}
                    </button>

                    <!-- Login link -->
                    <p class="text-center text-[13px] text-[#1b1b18]">
                        Already have an account? <Link href="/login" class="text-[#FF7CEA]">Login</Link>
                    </p>

                    <!-- Divider -->
                    <div class="flex items-center gap-4">
                        <div class="h-px flex-1 bg-[#1b1b18]/10"></div>
                        <span class="text-[12px] text-[#1b1b18]/40">Or Sign up with</span>
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
                </form>
            </div>
        </div>
    </div>
</template>
