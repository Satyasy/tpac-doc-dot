<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Icon } from '@iconify/vue';
import Navbar from '@/components/Navbar.vue';
import Footer from '@/components/Footer.vue';
import { useScrollAnimation } from '@/composables/useScrollAnimation';

useScrollAnimation();
</script>

<template>
    <Head title="Kebijakan Privasi - DocDot" />

    <div class="min-h-screen pt-16 sm:pt-20 lg:pt-22 font-[Poppins]" style="background: linear-gradient(to left, rgba(141, 208, 252, 0.6) 0%, rgba(221, 180, 246, 0.6) 100%)">
        <Navbar />

        <!-- Hero Section -->
        <section class="px-4 py-8 sm:px-6 sm:py-12 lg:px-12">
            <div class="mx-auto max-w-4xl">
                <nav class="mb-4 flex items-center gap-2 text-[12px] sm:mb-6 sm:text-[14px]">
                    <Link href="/" class="text-[#1b1b18]/60 hover:text-[#1b1b18]">Beranda</Link>
                    <Icon icon="mdi:chevron-right" class="h-3 w-3 text-[#1b1b18]/40 sm:h-4 sm:w-4" />
                    <span class="text-[#1b1b18]">Kebijakan Privasi</span>
                </nav>
                <h1 class="scroll-animate text-[24px] font-bold text-[#1b1b18] sm:text-[32px] lg:text-[42px]">
                    Kebijakan <span class="bg-gradient-to-r from-[#BF55FF] to-[#43B3FC] bg-clip-text text-transparent">Privasi</span>
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
                        <section class="mb-6 sm:mb-8">
                            <h2 class="mb-3 text-[16px] font-semibold text-[#1b1b18] sm:mb-4 sm:text-[20px]">1. Pendahuluan</h2>
                            <p class="text-[13px] leading-relaxed text-[#1b1b18]/70 sm:text-[14px]">
                                DocDot ("kami", "kita", atau "milik kami") berkomitmen untuk melindungi privasi Anda. Kebijakan Privasi ini menjelaskan bagaimana kami mengumpulkan, menggunakan, mengungkapkan, dan melindungi informasi Anda ketika Anda menggunakan platform DocDot.
                            </p>
                        </section>

                        <section class="mb-6 sm:mb-8">
                            <h2 class="mb-3 text-[16px] font-semibold text-[#1b1b18] sm:mb-4 sm:text-[20px]">2. Informasi yang Kami Kumpulkan</h2>
                            <p class="mb-2 text-[13px] leading-relaxed text-[#1b1b18]/70 sm:mb-3 sm:text-[14px]">
                                Kami mengumpulkan beberapa jenis informasi:
                            </p>
                            <ul class="ml-4 space-y-1.5 text-[13px] text-[#1b1b18]/70 sm:space-y-2 sm:text-[14px]">
                                <li class="flex items-start gap-2">
                                    <Icon icon="mdi:circle-small" class="mt-1 h-4 w-4 flex-shrink-0 text-[#43B3FC]" />
                                    <span><strong>Informasi Pribadi:</strong> Nama, alamat email, nomor telepon, tanggal lahir, jenis kelamin.</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <Icon icon="mdi:circle-small" class="mt-1 h-4 w-4 flex-shrink-0 text-[#43B3FC]" />
                                    <span><strong>Informasi Kesehatan:</strong> Data kesehatan yang Anda input seperti berat badan, tinggi badan, tekanan darah, riwayat kesehatan, dan catatan konsultasi.</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <Icon icon="mdi:circle-small" class="mt-1 h-4 w-4 flex-shrink-0 text-[#43B3FC]" />
                                    <span><strong>Informasi Penggunaan:</strong> Data tentang bagaimana Anda berinteraksi dengan platform kami.</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <Icon icon="mdi:circle-small" class="mt-1 h-4 w-4 flex-shrink-0 text-[#43B3FC]" />
                                    <span><strong>Informasi Perangkat:</strong> Jenis perangkat, sistem operasi, dan browser yang digunakan.</span>
                                </li>
                            </ul>
                        </section>

                        <section class="mb-8">
                            <h2 class="mb-4 text-[20px] font-semibold text-[#1b1b18]">3. Penggunaan Informasi</h2>
                            <p class="mb-3 text-[14px] leading-relaxed text-[#1b1b18]/70">
                                Kami menggunakan informasi yang dikumpulkan untuk:
                            </p>
                            <ul class="ml-4 space-y-2 text-[14px] text-[#1b1b18]/70">
                                <li class="flex items-start gap-2">
                                    <Icon icon="mdi:check-circle" class="mt-0.5 h-4 w-4 flex-shrink-0 text-green-500" />
                                    <span>Menyediakan dan memelihara layanan konsultasi kesehatan AI</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <Icon icon="mdi:check-circle" class="mt-0.5 h-4 w-4 flex-shrink-0 text-green-500" />
                                    <span>Mempersonalisasi pengalaman pengguna</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <Icon icon="mdi:check-circle" class="mt-0.5 h-4 w-4 flex-shrink-0 text-green-500" />
                                    <span>Mengirimkan informasi dan pembaruan terkait layanan</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <Icon icon="mdi:check-circle" class="mt-0.5 h-4 w-4 flex-shrink-0 text-green-500" />
                                    <span>Meningkatkan kualitas layanan dan mengembangkan fitur baru</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <Icon icon="mdi:check-circle" class="mt-0.5 h-4 w-4 flex-shrink-0 text-green-500" />
                                    <span>Mematuhi kewajiban hukum</span>
                                </li>
                            </ul>
                        </section>

                        <section class="mb-8">
                            <h2 class="mb-4 text-[20px] font-semibold text-[#1b1b18]">4. Keamanan Data</h2>
                            <p class="text-[14px] leading-relaxed text-[#1b1b18]/70">
                                Kami menerapkan langkah-langkah keamanan yang sesuai untuk melindungi data Anda, termasuk:
                            </p>
                            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                                <div class="rounded-xl bg-[#F8F8F8] p-4">
                                    <Icon icon="mdi:lock" class="mb-2 h-6 w-6 text-[#43B3FC]" />
                                    <p class="text-[13px] font-medium text-[#1b1b18]">Enkripsi SSL/TLS</p>
                                    <p class="text-[12px] text-[#1b1b18]/60">Semua data dienkripsi saat transit</p>
                                </div>
                                <div class="rounded-xl bg-[#F8F8F8] p-4">
                                    <Icon icon="mdi:database-lock" class="mb-2 h-6 w-6 text-[#BF55FF]" />
                                    <p class="text-[13px] font-medium text-[#1b1b18]">Enkripsi Database</p>
                                    <p class="text-[12px] text-[#1b1b18]/60">Data sensitif dienkripsi saat disimpan</p>
                                </div>
                                <div class="rounded-xl bg-[#F8F8F8] p-4">
                                    <Icon icon="mdi:shield-check" class="mb-2 h-6 w-6 text-[#F4AFE9]" />
                                    <p class="text-[13px] font-medium text-[#1b1b18]">Akses Terbatas</p>
                                    <p class="text-[12px] text-[#1b1b18]/60">Hanya personel terotorisasi</p>
                                </div>
                                <div class="rounded-xl bg-[#F8F8F8] p-4">
                                    <Icon icon="mdi:backup-restore" class="mb-2 h-6 w-6 text-[#8DD0FC]" />
                                    <p class="text-[13px] font-medium text-[#1b1b18]">Backup Rutin</p>
                                    <p class="text-[12px] text-[#1b1b18]/60">Pencadangan data berkala</p>
                                </div>
                            </div>
                        </section>

                        <section class="mb-8">
                            <h2 class="mb-4 text-[20px] font-semibold text-[#1b1b18]">5. Berbagi Informasi</h2>
                            <p class="text-[14px] leading-relaxed text-[#1b1b18]/70">
                                Kami <strong>tidak akan menjual</strong> data pribadi Anda kepada pihak ketiga. Kami hanya dapat membagikan informasi dalam kondisi berikut:
                            </p>
                            <ul class="mt-3 ml-4 space-y-2 text-[14px] text-[#1b1b18]/70">
                                <li class="flex items-start gap-2">
                                    <Icon icon="mdi:circle-small" class="mt-1 h-4 w-4 flex-shrink-0 text-[#43B3FC]" />
                                    <span>Dengan persetujuan eksplisit dari Anda</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <Icon icon="mdi:circle-small" class="mt-1 h-4 w-4 flex-shrink-0 text-[#43B3FC]" />
                                    <span>Untuk memenuhi kewajiban hukum atau perintah pengadilan</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <Icon icon="mdi:circle-small" class="mt-1 h-4 w-4 flex-shrink-0 text-[#43B3FC]" />
                                    <span>Kepada penyedia layanan terpercaya yang membantu operasional kami</span>
                                </li>
                            </ul>
                        </section>

                        <section class="mb-8">
                            <h2 class="mb-4 text-[20px] font-semibold text-[#1b1b18]">6. Hak Pengguna</h2>
                            <p class="mb-3 text-[14px] leading-relaxed text-[#1b1b18]/70">
                                Anda memiliki hak untuk:
                            </p>
                            <ul class="ml-4 space-y-2 text-[14px] text-[#1b1b18]/70">
                                <li class="flex items-start gap-2">
                                    <Icon icon="mdi:eye" class="mt-0.5 h-4 w-4 flex-shrink-0 text-[#43B3FC]" />
                                    <span><strong>Mengakses:</strong> Meminta salinan data pribadi Anda</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <Icon icon="mdi:pencil" class="mt-0.5 h-4 w-4 flex-shrink-0 text-[#BF55FF]" />
                                    <span><strong>Memperbaiki:</strong> Memperbarui informasi yang tidak akurat</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <Icon icon="mdi:delete" class="mt-0.5 h-4 w-4 flex-shrink-0 text-[#F4AFE9]" />
                                    <span><strong>Menghapus:</strong> Meminta penghapusan data Anda</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <Icon icon="mdi:download" class="mt-0.5 h-4 w-4 flex-shrink-0 text-[#8DD0FC]" />
                                    <span><strong>Portabilitas:</strong> Meminta data dalam format yang dapat dibaca mesin</span>
                                </li>
                            </ul>
                        </section>

                        <section class="mb-8">
                            <h2 class="mb-4 text-[20px] font-semibold text-[#1b1b18]">7. Cookie</h2>
                            <p class="text-[14px] leading-relaxed text-[#1b1b18]/70">
                                Kami menggunakan cookie dan teknologi pelacakan serupa untuk meningkatkan pengalaman pengguna. Anda dapat mengatur preferensi cookie melalui pengaturan browser Anda.
                            </p>
                        </section>

                        <section class="mb-8">
                            <h2 class="mb-4 text-[20px] font-semibold text-[#1b1b18]">8. Perubahan Kebijakan</h2>
                            <p class="text-[14px] leading-relaxed text-[#1b1b18]/70">
                                Kami dapat memperbarui Kebijakan Privasi ini dari waktu ke waktu. Perubahan akan diposting di halaman ini dengan tanggal "Terakhir diperbarui" yang baru. Kami mendorong Anda untuk meninjau kebijakan ini secara berkala.
                            </p>
                        </section>

                        <section class="rounded-xl bg-gradient-to-r from-[#F4AFE9]/20 to-[#8DD0FC]/20 p-6">
                            <h2 class="mb-3 text-[18px] font-semibold text-[#1b1b18]">9. Hubungi Kami</h2>
                            <p class="text-[14px] text-[#1b1b18]/70">
                                Jika Anda memiliki pertanyaan tentang Kebijakan Privasi ini, silakan hubungi kami:
                            </p>
                            <div class="mt-4 flex flex-wrap gap-4">
                                <a href="mailto:privacy@docdot.id" class="inline-flex items-center gap-2 text-[14px] text-[#43B3FC] hover:underline">
                                    <Icon icon="mdi:email" class="h-4 w-4" />
                                    privacy@docdot.id
                                </a>
                                <Link href="/contact" class="inline-flex items-center gap-2 text-[14px] text-[#43B3FC] hover:underline">
                                    <Icon icon="mdi:message" class="h-4 w-4" />
                                    Form Kontak
                                </Link>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </section>

        <Footer />
    </div>
</template>
