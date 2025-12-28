<script setup lang="ts">
import { Icon } from '@iconify/vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <footer class="relative mt-8 sm:mt-12 lg:mt-16">
        <!-- Wave SVG di atas -->
        <div class="absolute -top-12 left-0 right-0 sm:-top-16 lg:-top-20">
            <svg viewBox="0 0 1440 120" preserveAspectRatio="none" class="h-16 w-full sm:h-20 lg:h-24">
                <defs>
                    <linearGradient id="waveGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                        <stop offset="0%" stop-color="#8DD0FC" />
                        <stop offset="100%" stop-color="#DDB4F6" />
                    </linearGradient>
                </defs>
                <path d="M0,60 C150,100 350,0 500,50 C650,100 800,20 1000,60 C1150,90 1300,30 1440,60 L1440,120 L0,120 Z" fill="url(#waveGradient)" opacity="0.3" />
                <path d="M0,80 C200,40 400,100 600,60 C800,20 1000,90 1200,50 C1350,20 1400,70 1440,80 L1440,120 L0,120 Z" fill="url(#waveGradient)" opacity="0.5" />
                <path d="M0,90 C100,70 300,110 500,80 C700,50 900,100 1100,70 C1250,50 1350,90 1440,90 L1440,120 L0,120 Z" fill="url(#waveGradient)" opacity="0.7" />
            </svg>
        </div>

        <!-- Footer Content -->
        <div class="bg-gradient-to-r from-[#8DD0FC] to-[#DDB4F6] px-4 pt-10 pb-4 sm:px-6 sm:pt-12 sm:pb-6 lg:px-12 lg:pt-16">
            <div class="mx-auto max-w-6xl">
                <div class="grid grid-cols-2 gap-6 sm:gap-8 lg:grid-cols-4">
                    <!-- DocDot -->
                    <div class="col-span-2 sm:col-span-1">
                        <img src="/images/logo.png" alt="DocDot" class="mb-3 h-12 w-auto sm:mb-4 sm:h-14 lg:h-16" />
                        <p class="text-[13px] leading-relaxed text-[#1b1b18]/80 sm:text-[14px]">
                            Teman cerdas Anda untuk informasi kesehatan terpercaya.
                        </p>
                    </div>

                    <!-- Menu -->
                    <div>
                        <h3 class="mb-3 text-[16px] font-bold text-[#1b1b18] sm:mb-4 sm:text-[18px] lg:text-[20px]">Menu</h3>
                        <ul class="space-y-1.5 text-[13px] text-[#1b1b18]/80 sm:space-y-2 sm:text-[14px]">
                            <li><Link href="/" class="hover:text-[#1b1b18]">Home</Link></li>
                            <li><Link href="/article" class="hover:text-[#1b1b18]">Article</Link></li>
                            <li><Link href="/drug-catalog" class="hover:text-[#1b1b18]">Drug Catalog</Link></li>
                            <li><Link href="/consultation" class="hover:text-[#1b1b18]">Consultation</Link></li>
                        </ul>
                    </div>

                    <!-- Bantuan & Panduan -->
                    <div>
                        <h3 class="mb-3 text-[16px] font-bold text-[#1b1b18] sm:mb-4 sm:text-[18px] lg:text-[20px]">Bantuan</h3>
                        <ul class="space-y-1.5 text-[13px] text-[#1b1b18]/80 sm:space-y-2 sm:text-[14px]">
                            <li><Link href="/about" class="hover:text-[#1b1b18]">Tentang Kami</Link></li>
                            <li><Link href="/contact" class="hover:text-[#1b1b18]">Hubungi Kami</Link></li>
                            <li><Link href="/terms-of-service" class="hover:text-[#1b1b18]">Syarat & Ketentuan</Link></li>
                            <li><Link href="/privacy-policy" class="hover:text-[#1b1b18]">Kebijakan Privasi</Link></li>
                        </ul>
                    </div>

                    <!-- Kontak -->
                    <div class="col-span-2 sm:col-span-1">
                        <h3 class="mb-3 text-[16px] font-bold text-[#1b1b18] sm:mb-4 sm:text-[18px] lg:text-[20px]">Kontak</h3>
                        <div class="space-y-1.5 text-[13px] text-[#1b1b18]/80 sm:space-y-2 sm:text-[14px]">
                            <p class="flex items-center gap-2">
                                <Icon icon="mdi:phone" class="h-4 w-4" />
                                +62 812-3456-7890
                            </p>
                            <p class="flex items-center gap-2">
                                <Icon icon="mdi:email" class="h-4 w-4" />
                                support@docdot.id
                            </p>
                        </div>
                        <!-- Social Icons -->
                        <div class="mt-3 flex gap-2 sm:mt-4 sm:gap-3">
                            <a href="#" class="flex h-7 w-7 items-center justify-center rounded-full bg-black sm:h-8 sm:w-8">
                                <Icon icon="ic:baseline-tiktok" class="h-4 w-4 text-white sm:h-5 sm:w-5" />
                            </a>
                            <a href="#" class="flex h-7 w-7 items-center justify-center rounded-full bg-gradient-to-br from-[#f9ce34] via-[#ee2a7b] to-[#6228d7] sm:h-8 sm:w-8">
                                <Icon icon="mdi:instagram" class="h-4 w-4 text-white sm:h-5 sm:w-5" />
                            </a>
                            <a href="#" class="flex h-7 w-7 items-center justify-center rounded-full bg-[#1877F2] sm:h-8 sm:w-8">
                                <Icon icon="mdi:facebook" class="h-4 w-4 text-white sm:h-5 sm:w-5" />
                            </a>
                            <a href="#" class="flex h-7 w-7 items-center justify-center rounded-full bg-black sm:h-8 sm:w-8">
                                <Icon icon="mdi:twitter" class="h-4 w-4 text-white sm:h-5 sm:w-5" />
                            </a>
                        </div>
                    </div>
                </div>

                <!-- All Right Reserved -->
                <div class="mt-6 border-t border-[#1b1b18]/10 pt-3 sm:mt-8 sm:pt-4">
                    <p class="flex items-center justify-center gap-1.5 text-[11px] text-[#1b1b18]/60 sm:gap-2 sm:text-[12px]">
                        <Icon icon="mdi:copyright" class="h-3.5 w-3.5 sm:h-4 sm:w-4" />
                        {{ new Date().getFullYear() }} DocDot. All rights reserved.
                    </p>
                </div>
            </div>
        </div>
    </footer>
</template>
