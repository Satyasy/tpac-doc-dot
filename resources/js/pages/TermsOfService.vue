<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Icon } from '@iconify/vue';
import Navbar from '@/components/Navbar.vue';
import Footer from '@/components/Footer.vue';
import { useScrollAnimation } from '@/composables/useScrollAnimation';

useScrollAnimation();
</script>

<template>
    <Head title="Syarat & Ketentuan - DocDot" />

    <div class="min-h-screen pt-16 sm:pt-20 lg:pt-22 font-[Poppins]" style="background: linear-gradient(to left, rgba(141, 208, 252, 0.6) 0%, rgba(221, 180, 246, 0.6) 100%)">
        <Navbar />

        <!-- Hero Section -->
        <section class="px-4 py-8 sm:px-6 sm:py-12 lg:px-12">
            <div class="mx-auto max-w-4xl">
                <nav class="mb-4 flex items-center gap-2 text-[12px] sm:mb-6 sm:text-[14px]">
                    <Link href="/" class="text-[#1b1b18]/60 hover:text-[#1b1b18]">Beranda</Link>
                    <Icon icon="mdi:chevron-right" class="h-3 w-3 text-[#1b1b18]/40 sm:h-4 sm:w-4" />
                    <span class="text-[#1b1b18]">Syarat & Ketentuan</span>
                </nav>
                <h1 class="scroll-animate text-[24px] font-bold text-[#1b1b18] sm:text-[32px] lg:text-[42px]">
                    Syarat & <span class="bg-gradient-to-r from-[#BF55FF] to-[#43B3FC] bg-clip-text text-transparent">Ketentuan</span>
                </h1>
                <p class="scroll-animate mt-1 text-[12px] text-[#1b1b18]/60 sm:mt-2 sm:text-[14px]">
                    Terakhir diperbarui: 26 Desember 2025
                </p>
            </div>
        </section>

        <!-- Content -->
        <section class="px-4 pb-12 sm:px-6 sm:pb-16 lg:px-12">
            <div class="mx-auto max-w-4xl">
                <div class="scroll-animate rounded-xl bg-white p-4 sm:rounded-2xl sm:p-6 lg:p-10">
                    <div class="prose prose-lg max-w-none">
                        <!-- Important Notice -->
                        <div class="mb-8 rounded-xl border-l-4 border-[#F4AFE9] bg-[#F4AFE9]/10 p-4">
                            <div class="flex items-start gap-3">
                                <Icon icon="mdi:alert-circle" class="mt-0.5 h-5 w-5 flex-shrink-0 text-[#BF55FF]" />
                                <div>
                                    <p class="text-[14px] font-medium text-[#1b1b18]">Penting untuk Dibaca</p>
                                    <p class="mt-1 text-[13px] text-[#1b1b18]/70">
                                        Dengan menggunakan DocDot, Anda menyetujui Syarat & Ketentuan ini. Jika tidak setuju, mohon untuk tidak menggunakan layanan kami.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <section class="mb-8">
                            <h2 class="mb-4 text-[20px] font-semibold text-[#1b1b18]">1. Ketentuan Umum</h2>
                            <p class="text-[14px] leading-relaxed text-[#1b1b18]/70">
                                Syarat dan Ketentuan ini mengatur penggunaan Anda atas platform DocDot, termasuk website dan semua layanan yang terkait. Dengan mengakses atau menggunakan platform kami, Anda setuju untuk terikat dengan syarat dan ketentuan ini.
                            </p>
                        </section>

                        <section class="mb-8">
                            <h2 class="mb-4 text-[20px] font-semibold text-[#1b1b18]">2. Definisi Layanan</h2>
                            <p class="mb-3 text-[14px] leading-relaxed text-[#1b1b18]/70">
                                DocDot menyediakan layanan berupa:
                            </p>
                            <ul class="ml-4 space-y-2 text-[14px] text-[#1b1b18]/70">
                                <li class="flex items-start gap-2">
                                    <Icon icon="mdi:robot" class="mt-0.5 h-4 w-4 flex-shrink-0 text-[#43B3FC]" />
                                    <span><strong>Konsultasi AI:</strong> Asisten kesehatan berbasis kecerdasan buatan untuk informasi kesehatan umum</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <Icon icon="mdi:newspaper" class="mt-0.5 h-4 w-4 flex-shrink-0 text-[#BF55FF]" />
                                    <span><strong>Artikel Kesehatan:</strong> Informasi dan edukasi kesehatan yang terverifikasi</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <Icon icon="mdi:pill" class="mt-0.5 h-4 w-4 flex-shrink-0 text-[#F4AFE9]" />
                                    <span><strong>Katalog Obat:</strong> Database informasi obat-obatan umum</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <Icon icon="mdi:chart-line" class="mt-0.5 h-4 w-4 flex-shrink-0 text-[#8DD0FC]" />
                                    <span><strong>Health Tracking:</strong> Fitur pemantauan kesehatan pribadi</span>
                                </li>
                            </ul>
                        </section>

                        <!-- Medical Disclaimer - Highlighted -->
                        <section class="mb-8 rounded-xl bg-red-50 p-6">
                            <div class="flex items-start gap-3">
                                <Icon icon="mdi:medical-bag" class="mt-0.5 h-6 w-6 flex-shrink-0 text-red-500" />
                                <div>
                                    <h2 class="text-[18px] font-semibold text-red-700">3. Disclaimer Medis (PENTING)</h2>
                                    <div class="mt-3 space-y-3 text-[14px] text-red-700/80">
                                        <p>
                                            <strong>DocDot BUKAN pengganti layanan medis profesional.</strong> Informasi yang disediakan oleh platform ini, termasuk respons dari AI, hanya bersifat informatif dan edukatif.
                                        </p>
                                        <p>
                                            Anda <strong>WAJIB</strong> berkonsultasi dengan dokter atau tenaga medis profesional untuk:
                                        </p>
                                        <ul class="ml-4 space-y-1">
                                            <li>• Diagnosis penyakit atau kondisi medis</li>
                                            <li>• Pengobatan dan peresepan obat</li>
                                            <li>• Keputusan medis yang mempengaruhi kesehatan Anda</li>
                                            <li>• Kondisi darurat atau gejala serius</li>
                                        </ul>
                                        <p>
                                            Kami <strong>TIDAK BERTANGGUNG JAWAB</strong> atas keputusan medis yang diambil berdasarkan informasi dari platform ini.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section class="mb-8">
                            <h2 class="mb-4 text-[20px] font-semibold text-[#1b1b18]">4. Akun Pengguna</h2>
                            <p class="mb-3 text-[14px] leading-relaxed text-[#1b1b18]/70">
                                Untuk menggunakan fitur tertentu, Anda perlu membuat akun. Anda bertanggung jawab untuk:
                            </p>
                            <ul class="ml-4 space-y-2 text-[14px] text-[#1b1b18]/70">
                                <li class="flex items-start gap-2">
                                    <Icon icon="mdi:check" class="mt-0.5 h-4 w-4 flex-shrink-0 text-green-500" />
                                    <span>Memberikan informasi yang akurat dan lengkap saat pendaftaran</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <Icon icon="mdi:check" class="mt-0.5 h-4 w-4 flex-shrink-0 text-green-500" />
                                    <span>Menjaga kerahasiaan kata sandi dan keamanan akun</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <Icon icon="mdi:check" class="mt-0.5 h-4 w-4 flex-shrink-0 text-green-500" />
                                    <span>Memberitahu kami segera jika terjadi penggunaan tidak sah</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <Icon icon="mdi:check" class="mt-0.5 h-4 w-4 flex-shrink-0 text-green-500" />
                                    <span>Bertanggung jawab atas semua aktivitas di akun Anda</span>
                                </li>
                            </ul>
                        </section>

                        <section class="mb-8">
                            <h2 class="mb-4 text-[20px] font-semibold text-[#1b1b18]">5. Penggunaan yang Dilarang</h2>
                            <p class="mb-3 text-[14px] leading-relaxed text-[#1b1b18]/70">
                                Anda setuju untuk TIDAK:
                            </p>
                            <ul class="ml-4 space-y-2 text-[14px] text-[#1b1b18]/70">
                                <li class="flex items-start gap-2">
                                    <Icon icon="mdi:close-circle" class="mt-0.5 h-4 w-4 flex-shrink-0 text-red-500" />
                                    <span>Menggunakan platform untuk tujuan ilegal atau tidak sah</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <Icon icon="mdi:close-circle" class="mt-0.5 h-4 w-4 flex-shrink-0 text-red-500" />
                                    <span>Menyebarkan informasi palsu atau menyesatkan</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <Icon icon="mdi:close-circle" class="mt-0.5 h-4 w-4 flex-shrink-0 text-red-500" />
                                    <span>Melakukan serangan atau upaya peretasan sistem</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <Icon icon="mdi:close-circle" class="mt-0.5 h-4 w-4 flex-shrink-0 text-red-500" />
                                    <span>Mengganggu operasi normal platform</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <Icon icon="mdi:close-circle" class="mt-0.5 h-4 w-4 flex-shrink-0 text-red-500" />
                                    <span>Mengumpulkan data pengguna lain tanpa izin</span>
                                </li>
                            </ul>
                        </section>

                        <section class="mb-8">
                            <h2 class="mb-4 text-[20px] font-semibold text-[#1b1b18]">6. Hak Kekayaan Intelektual</h2>
                            <p class="text-[14px] leading-relaxed text-[#1b1b18]/70">
                                Semua konten di platform DocDot, termasuk namun tidak terbatas pada teks, grafis, logo, ikon, gambar, klip audio, unduhan digital, dan kompilasi data adalah milik DocDot atau pemberi lisensinya dan dilindungi oleh hukum hak cipta Indonesia dan internasional.
                            </p>
                        </section>

                        <section class="mb-8">
                            <h2 class="mb-4 text-[20px] font-semibold text-[#1b1b18]">7. Batasan Tanggung Jawab</h2>
                            <p class="text-[14px] leading-relaxed text-[#1b1b18]/70">
                                Sejauh diizinkan oleh hukum yang berlaku, DocDot tidak bertanggung jawab atas kerugian langsung, tidak langsung, insidental, khusus, atau konsekuensial yang timbul dari penggunaan atau ketidakmampuan menggunakan layanan kami, termasuk namun tidak terbatas pada kerugian yang timbul dari:
                            </p>
                            <ul class="mt-3 ml-4 space-y-2 text-[14px] text-[#1b1b18]/70">
                                <li class="flex items-start gap-2">
                                    <Icon icon="mdi:circle-small" class="mt-1 h-4 w-4 flex-shrink-0 text-[#43B3FC]" />
                                    <span>Keputusan medis berdasarkan informasi dari platform</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <Icon icon="mdi:circle-small" class="mt-1 h-4 w-4 flex-shrink-0 text-[#43B3FC]" />
                                    <span>Ketidakakuratan atau ketidaklengkapan informasi</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <Icon icon="mdi:circle-small" class="mt-1 h-4 w-4 flex-shrink-0 text-[#43B3FC]" />
                                    <span>Gangguan atau tidak tersedianya layanan</span>
                                </li>
                            </ul>
                        </section>

                        <section class="mb-8">
                            <h2 class="mb-4 text-[20px] font-semibold text-[#1b1b18]">8. Perubahan Layanan</h2>
                            <p class="text-[14px] leading-relaxed text-[#1b1b18]/70">
                                Kami berhak untuk mengubah, menangguhkan, atau menghentikan layanan kami (atau bagian dari layanan) kapan saja dengan atau tanpa pemberitahuan. Kami tidak bertanggung jawab kepada Anda atau pihak ketiga atas perubahan, penangguhan, atau penghentian tersebut.
                            </p>
                        </section>

                        <section class="mb-8">
                            <h2 class="mb-4 text-[20px] font-semibold text-[#1b1b18]">9. Perubahan Syarat & Ketentuan</h2>
                            <p class="text-[14px] leading-relaxed text-[#1b1b18]/70">
                                Kami dapat memperbarui Syarat & Ketentuan ini dari waktu ke waktu. Perubahan akan berlaku segera setelah diposting di platform. Penggunaan berkelanjutan Anda atas layanan kami setelah perubahan merupakan penerimaan Anda terhadap syarat yang diubah.
                            </p>
                        </section>

                        <section class="mb-8">
                            <h2 class="mb-4 text-[20px] font-semibold text-[#1b1b18]">10. Hukum yang Berlaku</h2>
                            <p class="text-[14px] leading-relaxed text-[#1b1b18]/70">
                                Syarat & Ketentuan ini diatur oleh dan ditafsirkan sesuai dengan hukum Republik Indonesia. Setiap sengketa yang timbul akan diselesaikan melalui pengadilan yang berwenang di Indonesia.
                            </p>
                        </section>

                        <section class="rounded-xl bg-gradient-to-r from-[#F4AFE9]/20 to-[#8DD0FC]/20 p-6">
                            <h2 class="mb-3 text-[18px] font-semibold text-[#1b1b18]">11. Hubungi Kami</h2>
                            <p class="text-[14px] text-[#1b1b18]/70">
                                Jika Anda memiliki pertanyaan tentang Syarat & Ketentuan ini, silakan hubungi kami:
                            </p>
                            <div class="mt-4 flex flex-wrap gap-4">
                                <a href="mailto:legal@docdot.id" class="inline-flex items-center gap-2 text-[14px] text-[#43B3FC] hover:underline">
                                    <Icon icon="mdi:email" class="h-4 w-4" />
                                    legal@docdot.id
                                </a>
                                <Link href="/contact" class="inline-flex items-center gap-2 text-[14px] text-[#43B3FC] hover:underline">
                                    <Icon icon="mdi:message" class="h-4 w-4" />
                                    Form Kontak
                                </Link>
                            </div>
                        </section>

                        <!-- Agreement -->
                        <div class="mt-8 rounded-xl bg-[#1b1b18] p-6 text-center">
                            <p class="text-[14px] text-white/80">
                                Dengan menggunakan DocDot, Anda menyatakan bahwa Anda telah membaca, memahami, dan menyetujui Syarat & Ketentuan ini.
                            </p>
                            <div class="mt-4 flex flex-wrap justify-center gap-4">
                                <Link 
                                    href="/register"
                                    class="rounded-full bg-gradient-to-r from-[#F4AFE9] to-[#8DD0FC] px-6 py-2 text-[14px] font-medium text-white transition-all hover:shadow-lg"
                                >
                                    Daftar Sekarang
                                </Link>
                                <Link 
                                    href="/privacy-policy"
                                    class="rounded-full border border-white/30 px-6 py-2 text-[14px] font-medium text-white transition-all hover:bg-white/10"
                                >
                                    Baca Kebijakan Privasi
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <Footer />
    </div>
</template>
