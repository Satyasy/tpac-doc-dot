<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Icon } from '@iconify/vue';
import Navbar from '@/components/Navbar.vue';
import Footer from '@/components/Footer.vue';
import { useScrollAnimation } from '@/composables/useScrollAnimation';
import { ref } from 'vue';

useScrollAnimation();

const form = useForm({
    name: '',
    email: '',
    subject: '',
    message: '',
});

const showSuccess = ref(false);

const submit = () => {
    form.post('/contact', {
        preserveScroll: true,
        onSuccess: () => {
            showSuccess.value = true;
            form.reset();
            setTimeout(() => {
                showSuccess.value = false;
            }, 5000);
        },
    });
};

const faqs = [
    {
        question: 'Apakah konsultasi di DocDot gratis?',
        answer: 'Ya, konsultasi dengan AI DocDot sepenuhnya gratis. Anda hanya perlu mendaftar dan verifikasi email untuk mulai berkonsultasi.',
    },
    {
        question: 'Apakah data kesehatan saya aman?',
        answer: 'Keamanan data adalah prioritas kami. Semua data dienkripsi dan disimpan dengan standar keamanan tinggi. Kami tidak akan membagikan data Anda kepada pihak ketiga.',
    },
    {
        question: 'Apakah DocDot bisa menggantikan dokter?',
        answer: 'DocDot adalah asisten kesehatan AI yang memberikan informasi umum. Untuk diagnosis dan pengobatan, tetap konsultasikan dengan dokter atau tenaga medis profesional.',
    },
    {
        question: 'Bagaimana cara menghubungi customer service?',
        answer: 'Anda dapat menghubungi kami melalui form di halaman ini, email ke support@docdot.id, atau WhatsApp di nomor yang tertera.',
    },
];

const openFaq = ref<number | null>(null);

const toggleFaq = (index: number) => {
    openFaq.value = openFaq.value === index ? null : index;
};

const contactInfo = [
    {
        icon: 'mdi:email-outline',
        label: 'Email',
        value: 'support@docdot.id',
        href: 'mailto:support@docdot.id',
    },
    {
        icon: 'mdi:whatsapp',
        label: 'WhatsApp',
        value: '+62 812-3456-7890',
        href: 'https://wa.me/6281234567890',
    },
    {
        icon: 'mdi:map-marker-outline',
        label: 'Alamat',
        value: 'Jakarta, Indonesia',
        href: null,
    },
];
</script>

