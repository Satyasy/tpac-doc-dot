<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { Icon } from '@iconify/vue';
import { computed } from 'vue';
import type { AppPageProps } from '@/types';

const page = usePage<AppPageProps>();
const success = computed(() => page.props.flash?.success);

const form = useForm({
    email: '',
});

const submit = () => {
    form.post('/forgot-password');
};
</script>

<template>
    <Head title="Lupa Password" />
    <div class="flex min-h-screen overflow-x-hidden" style="background: linear-gradient(to left, rgba(141, 208, 252, 0.6) 0%, rgba(221, 180, 246, 0.6) 100%);">
        <!-- Left Side - Logo -->
        <div class="relative hidden flex-1 items-center justify-center lg:flex">
            <!-- Decorative circles -->
            <div class="absolute h-[400px] w-[400px] rounded-full bg-[#DDB4F6]/30"></div>
            <div class="absolute h-[300px] w-[300px] rounded-full bg-[#8DD0FC]/20"></div>
            <img src="/images/logo.png" alt="DocDot" class="relative z-10 h-[280px] w-auto drop-shadow-2xl" />
        </div>

        <!-- Right Side - Form -->
        <div class="flex flex-1 items-center justify-center px-6 lg:justify-start lg:pl-12">
            <div class="w-full max-w-[600px] rounded-[30px] bg-white/50 p-8 lg:-ml-16 lg:p-10">
                <!-- Back to Login -->
                <Link href="/login" class="mb-6 inline-flex items-center gap-2 text-[14px] text-[#1b1b18]/70 hover:text-[#1b1b18]">
                    <Icon icon="mdi:arrow-left" class="h-5 w-5" />
                    Kembali ke Login
                </Link>

                <h1 class="text-[28px] font-bold text-[#1b1b18] lg:text-[36px]">Lupa Password?</h1>
                <p class="mt-2 text-[14px] text-[#1b1b18]/70">
                    Jangan khawatir! Masukkan email yang terdaftar dan kami akan kirimkan link untuk reset password Anda.
                </p>

                <!-- Success Message -->
                <div v-if="success" class="mt-6 rounded-lg bg-green-50 border border-green-200 p-4">
                    <div class="flex items-start gap-3">
                        <Icon icon="mdi:check-circle" class="h-5 w-5 text-green-500 flex-shrink-0 mt-0.5" />
                        <div>
                            <p class="text-[14px] font-medium text-green-800">{{ success }}</p>
                            <p class="mt-1 text-[12px] text-green-700">Silakan cek inbox atau folder spam email Anda.</p>
                        </div>
                    </div>
                </div>

                <form @submit.prevent="submit" class="mt-8 space-y-5">
                    <!-- Email -->
                    <div>
                        <label class="mb-1 block text-[12px] text-[#1b1b18]/60">Email</label>
                        <div class="relative">
                            <input 
                                v-model="form.email"
                                type="email" 
                                placeholder="Masukkan email Anda"
                                class="w-full rounded-lg border border-[#1b1b18]/20 bg-transparent px-4 py-3 pl-11 text-[14px] text-[#1b1b18] outline-none focus:border-[#43B3FC] focus:ring-0"
                            />
                            <Icon icon="mdi:email-outline" class="absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-[#1b1b18]/40" />
                        </div>
                        <p v-if="form.errors.email" class="mt-1 text-[12px] text-red-500">{{ form.errors.email }}</p>
                    </div>

                    <!-- Submit Button -->
                    <button 
                        type="submit"
                        :disabled="form.processing || !form.email"
                        class="w-full rounded-lg bg-gradient-to-r from-[#F4AFE9] to-[#8DD0FC] py-3 text-[16px] font-medium text-white shadow-lg transition-all duration-300 hover:shadow-xl hover:scale-[1.02] active:scale-[0.98] disabled:opacity-50 disabled:hover:scale-100"
                    >
                        <span v-if="form.processing" class="flex items-center justify-center gap-2">
                            <Icon icon="mdi:loading" class="h-5 w-5 animate-spin" />
                            Mengirim...
                        </span>
                        <span v-else>Kirim Link Reset Password</span>
                    </button>

                    <!-- Back to Login -->
                    <p class="text-center text-[13px] text-[#1b1b18]">
                        Sudah ingat password? <Link href="/login" class="text-[#FF7CEA] hover:underline">Login</Link>
                    </p>
                </form>

                <!-- Help Section -->
                <div class="mt-8 rounded-lg bg-[#F4AFE9]/10 p-4">
                    <div class="flex items-start gap-3">
                        <Icon icon="mdi:help-circle-outline" class="h-5 w-5 text-[#F4AFE9] flex-shrink-0 mt-0.5" />
                        <div>
                            <p class="text-[13px] font-medium text-[#1b1b18]">Butuh bantuan?</p>
                            <p class="mt-1 text-[12px] text-[#1b1b18]/70">
                                Jika Anda tidak menerima email dalam 5 menit, pastikan email yang dimasukkan benar atau hubungi kami.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
