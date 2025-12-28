<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import Navbar from '@/components/Navbar.vue';
import Footer from '@/components/Footer.vue';
import { Icon } from '@iconify/vue';
import { useScrollAnimation } from '@/composables/useScrollAnimation';

useScrollAnimation();
</script>

<template>
    <Head title="Welcome" />
    <div class="min-h-screen overflow-x-hidden bg-[#FAFAFA] pt-16 sm:pt-20 lg:pt-22">
        <Navbar />

        <!-- Hero Section -->
        <section class="relative overflow-hidden px-4 pt-6 sm:px-6 sm:pt-8 lg:px-12">
            <div class="relative z-10 mx-auto flex max-w-7xl flex-col items-center gap-6 text-center lg:flex-row lg:items-start lg:justify-between lg:text-left">
                <div class="scroll-animate max-w-xl pt-4 lg:pt-16">
                    <h1 class="text-[28px] font-semibold leading-tight text-[#1b1b18] sm:text-[36px] lg:text-[48px]">
                        Lindungi Diri Anda<br />Lindungi Keluarga
                    </h1>
                    <p class="mt-3 text-[16px] font-light text-[#1b1b18] sm:mt-4 sm:text-[18px] lg:text-[20px]">
                        Konsultasikan Keluhan Kesehatan Anda<br class="hidden sm:block" />Langsung, dengan Dokter Terpercaya.
                    </p>
                    <Link href="/consultation" class="mt-6 inline-flex w-fit items-center justify-between gap-4 rounded-full border-2 border-[#1b1b18] px-6 py-2 text-[16px] font-medium text-[#1b1b18] transition-colors hover:bg-[#1b1b18] hover:text-white sm:mt-8 sm:gap-6 sm:px-10 sm:py-3 sm:text-[20px]">
                        ChatBot
                        <Icon icon="mdi:arrow-right" class="h-4 w-4 sm:h-5 sm:w-5" />
                    </Link>
                </div>

                <div class="scroll-animate scroll-animate-delay-2 relative lg:-translate-x-16">
                    <div class="absolute -top-2 right-0 z-10 hidden rounded-full bg-white px-4 py-2 text-[14px] font-normal shadow-lg sm:block sm:px-6 sm:py-3 sm:text-[16px] lg:-right-32 lg:-top-4">
                        Ayo mulai sekarang!
                    </div>
                    <img src="/images/hero.png" alt="App Preview" class="w-[300px] sm:w-[400px] lg:w-[550px]" style="filter: drop-shadow(0 25px 50px rgba(141, 208, 252, 0.5))" />
                    <div class="absolute bottom-16 -left-4 z-10 hidden rounded-xl bg-white px-4 py-2 shadow-lg sm:block sm:bottom-32 sm:px-5 sm:py-3 lg:bottom-48 lg:-left-8">
                        <p class="text-[12px] font-semibold text-[#1b1b18] sm:text-[14px]">Konsultasi Sekarang! 🌟 9.10</p>
                    </div>
                </div>
            </div>

            <!-- Gradient Section with Features - Hidden on Mobile -->
            <div class="relative -mt-8 hidden h-[320px] lg:-mt-36 lg:block">
                <svg class="absolute left-32 right-0 h-full" style="width: calc(100% + 50px)" viewBox="0 0 1400 280" preserveAspectRatio="none">
                    <defs>
                        <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="0%">
                            <stop offset="0%" stop-color="#8DD0FC" />
                            <stop offset="100%" stop-color="#DDB4F6" />
                        </linearGradient>
                    </defs>
                    <path d="M40,80 L1360,15 Q1400,10 1400,50 L1400,240 Q1400,280 1360,280 L40,280 Q0,280 0,240 L0,120 Q0,80 40,80 Z" fill="url(#gradient)" />
                </svg>

                <div class="relative z-10 px-12 pt-28 pb-12 pl-48">
                    <div class="mx-auto flex max-w-7xl items-start justify-between gap-24">
                        <div class="flex gap-24">
                            <div class="max-w-[320px]">
                                <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-[#5BA4D9]/20">
                                    <Icon icon="eos-icons:ai" class="h-8 w-8 text-[#2D7BB8]" />
                                </div>
                                <h3 class="text-[20px] font-bold text-[#1b1b18]">AI Consultation</h3>
                                <p class="mt-2 text-[14px] leading-relaxed text-[#1b1b18]/80">
                                    Konsultasikan keluhan penyakit Anda sekarang dan dapatkan solusi cepat dari DocDot, aman terpercaya.
                                </p>
                            </div>
                            <div class="max-w-[320px]">
                                <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-white/40">
                                    <Icon icon="fluent-mdl2:insights" class="h-8 w-8 text-[#2D7BB8]" />
                                </div>
                                <h3 class="text-[20px] font-bold text-[#1b1b18]">DocDot Insight</h3>
                                <p class="mt-2 text-[14px] leading-relaxed text-[#1b1b18]/80">
                                    Temukan berbagai artikel kesehatan terpercaya dan terbaru hanya di DocDot, untuk hidup lebih sehat.
                                </p>
                            </div>
                        </div>

                        <div class="flex flex-col items-center">
                            <div class="rounded-xl bg-white px-8 py-4 shadow-lg">
                                <p class="text-[14px] font-normal text-[#1b1b18]">Periksa Kesehatan Anda</p>
                                <p class="flex items-center gap-1 text-[14px] font-normal text-[#1b1b18]">
                                    Sekarang!
                                    <Icon icon="mdi:magnify" class="h-4 w-4" />
                                </p>
                            </div>
                            <div class="mt-6 flex items-center">
                                <div class="flex -space-x-8">
                                    <img src="https://i.pravatar.cc/48?img=1" class="h-12 w-12 rounded-full border-2 border-white object-cover" />
                                    <img src="https://i.pravatar.cc/48?img=2" class="h-12 w-12 rounded-full border-2 border-white object-cover" />
                                    <img src="https://i.pravatar.cc/48?img=3" class="h-12 w-12 rounded-full border-2 border-white object-cover" />
                                    <img src="https://i.pravatar.cc/48?img=4" class="h-12 w-12 rounded-full border-2 border-white object-cover" />
                                </div>
                                <div class="-ml-4 flex h-12 w-12 items-center justify-center rounded-full bg-white text-[14px] font-semibold text-[#1b1b18]">
                                    100+
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile Features Section -->
            <div class="mt-8 grid grid-cols-2 gap-4 px-2 sm:hidden">
                <div class="rounded-2xl bg-gradient-to-br from-[#8DD0FC]/20 to-[#DDB4F6]/20 p-4">
                    <div class="mb-2 flex h-10 w-10 items-center justify-center rounded-xl bg-[#5BA4D9]/20">
                        <Icon icon="eos-icons:ai" class="h-5 w-5 text-[#2D7BB8]" />
                    </div>
                    <h3 class="text-[14px] font-bold text-[#1b1b18]">AI Consultation</h3>
                    <p class="mt-1 text-[11px] leading-relaxed text-[#1b1b18]/70">Konsultasi cepat dengan AI</p>
                </div>
                <div class="rounded-2xl bg-gradient-to-br from-[#DDB4F6]/20 to-[#8DD0FC]/20 p-4">
                    <div class="mb-2 flex h-10 w-10 items-center justify-center rounded-xl bg-white/60">
                        <Icon icon="fluent-mdl2:insights" class="h-5 w-5 text-[#2D7BB8]" />
                    </div>
                    <h3 class="text-[14px] font-bold text-[#1b1b18]">DocDot Insight</h3>
                    <p class="mt-1 text-[11px] leading-relaxed text-[#1b1b18]/70">Artikel kesehatan terpercaya</p>
                </div>
            </div>
        </section>

        <!-- Features Bar Section -->
        <section class="px-4 py-8 sm:px-6 sm:py-12 lg:px-12 lg:py-16">
            <!-- Mobile: 3 separate cards -->
            <div class="grid grid-cols-1 gap-3 sm:hidden">
                <div class="flex items-center gap-3 rounded-2xl bg-gradient-to-r from-[#8DD0FC] to-[#DDB4F6] px-4 py-4">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg border-2 border-[#7D80DF] bg-transparent">
                        <Icon icon="mdi:clock-fast" class="h-5 w-5 text-[#43B3FC]" />
                    </div>
                    <span class="text-[14px] font-medium text-[#1b1b18]">Layanan yang cepat</span>
                </div>
                <div class="flex items-center gap-3 rounded-2xl bg-gradient-to-r from-[#8DD0FC] to-[#DDB4F6] px-4 py-4">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg border-2 border-[#7D80DF] bg-transparent">
                        <Icon icon="mingcute:hours-fill" class="h-5 w-5 text-[#43B3FC]" />
                    </div>
                    <span class="text-[14px] font-medium text-[#1b1b18]">24 Jam</span>
                </div>
                <div class="flex items-center gap-3 rounded-2xl bg-gradient-to-r from-[#8DD0FC] to-[#DDB4F6] px-4 py-4">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg border-2 border-[#7D80DF] bg-transparent">
                        <Icon icon="wpf:doctors-bag" class="h-5 w-5 text-[#43B3FC]" />
                    </div>
                    <span class="text-[14px] font-medium text-[#1b1b18]">Konsultasi Gratis</span>
                </div>
            </div>

            <!-- Desktop: Single bar -->
            <div class="scroll-animate hidden rounded-[30px] bg-gradient-to-r from-[#8DD0FC] to-[#DDB4F6] px-8 py-6 sm:block lg:px-16">
                <div class="flex items-center justify-between gap-6">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl border-2 border-[#7D80DF] bg-transparent">
                            <Icon icon="mdi:clock-fast" class="h-8 w-8 text-[#43B3FC]" />
                        </div>
                        <span class="text-[16px] font-medium text-[#1b1b18] lg:text-[18px]">Layanan yang cepat</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl border-2 border-[#7D80DF] bg-transparent">
                            <Icon icon="mingcute:hours-fill" class="h-8 w-8 text-[#43B3FC]" />
                        </div>
                        <span class="text-[16px] font-medium text-[#1b1b18] lg:text-[18px]">24 Jam</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl border-2 border-[#7D80DF] bg-transparent">
                            <Icon icon="wpf:doctors-bag" class="h-8 w-8 text-[#43B3FC]" />
                        </div>
                        <span class="text-[16px] font-medium text-[#1b1b18] lg:text-[18px]">Konsultasi Gratis</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Mengapa harus DocDot Section -->
        <section class="px-4 py-8 sm:px-6 sm:py-12 lg:px-12 lg:py-16">
            <div class="mx-auto max-w-7xl">
                <div class="flex flex-col items-center gap-8 lg:flex-row lg:items-start lg:justify-between">
                    <!-- Left side visual - Hidden on mobile, visible on tablet+ -->
                    <div class="scroll-animate relative hidden items-center sm:flex">
                        <div class="relative h-[280px] w-[340px] rounded-[20px] bg-[#43B3FC]/10 sm:h-[380px] sm:w-[480px] sm:rounded-[30px]">
                            <img src="/images/docbot.png" alt="DocBot" class="absolute top-1/2 left-1/2 z-50 max-w-none w-[200px] -translate-x-1/2 -translate-y-1/2 sm:w-[260px] lg:w-[320px]" />
                            <div class="absolute top-1/2 -left-10 flex h-14 w-14 -translate-y-1/2 rotate-[-15deg] items-center justify-center rounded-[12px] bg-[#7DD3FC] sm:-left-14 sm:h-20 sm:w-20 sm:rounded-[16px]">
                                <Icon icon="mdi:magnify" class="h-7 w-7 text-white sm:h-10 sm:w-10" />
                            </div>
                        </div>

                        <div class="relative -ml-4 max-w-[280px] rounded-[20px] bg-white p-6 sm:-ml-8 sm:max-w-md sm:rounded-[30px] sm:p-10" style="box-shadow: 0 10px 40px rgba(141, 208, 252, 0.4)">
                            <h2 class="mb-4 text-[18px] font-bold text-[#1b1b18] sm:mb-6 sm:text-[24px]">Mengapa harus DocDot?</h2>
                            <div class="space-y-3 sm:space-y-4">
                                <div class="border-l-2 border-[#43B3FC] pl-3 sm:pl-4">
                                    <p class="text-[12px] leading-relaxed text-[#1b1b18]/80 sm:text-[14px]">
                                        Hampir separuh hingga dua pertiga populasi Indonesia belum memahami informasi kesehatan dengan baik.
                                    </p>
                                </div>
                                <div class="border-l-2 border-[#43B3FC] pl-3 sm:pl-4">
                                    <p class="text-[12px] leading-relaxed text-[#1b1b18]/80 sm:text-[14px]">
                                        Di banyak wilayah global, lebih dari sepertiga hingga setengah populasi juga mengalami kondisi serupa.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile version of why DocDot -->
                    <div class="scroll-animate w-full rounded-2xl bg-white p-5 shadow-lg sm:hidden">
                        <h2 class="mb-4 text-[20px] font-bold text-[#1b1b18]">Mengapa harus DocDot?</h2>
                        <div class="space-y-3">
                            <div class="border-l-2 border-[#43B3FC] pl-3">
                                <p class="text-[13px] leading-relaxed text-[#1b1b18]/80">
                                    Hampir separuh hingga dua pertiga populasi Indonesia belum memahami informasi kesehatan dengan baik.
                                </p>
                            </div>
                            <div class="border-l-2 border-[#43B3FC] pl-3">
                                <p class="text-[13px] leading-relaxed text-[#1b1b18]/80">
                                    Di banyak wilayah global, lebih dari sepertiga hingga setengah populasi juga mengalami kondisi serupa.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Stats section -->
                    <div class="scroll-animate scroll-animate-delay-2 flex flex-col gap-6 sm:gap-8">
                        <div class="flex flex-col items-center gap-3 text-center sm:flex-row sm:items-center sm:gap-6 sm:text-left">
                            <div class="flex-shrink-0">
                                <p class="text-[40px] font-bold leading-none text-[#43B3FC] sm:text-[56px]">33%</p>
                                <p class="text-[14px] font-medium text-[#43B3FC] sm:text-[20px]">kesehatan<br />problematic</p>
                            </div>
                            <p class="max-w-[450px] text-[14px] font-light leading-relaxed text-[#1b1b18] sm:text-[18px] lg:text-[20px]">
                                Di antara pasien dengan penyakit kronis, sekitar 33% menunjukkan tingkat literasi kesehatan problematic dan 16,2% berada pada kategori inadequate.
                            </p>
                        </div>
                        <div class="flex flex-col items-center gap-3 text-center sm:flex-row sm:items-center sm:gap-6 sm:text-left">
                            <div class="flex-shrink-0">
                                <p class="text-[40px] font-bold leading-none text-[#43B3FC] sm:text-[56px]">26%</p>
                                <p class="text-[14px] font-medium text-[#43B3FC] sm:text-[20px]">low health<br />literacy</p>
                            </div>
                            <p class="max-w-[450px] text-[14px] font-light leading-relaxed text-[#1b1b18] sm:text-[18px] lg:text-[20px]">
                                Analisis dari berbagai kategori menunjukkan bahwa rata-rata 26% populasi memiliki low health literacy.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Rasakan Fitur Kesehatan Section -->
        <section class="bg-[#DDB4F6]/20 px-4 py-8 sm:px-6 sm:py-12 lg:px-12 lg:py-16">
            <div class="mx-auto max-w-7xl text-center">
                <h2 class="scroll-animate text-[22px] font-bold text-[#1b1b18] sm:text-[28px] lg:text-[36px]">
                    Rasakan <span class="text-[#CC79FF]">Fitur Kesehatan</span> Lengkap dengan<br class="hidden sm:block" />
                    Teknologi Pintar dari <span class="bg-gradient-to-r from-[#CC79FF] to-[#4FB9FF] bg-clip-text text-transparent">DocDot</span> Setiap Hari 🚀
                </h2>

                <div class="scroll-animate scroll-animate-delay-1 mt-8 grid gap-4 sm:mt-12 sm:grid-cols-2 lg:grid-cols-3 lg:gap-6">
                    <div class="relative h-[160px] overflow-hidden rounded-2xl bg-[#E2B2FF] p-4 sm:h-[180px] sm:rounded-[30px] sm:p-6">
                        <div class="absolute -top-8 -bottom-8 left-[51%] w-[18%] rounded-[20px] bg-white/60" style="transform: skewX(-15deg);"></div>
                        <div class="absolute -top-8 -bottom-8 left-[69%] w-[18%] rounded-[20px] bg-white/30" style="transform: skewX(-15deg);"></div>
                        <div class="absolute -top-8 -bottom-8 left-[87%] w-[18%] rounded-[20px] bg-white/20" style="transform: skewX(-15deg);"></div>
                        <div class="relative z-10 mt-auto h-[100px] rounded-[16px] bg-white p-4 sm:h-[110px] sm:rounded-[20px] sm:p-5">
                            <div class="flex items-start gap-3">
                                <img src="/images/time.png" alt="Time" class="h-10 w-10 sm:h-12 sm:w-12" />
                                <p class="text-left text-[12px] leading-relaxed text-[#1b1b18]/80 sm:text-[14px]">
                                    Tim kami siap merespon keluhan kesehatan mu dengan cepat, 24/7.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="relative h-[160px] overflow-hidden rounded-2xl bg-[#E2B2FF] p-4 sm:h-[180px] sm:rounded-[30px] sm:p-6">
                        <div class="absolute -top-8 -bottom-8 left-[51%] w-[18%] rounded-[20px] bg-white/60" style="transform: skewX(-15deg);"></div>
                        <div class="absolute -top-8 -bottom-8 left-[69%] w-[18%] rounded-[20px] bg-white/30" style="transform: skewX(-15deg);"></div>
                        <div class="absolute -top-8 -bottom-8 left-[87%] w-[18%] rounded-[20px] bg-white/20" style="transform: skewX(-15deg);"></div>
                        <div class="relative z-10 mt-auto h-[100px] rounded-[16px] bg-white p-4 sm:h-[110px] sm:rounded-[20px] sm:p-5">
                            <div class="flex items-start gap-3">
                                <img src="/images/chat.png" alt="Chat" class="h-10 w-10 sm:h-12 sm:w-12" />
                                <p class="text-left text-[12px] leading-relaxed text-[#1b1b18]/80 sm:text-[14px]">
                                    Konsultasi tersedia kapan pun, siap melayani siang dan malam.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="relative h-[160px] overflow-hidden rounded-2xl bg-[#E2B2FF] p-4 sm:col-span-2 sm:h-[180px] sm:rounded-[30px] sm:p-6 lg:col-span-1">
                        <div class="absolute -top-8 -bottom-8 left-[51%] w-[18%] rounded-[20px] bg-white/60" style="transform: skewX(-15deg);"></div>
                        <div class="absolute -top-8 -bottom-8 left-[69%] w-[18%] rounded-[20px] bg-white/30" style="transform: skewX(-15deg);"></div>
                        <div class="absolute -top-8 -bottom-8 left-[87%] w-[18%] rounded-[20px] bg-white/20" style="transform: skewX(-15deg);"></div>
                        <div class="relative z-10 mt-auto h-[100px] rounded-[16px] bg-white p-4 sm:h-[110px] sm:rounded-[20px] sm:p-5">
                            <div class="flex items-start gap-3">
                                <img src="/images/talks.png" alt="Talks" class="h-10 w-10 sm:h-12 sm:w-12" />
                                <p class="text-left text-[12px] leading-relaxed text-[#1b1b18]/80 sm:text-[14px]">
                                    Nikmati layanan konsultasi tanpa biaya, langsung dari rumah.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Pengingat Medibot Section -->
        <section class="bg-white px-4 py-8 sm:px-6 sm:py-12 lg:px-12 lg:py-16">
            <div class="mx-auto max-w-7xl">
                <div class="flex flex-col items-center gap-8 lg:flex-row lg:items-start lg:justify-between lg:gap-12">
                    <!-- Visual section - hidden on mobile -->
                    <div class="scroll-animate relative hidden gap-8 sm:flex">
                        <div class="relative">
                            <div class="absolute -top-6 left-1/2 z-20 flex h-12 w-12 -translate-x-1/2 items-center justify-center rounded-full bg-white shadow-lg sm:-top-8 sm:h-16 sm:w-16">
                                <Icon icon="mdi:stethoscope" class="h-6 w-6 text-[#CC79FF] sm:h-8 sm:w-8" />
                            </div>

                            <div class="relative h-[200px] w-[200px] rounded-[20px] bg-[#E2B2FF] sm:h-[280px] sm:w-[280px] sm:rounded-[30px]">
                                <div class="absolute top-0 right-0 h-full w-1/2 rounded-r-[20px] bg-[#D9A0F0] sm:rounded-r-[30px]"></div>
                                <img src="/images/bot.png" alt="DocDot Bot" class="absolute bottom-3 left-1/2 z-10 h-[160px] w-auto -translate-x-1/2 sm:bottom-4 sm:h-[220px]" />
                            </div>

                            <div class="absolute top-12 -right-12 z-20 rounded-full bg-white px-5 py-2 shadow-lg sm:top-16 sm:-right-20 sm:px-8 sm:py-3">
                                <span class="text-[14px] font-semibold text-[#CC79FF] sm:text-[18px]">Notification</span>
                            </div>
                        </div>

                        <div class="relative mt-24 hidden sm:mt-40 lg:block">
                            <div class="absolute -top-6 left-1/2 z-20 flex h-10 w-10 -translate-x-1/2 items-center justify-center rounded-full bg-white shadow-lg sm:-top-8 sm:h-14 sm:w-14">
                                <Icon icon="hugeicons:tick-03" class="h-5 w-5 text-[#43B3FC] sm:h-7 sm:w-7" />
                            </div>

                            <div class="relative h-[180px] w-[200px] overflow-hidden rounded-[16px] bg-[#83CEFF] p-4 sm:h-[220px] sm:w-[260px] sm:rounded-[20px] sm:p-6">
                                <div class="absolute top-0 bottom-0 left-[35%] w-[25%] bg-[#99D7FF]"></div>
                                <div class="absolute top-0 bottom-0 left-[60%] right-0 bg-[#ACDEFF]"></div>
                                <div class="relative z-10 flex h-full flex-col justify-center space-y-3 sm:space-y-5">
                                    <div class="flex items-center gap-2 sm:gap-3">
                                        <Icon icon="hugeicons:tick-03" class="h-5 w-5 text-white sm:h-6 sm:w-6" />
                                        <span class="text-[13px] font-light text-[#1b1b18] sm:text-[16px]">Tips Kesehatan Harian</span>
                                    </div>
                                    <div class="flex items-center gap-2 sm:gap-3">
                                        <Icon icon="hugeicons:tick-03" class="h-5 w-5 text-white sm:h-6 sm:w-6" />
                                        <span class="text-[13px] font-light text-[#1b1b18] sm:text-[16px]">Pengingat Konsultasi</span>
                                    </div>
                                    <div class="flex items-center gap-2 sm:gap-3">
                                        <Icon icon="hugeicons:tick-03" class="h-5 w-5 text-white sm:h-6 sm:w-6" />
                                        <span class="text-[13px] font-light text-[#1b1b18] sm:text-[16px]">Ucapan Penyemangat</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Content section -->
                    <div class="scroll-animate scroll-animate-delay-2 max-w-xl">
                        <h2 class="text-[24px] font-bold text-[#1b1b18] sm:text-[28px] lg:text-[32px]">Pengingat DocDot 🔔</h2>
                        <div class="mt-3 border-l-2 border-[#039BFF] pl-3 sm:mt-4 sm:pl-4">
                            <p class="text-[14px] leading-relaxed text-[#1b1b18]/80 sm:text-[16px]">
                                Fitur notification di DocDot berfungsi untuk memberikan pengingat dan informasi penting secara otomatis. Tujuannya agar pengguna lebih teratur dalam menjaga kesehatannya.
                            </p>
                        </div>

                        <h3 class="mt-6 text-[20px] font-bold text-[#1b1b18] sm:mt-8 sm:text-[24px]">Siaga bersama DocDot</h3>
                        <div class="mt-3 border-l-2 border-[#BF55FF] pl-3 sm:mt-4 sm:pl-4">
                            <p class="text-[14px] leading-relaxed text-[#1b1b18]/80 sm:text-[16px]">
                                Dengan fitur notifikasi otomatis, DocDot selalu siap mengingatkanmu tentang jadwal kesehatan penting konsultasi, dan tips harian agar kamu tetap sehat dan terjaga setiap saat.
                            </p>
                        </div>

                        <!-- Mobile features list -->
                        <div class="mt-6 grid grid-cols-1 gap-3 sm:hidden">
                            <div class="flex items-center gap-3 rounded-xl bg-[#83CEFF]/20 p-3">
                                <Icon icon="hugeicons:tick-03" class="h-5 w-5 text-[#43B3FC]" />
                                <span class="text-[14px] text-[#1b1b18]">Tips Kesehatan Harian</span>
                            </div>
                            <div class="flex items-center gap-3 rounded-xl bg-[#83CEFF]/20 p-3">
                                <Icon icon="hugeicons:tick-03" class="h-5 w-5 text-[#43B3FC]" />
                                <span class="text-[14px] text-[#1b1b18]">Pengingat Konsultasi</span>
                            </div>
                            <div class="flex items-center gap-3 rounded-xl bg-[#83CEFF]/20 p-3">
                                <Icon icon="hugeicons:tick-03" class="h-5 w-5 text-[#43B3FC]" />
                                <span class="text-[14px] text-[#1b1b18]">Ucapan Penyemangat</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Informasi Tepat Section -->
        <section class="bg-white px-4 py-8 sm:px-6 lg:mt-16 lg:px-12 lg:pt-40 lg:pb-16">
            <div class="mx-auto max-w-6xl">
                <!-- Mobile Version -->
                <div class="scroll-animate block rounded-2xl bg-gradient-to-r from-[#8DD0FC] to-[#DDB4F6] p-6 sm:hidden">
                    <h2 class="flex items-center gap-2 text-[24px] font-bold text-[#1b1b18]">
                        Informasi Tepat
                        <Icon icon="carbon:circle-dash" class="h-6 w-6 text-[#1b1b18]" />
                    </h2>
                    <h3 class="text-[24px] font-bold text-[#1b1b18]">
                        Untuk <span class="text-[#7C3AED]">Hidup</span> Sehat
                    </h3>
                    <p class="mt-3 text-[14px] font-light text-[#1b1b18]/80">
                        Konten tepercaya untuk mendukung kesehatan keluarga Anda.
                    </p>
                    <Link href="/article" class="mt-4 inline-flex w-fit items-center justify-between gap-4 rounded-full border-2 border-[#1b1b18] bg-white px-5 py-2 text-[14px] font-medium text-[#1b1b18] transition-colors hover:bg-[#1b1b18] hover:text-white">
                        Article
                        <Icon icon="mdi:arrow-top-right" class="h-4 w-4" />
                    </Link>
                </div>

                <!-- Desktop Version -->
                <div class="scroll-animate relative hidden h-[400px] items-center rounded-[30px] bg-gradient-to-r from-[#8DD0FC] to-[#DDB4F6] px-10 sm:flex">
                    <!-- Stripes di pojok kanan bawah (inside card with clip) -->
                    <div class="absolute inset-0 overflow-hidden rounded-[30px]">
                        <div class="absolute bottom-16 -right-17 h-[50px] w-[500px] rounded-l-full bg-white/20" style="transform: rotate(-35deg); transform-origin: bottom right;"></div>
                    </div>

                    <div class="absolute right-10 z-10 max-w-sm py-4 lg:right-48">
                        <h2 class="flex items-center gap-3 text-[24px] font-bold text-[#1b1b18] lg:text-[32px]">
                            Informasi Tepat
                            <Icon icon="carbon:circle-dash" class="h-6 w-6 text-[#1b1b18] lg:h-8 lg:w-8" />
                        </h2>
                        <h3 class="text-[24px] font-bold text-[#1b1b18] lg:text-[32px]">
                            Untuk <span class="text-[#7C3AED]">Hidup</span> Sehat
                        </h3>
                        <p class="mt-3 text-[14px] font-light text-[#1b1b18]/80 lg:text-[18px]">
                            Konten tepercaya untuk<br />mendukung kesehatan keluarga<br />Anda.
                        </p>
                        <Link href="/article" class="mt-5 inline-flex w-fit items-center justify-between gap-6 rounded-full border-2 border-[#1b1b18] bg-white px-6 py-2 text-[16px] font-medium text-[#1b1b18] transition-colors hover:bg-[#1b1b18] hover:text-white">
                            Article
                            <Icon icon="mdi:arrow-top-right" class="h-5 w-5" />
                        </Link>
                    </div>
                    <div class="absolute left-4 -top-24 z-20 lg:left-10 lg:-top-32">
                        <img src="/images/informasi.png" alt="Informasi" class="w-[350px] max-w-none lg:w-[600px]" />
                    </div>
                </div>
            </div>
        </section>

        <!-- Ulasan Section -->
        <section class="bg-white px-4 py-8 sm:px-6 sm:py-16 lg:px-12">
            <div class="mx-auto max-w-6xl">
                <h2 class="scroll-animate mb-6 text-left text-[24px] font-bold text-[#1b1b18] sm:mb-12 sm:text-[32px]">
                    Ulasan Nyata dari Pengguna DocDot
                </h2>

                <div class="scroll-animate scroll-animate-delay-1 flex flex-col gap-4 sm:flex-row sm:justify-between">
                    <!-- Card 1 -->
                    <div class="w-full rounded-[20px] bg-[#FAF1FF] p-5 sm:w-[350px] sm:p-6">
                        <div class="mb-4 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <img src="https://i.pravatar.cc/48?img=11" class="h-10 w-10 rounded-full object-cover sm:h-12 sm:w-12" />
                                <div>
                                    <p class="text-[14px] font-semibold text-[#1b1b18] sm:text-[16px]">Rina Sari</p>
                                    <p class="text-[11px] text-[#1b1b18]/60 sm:text-[12px]">@rinasari</p>
                                </div>
                            </div>
                            <div class="flex gap-1">
                                <Icon icon="mdi:star" class="h-3 w-3 text-[#FFD700] sm:h-4 sm:w-4" />
                                <Icon icon="mdi:star" class="h-3 w-3 text-[#FFD700] sm:h-4 sm:w-4" />
                                <Icon icon="mdi:star" class="h-3 w-3 text-[#FFD700] sm:h-4 sm:w-4" />
                                <Icon icon="mdi:star" class="h-3 w-3 text-[#FFD700] sm:h-4 sm:w-4" />
                                <Icon icon="mdi:star" class="h-3 w-3 text-[#FFD700] sm:h-4 sm:w-4" />
                            </div>
                        </div>
                        <p class="text-[13px] leading-relaxed text-[#1b1b18]/80 sm:text-[14px]">
                            DocDot sangat membantu saya memahami gejala yang saya alami. Responnya cepat dan informasinya mudah dipahami. Recommended!
                        </p>
                    </div>

                    <!-- Card 2 -->
                    <div class="w-full rounded-[20px] bg-[#FAF1FF] p-5 sm:w-[350px] sm:p-6">
                        <div class="mb-4 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <img src="https://i.pravatar.cc/48?img=12" class="h-10 w-10 rounded-full object-cover sm:h-12 sm:w-12" />
                                <div>
                                    <p class="text-[14px] font-semibold text-[#1b1b18] sm:text-[16px]">Budi Santoso</p>
                                    <p class="text-[11px] text-[#1b1b18]/60 sm:text-[12px]">@budisantoso</p>
                                </div>
                            </div>
                            <div class="flex gap-1">
                                <Icon icon="mdi:star" class="h-3 w-3 text-[#FFD700] sm:h-4 sm:w-4" />
                                <Icon icon="mdi:star" class="h-3 w-3 text-[#FFD700] sm:h-4 sm:w-4" />
                                <Icon icon="mdi:star" class="h-3 w-3 text-[#FFD700] sm:h-4 sm:w-4" />
                                <Icon icon="mdi:star" class="h-3 w-3 text-[#FFD700] sm:h-4 sm:w-4" />
                                <Icon icon="mdi:star" class="h-3 w-3 text-[#FFD700] sm:h-4 sm:w-4" />
                            </div>
                        </div>
                        <p class="text-[13px] leading-relaxed text-[#1b1b18]/80 sm:text-[14px]">
                            Fitur konsultasi 24 jam sangat berguna. Saya bisa bertanya kapan saja tanpa harus menunggu jam kerja. Terima kasih DocDot!
                        </p>
                    </div>

                    <!-- Card 3 -->
                    <div class="w-full rounded-[20px] bg-[#FAF1FF] p-5 sm:w-[350px] sm:p-6">
                        <div class="mb-4 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <img src="https://i.pravatar.cc/48?img=13" class="h-10 w-10 rounded-full object-cover sm:h-12 sm:w-12" />
                                <div>
                                    <p class="text-[14px] font-semibold text-[#1b1b18] sm:text-[16px]">Dewi Lestari</p>
                                    <p class="text-[11px] text-[#1b1b18]/60 sm:text-[12px]">@dewilestari</p>
                                </div>
                            </div>
                            <div class="flex gap-1">
                                <Icon icon="mdi:star" class="h-3 w-3 text-[#FFD700] sm:h-4 sm:w-4" />
                                <Icon icon="mdi:star" class="h-3 w-3 text-[#FFD700] sm:h-4 sm:w-4" />
                                <Icon icon="mdi:star" class="h-3 w-3 text-[#FFD700] sm:h-4 sm:w-4" />
                                <Icon icon="mdi:star" class="h-3 w-3 text-[#FFD700] sm:h-4 sm:w-4" />
                                <Icon icon="mdi:star" class="h-3 w-3 text-[#FFD700] sm:h-4 sm:w-4" />
                            </div>
                        </div>
                        <p class="text-[13px] leading-relaxed text-[#1b1b18]/80 sm:text-[14px]">
                            Artikel kesehatannya lengkap dan terpercaya. Sekarang saya lebih paham cara menjaga kesehatan keluarga. Sangat bermanfaat!
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <Footer />
    </div>
</template>
