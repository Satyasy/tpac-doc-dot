<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Icon } from '@iconify/vue';
import { ref } from 'vue';

interface Props {
    token: string;
    email: string;
}

const props = defineProps<Props>();

const showPassword = ref(false);
const showConfirmPassword = ref(false);

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post('/reset-password');
};
</script>

<template>
    <Head title="Reset Password" />
    <div class="flex min-h-screen overflow-x-hidden" style="background: linear-gradient(to left, rgba(141, 208, 252, 0.6) 0%, rgba(221, 180, 246, 0.6) 100%);">
        <!-- Left Side - Logo -->
        <div class="relative hidden flex-1 items-center justify-center lg:flex">
            <!-- Decorative circles -->
            <div class="absolute h-[400px] w-[400px] rounded-full bg-[#DDB4F6]/30"></div>
            <div class="absolute h-[300px] w-[300px] rounded-full bg-[#8DD0FC]/20"></div>
            <img src="/images/logo.png" alt="DocDot" class="relative z-10 h-[280px] w-auto drop-shadow-2xl" />
        </div>

        <!-- Right Side - Form -->
        <div class="flex flex-1 items-center justify-center px-4 py-8 sm:px-6 lg:justify-start lg:pl-12">
            <div class="w-full max-w-[600px] rounded-2xl bg-white/50 p-6 sm:rounded-[30px] sm:p-8 lg:-ml-16 lg:p-10">
                <!-- Icon -->
                <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-gradient-to-r from-[#F4AFE9] to-[#8DD0FC] sm:mb-6 sm:h-16 sm:w-16">
                    <Icon icon="mdi:lock-reset" class="h-6 w-6 text-white sm:h-8 sm:w-8" />
                </div>

                <h1 class="text-[24px] font-bold text-[#1b1b18] sm:text-[28px] lg:text-[36px]">Reset Password</h1>
                <p class="mt-1 text-[13px] text-[#1b1b18]/70 sm:mt-2 sm:text-[14px]">
                    Buat password baru untuk akun <span class="font-medium text-[#1b1b18]">{{ email }}</span>
                </p>

                <form @submit.prevent="submit" class="mt-6 space-y-4 sm:mt-8 sm:space-y-5">
                    <!-- Hidden Fields -->
                    <input type="hidden" v-model="form.token" />
                    <input type="hidden" v-model="form.email" />

                    <!-- New Password -->
                    <div>
                        <label class="mb-1 block text-[12px] text-[#1b1b18]/60">Password Baru</label>
                        <div class="relative">
                            <input 
                                v-model="form.password"
                                :type="showPassword ? 'text' : 'password'" 
                                placeholder="Minimal 8 karakter"
                                class="w-full rounded-lg border border-[#1b1b18]/20 bg-transparent px-4 py-3 pr-12 text-[14px] text-[#1b1b18] outline-none focus:border-[#43B3FC] focus:ring-0"
                            />
                            <button 
                                type="button"
                                @click="showPassword = !showPassword"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-[#1b1b18]/40 hover:text-[#1b1b18]/60"
                            >
                                <Icon :icon="showPassword ? 'mdi:eye' : 'mdi:eye-off'" class="h-5 w-5" />
                            </button>
                        </div>
                        <p v-if="form.errors.password" class="mt-1 text-[12px] text-red-500">{{ form.errors.password }}</p>
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label class="mb-1 block text-[12px] text-[#1b1b18]/60">Konfirmasi Password Baru</label>
                        <div class="relative">
                            <input 
                                v-model="form.password_confirmation"
                                :type="showConfirmPassword ? 'text' : 'password'" 
                                placeholder="Ulangi password baru"
                                class="w-full rounded-lg border border-[#1b1b18]/20 bg-transparent px-4 py-3 pr-12 text-[14px] text-[#1b1b18] outline-none focus:border-[#43B3FC] focus:ring-0"
                            />
                            <button 
                                type="button"
                                @click="showConfirmPassword = !showConfirmPassword"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-[#1b1b18]/40 hover:text-[#1b1b18]/60"
                            >
                                <Icon :icon="showConfirmPassword ? 'mdi:eye' : 'mdi:eye-off'" class="h-5 w-5" />
                            </button>
                        </div>
                        <p v-if="form.errors.password_confirmation" class="mt-1 text-[12px] text-red-500">{{ form.errors.password_confirmation }}</p>
                    </div>

                    <!-- Error for token/email -->
                    <div v-if="form.errors.email" class="rounded-lg bg-red-50 border border-red-200 p-4">
                        <div class="flex items-start gap-3">
                            <Icon icon="mdi:alert-circle" class="h-5 w-5 text-red-500 flex-shrink-0 mt-0.5" />
                            <p class="text-[13px] text-red-700">{{ form.errors.email }}</p>
                        </div>
                    </div>

                    <!-- Password Requirements -->
                    <div class="rounded-lg bg-[#8DD0FC]/10 p-4">
                        <p class="mb-2 text-[12px] font-medium text-[#1b1b18]">Password harus memenuhi:</p>
                        <ul class="space-y-1 text-[12px] text-[#1b1b18]/70">
                            <li class="flex items-center gap-2">
                                <Icon 
                                    :icon="form.password.length >= 8 ? 'mdi:check-circle' : 'mdi:circle-outline'" 
                                    :class="form.password.length >= 8 ? 'text-green-500' : 'text-[#1b1b18]/30'"
                                    class="h-4 w-4"
                                />
                                Minimal 8 karakter
                            </li>
                            <li class="flex items-center gap-2">
                                <Icon 
                                    :icon="form.password === form.password_confirmation && form.password_confirmation !== '' ? 'mdi:check-circle' : 'mdi:circle-outline'" 
                                    :class="form.password === form.password_confirmation && form.password_confirmation !== '' ? 'text-green-500' : 'text-[#1b1b18]/30'"
                                    class="h-4 w-4"
                                />
                                Password dan konfirmasi cocok
                            </li>
                        </ul>
                    </div>

                    <!-- Submit Button -->
                    <button 
                        type="submit"
                        :disabled="form.processing || form.password.length < 8 || form.password !== form.password_confirmation"
                        class="w-full rounded-lg bg-gradient-to-r from-[#F4AFE9] to-[#8DD0FC] py-3 text-[16px] font-medium text-white shadow-lg transition-all duration-300 hover:shadow-xl hover:scale-[1.02] active:scale-[0.98] disabled:opacity-50 disabled:hover:scale-100"
                    >
                        <span v-if="form.processing" class="flex items-center justify-center gap-2">
                            <Icon icon="mdi:loading" class="h-5 w-5 animate-spin" />
                            Menyimpan...
                        </span>
                        <span v-else>Reset Password</span>
                    </button>

                    <!-- Back to Login -->
                    <p class="text-center text-[13px] text-[#1b1b18]">
                        <Link href="/login" class="text-[#FF7CEA] hover:underline">Kembali ke Login</Link>
                    </p>
                </form>
            </div>
        </div>
    </div>
</template>