<template>
    <Head title="Hubungi Kami - DocDot" />

    <div class="min-h-screen pt-16 sm:pt-20 lg:pt-22 font-[Poppins]" style="background: linear-gradient(to left, rgba(141, 208, 252, 0.6) 0%, rgba(221, 180, 246, 0.6) 100%)">
        <Navbar />

        <!-- Hero Section -->
        <section class="px-4 py-8 sm:px-6 sm:py-12 lg:px-12 lg:py-16">
            <div class="mx-auto max-w-5xl text-center">
                <h1 class="scroll-animate text-[28px] font-bold text-[#1b1b18] sm:text-[36px] lg:text-[48px]">
                    Hubungi <span class="bg-gradient-to-r from-[#BF55FF] to-[#43B3FC] bg-clip-text text-transparent">Kami</span>
                </h1>
                <p class="scroll-animate scroll-animate-delay-1 mx-auto mt-3 max-w-2xl text-[14px] text-[#1b1b18]/70 sm:mt-4 sm:text-[16px] lg:text-[18px]">
                    Punya pertanyaan atau saran? Kami siap membantu Anda. Hubungi kami melalui form di bawah atau kontak langsung.
                </p>
            </div>
        </section>

        <!-- Contact Section -->
        <section class="px-4 pb-8 sm:px-6 sm:pb-12 lg:px-12 lg:pb-16">
            <div class="mx-auto max-w-6xl">
                <div class="grid gap-6 lg:grid-cols-2 lg:gap-8">
                    <!-- Contact Form -->
                    <div class="scroll-animate rounded-xl bg-white p-5 sm:rounded-2xl sm:p-6 lg:p-8">
                        <h2 class="mb-4 text-[18px] font-semibold text-[#1b1b18] sm:mb-6 sm:text-[20px]">Kirim Pesan</h2>

                        <!-- Success Message -->
                        <div 
                            v-if="showSuccess"
                            class="mb-6 flex items-center gap-3 rounded-xl bg-green-50 p-4 text-green-700"
                        >
                            <Icon icon="mdi:check-circle" class="h-5 w-5 flex-shrink-0" />
                            <p class="text-[14px]">Pesan Anda berhasil dikirim! Kami akan segera menghubungi Anda.</p>
                        </div>

                        <form @submit.prevent="submit" class="space-y-4">
                            <div>
                                <label class="mb-1 block text-[13px] font-medium text-[#1b1b18]">Nama Lengkap</label>
                                <input
                                    v-model="form.name"
                                    type="text"
                                    required
                                    placeholder="Masukkan nama Anda"
                                    class="w-full rounded-xl border border-[#1b1b18]/10 bg-[#F8F8F8] px-4 py-3 text-[14px] outline-none focus:border-[#8DD0FC]"
                                />
                                <p v-if="form.errors.name" class="mt-1 text-[12px] text-red-500">{{ form.errors.name }}</p>
                            </div>

                            <div>
                                <label class="mb-1 block text-[13px] font-medium text-[#1b1b18]">Email</label>
                                <input
                                    v-model="form.email"
                                    type="email"
                                    required
                                    placeholder="email@example.com"
                                    class="w-full rounded-xl border border-[#1b1b18]/10 bg-[#F8F8F8] px-4 py-3 text-[14px] outline-none focus:border-[#8DD0FC]"
                                />
                                <p v-if="form.errors.email" class="mt-1 text-[12px] text-red-500">{{ form.errors.email }}</p>
                            </div>

                            <div>
                                <label class="mb-1 block text-[13px] font-medium text-[#1b1b18]">Subjek</label>
                                <select
                                    v-model="form.subject"
                                    required
                                    class="w-full rounded-xl border border-[#1b1b18]/10 bg-[#F8F8F8] px-4 py-3 text-[14px] outline-none focus:border-[#8DD0FC]"
                                >
                                    <option value="">Pilih subjek</option>
                                    <option value="general">Pertanyaan Umum</option>
                                    <option value="feedback">Saran & Masukan</option>
                                    <option value="bug">Laporan Bug</option>
                                    <option value="partnership">Kerjasama</option>
                                    <option value="other">Lainnya</option>
                                </select>
                                <p v-if="form.errors.subject" class="mt-1 text-[12px] text-red-500">{{ form.errors.subject }}</p>
                            </div>

                            <div>
                                <label class="mb-1 block text-[13px] font-medium text-[#1b1b18]">Pesan</label>
                                <textarea
                                    v-model="form.message"
                                    required
                                    rows="5"
                                    placeholder="Tulis pesan Anda di sini..."
                                    class="w-full rounded-xl border border-[#1b1b18]/10 bg-[#F8F8F8] px-4 py-3 text-[14px] outline-none focus:border-[#8DD0FC]"
                                />
                                <p v-if="form.errors.message" class="mt-1 text-[12px] text-red-500">{{ form.errors.message }}</p>
                            </div>

                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="w-full rounded-xl bg-gradient-to-r from-[#F4AFE9] to-[#8DD0FC] py-3.5 text-[14px] font-medium text-white transition-all hover:shadow-lg disabled:opacity-50"
                            >
                                {{ form.processing ? 'Mengirim...' : 'Kirim Pesan' }}
                            </button>
                        </form>
                    </div>

                    <!-- Contact Info & FAQ -->
                    <div class="space-y-4 sm:space-y-6">
                        <!-- Contact Info -->
                        <div class="scroll-animate scroll-animate-delay-1 rounded-xl bg-white p-5 sm:rounded-2xl sm:p-6 lg:p-8">
                            <h2 class="mb-4 text-[18px] font-semibold text-[#1b1b18] sm:mb-6 sm:text-[20px]">Informasi Kontak</h2>
                            <div class="space-y-3 sm:space-y-4">
                                <a
                                    v-for="info in contactInfo"
                                    :key="info.label"
                                    :href="info.href || undefined"
                                    class="flex items-center gap-3 rounded-lg bg-[#F8F8F8] p-3 transition-colors sm:gap-4 sm:rounded-xl sm:p-4"
                                    :class="info.href ? 'hover:bg-[#F0F0F0]' : ''"
                                >
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-r from-[#F4AFE9]/30 to-[#8DD0FC]/30 sm:h-12 sm:w-12">
                                        <Icon :icon="info.icon" class="h-5 w-5 text-[#43B3FC] sm:h-6 sm:w-6" />
                                    </div>
                                    <div>
                                        <p class="text-[12px] text-[#1b1b18]/60 sm:text-[13px]">{{ info.label }}</p>
                                        <p class="text-[13px] font-medium text-[#1b1b18] sm:text-[14px]">{{ info.value }}</p>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <!-- FAQ -->
                        <div class="scroll-animate scroll-animate-delay-2 rounded-xl bg-white p-5 sm:rounded-2xl sm:p-6 lg:p-8">
                            <h2 class="mb-4 text-[18px] font-semibold text-[#1b1b18] sm:mb-6 sm:text-[20px]">FAQ</h2>
                            <div class="space-y-2 sm:space-y-3">
                                <div 
                                    v-for="(faq, index) in faqs" 
                                    :key="index"
                                    class="overflow-hidden rounded-lg bg-[#F8F8F8] sm:rounded-xl"
                                >
                                    <button
                                        @click="toggleFaq(index)"
                                        class="flex w-full items-center justify-between p-3 text-left sm:p-4"
                                    >
                                        <span class="pr-2 text-[13px] font-medium text-[#1b1b18] sm:text-[14px]">{{ faq.question }}</span>
                                        <Icon 
                                            :icon="openFaq === index ? 'mdi:chevron-up' : 'mdi:chevron-down'" 
                                            class="h-4 w-4 flex-shrink-0 text-[#1b1b18]/40 sm:h-5 sm:w-5"
                                        />
                                    </button>
                                    <div 
                                        v-show="openFaq === index"
                                        class="px-3 pb-3 sm:px-4 sm:pb-4"
                                    >
                                        <p class="text-[12px] text-[#1b1b18]/70 sm:text-[13px]">{{ faq.answer }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <Footer />
    </div>
</template>
