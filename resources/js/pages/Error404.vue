<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Icon } from '@iconify/vue';
</script>

<template>
    <Head title="404 - Halaman Tidak Ditemukan" />

    <div 
        class="flex min-h-screen flex-col items-center justify-center px-4 pt-16 sm:pt-20 lg:pt-22 font-[Poppins] sm:px-6"
        style="background: linear-gradient(to left, rgba(141, 208, 252, 0.6) 0%, rgba(221, 180, 246, 0.6) 100%)"
    >
        <div class="text-center">
            <!-- 404 Illustration -->
            <div class="relative mx-auto mb-6 w-48 sm:mb-8 sm:w-64">
                <div class="text-[100px] font-bold leading-none text-white/50 sm:text-[150px]">404</div>
                <div class="absolute inset-0 flex items-center justify-center">

                </div>
            </div>

            <!-- Message -->
            <h1 class="mb-3 text-[22px] font-bold text-[#1b1b18] sm:mb-4 sm:text-[28px] lg:text-[36px]">
                Halaman Tidak Ditemukan
            </h1>
            <p class="mx-auto mb-6 max-w-md text-[13px] text-[#1b1b18]/70 sm:mb-8 sm:text-[14px] lg:text-[16px]">
                Maaf, halaman yang Anda cari tidak dapat ditemukan. Mungkin halaman telah dipindahkan atau dihapus.
            </p>

            <!-- Actions -->
            <div class="flex flex-col justify-center gap-3 sm:flex-row sm:gap-4">
                <Link 
                    href="/"
                    class="inline-flex items-center justify-center gap-2 rounded-full bg-gradient-to-r from-[#F4AFE9] to-[#8DD0FC] px-5 py-2.5 text-[13px] font-medium text-white transition-all hover:shadow-lg sm:px-6 sm:py-3 sm:text-[14px]"
                >
                    <Icon icon="mdi:home" class="h-4 w-4 sm:h-5 sm:w-5" />
                    Kembali ke Beranda
                </Link>
                <Link 
                    href="/consultation"
                    class="inline-flex items-center justify-center gap-2 rounded-full border-2 border-[#1b1b18] px-5 py-2.5 text-[13px] font-medium text-[#1b1b18] transition-all hover:bg-[#1b1b18] hover:text-white sm:px-6 sm:py-3 sm:text-[14px]"
                >
                    <Icon icon="mdi:chat-outline" class="h-4 w-4 sm:h-5 sm:w-5" />
                    Konsultasi
                </Link>
            </div>

            <!-- Quick Links -->
            <div class="mt-10 sm:mt-12">
                <p class="mb-3 text-[12px] text-[#1b1b18]/60 sm:mb-4 sm:text-[13px]">Atau kunjungi halaman populer:</p>
                <div class="flex flex-wrap justify-center gap-2 sm:gap-3">
                    <Link 
                        href="/article" 
                        class="rounded-full bg-white px-3 py-1.5 text-[12px] text-[#1b1b18] transition-all hover:bg-[#F8F8F8] sm:px-4 sm:py-2 sm:text-[13px]"
                    >
                        Artikel
                    </Link>
                    <Link 
                        href="/drug-catalog" 
                        class="rounded-full bg-white px-3 py-1.5 text-[12px] text-[#1b1b18] transition-all hover:bg-[#F8F8F8] sm:px-4 sm:py-2 sm:text-[13px]"
                    >
                        Katalog Obat
                    </Link>
                    <Link 
                        href="/about" 
                        class="rounded-full bg-white px-3 py-1.5 text-[12px] text-[#1b1b18] transition-all hover:bg-[#F8F8F8] sm:px-4 sm:py-2 sm:text-[13px]"
                    >
                        Tentang Kami
                    </Link>
                    <Link 
                        href="/contact" 
                        class="rounded-full bg-white px-3 py-1.5 text-[12px] text-[#1b1b18] transition-all hover:bg-[#F8F8F8] sm:px-4 sm:py-2 sm:text-[13px]"
                    >
                        Hubungi Kami
                    </Link>
                </div>
            </div>
        </div>

        <!-- Decorative Elements -->
        <div class="pointer-events-none fixed inset-0 overflow-hidden">
            <div class="absolute -top-24 -left-24 h-48 w-48 rounded-full bg-[#F4AFE9]/20 blur-3xl"></div>
            <div class="absolute -bottom-24 -right-24 h-48 w-48 rounded-full bg-[#8DD0FC]/20 blur-3xl"></div>
        </div>
    </div>
</template>
