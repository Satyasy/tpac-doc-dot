<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import Navbar from '@/components/Navbar.vue';
import Footer from '@/components/Footer.vue';
import { Icon } from '@iconify/vue';
import { ref, watch } from 'vue';
import { useScrollAnimation } from '@/composables/useScrollAnimation';

useScrollAnimation();

interface Article {
    id: number;
    title: string;
    slug: string;
    content: string;
    category: string;
    source: string | null;
    published_at: string;
}

interface PaginatedArticles {
    data: Article[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    links: { url: string | null; label: string; active: boolean }[];
}

interface Props {
    articles: PaginatedArticles;
    featuredArticle: Article | null;
    popularArticles: Article[];
    categories: Record<string, number>;
    currentCategory: string | null;
    searchQuery: string | null;
}

const props = defineProps<Props>();

const searchInput = ref(props.searchQuery || '');
const selectedCategory = ref(props.currentCategory || '');

const allCategories = [
    'Berita & Update Kesehatan',
    'Edukasi Kesehatan',
    'Tips & Gaya Hidup Sehat',
    'Pencegahan & Perawatan',
];

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
};

const getCategoryColor = (category: string) => {
    const colors: Record<string, string> = {
        'Berita & Update Kesehatan': 'from-[#F4AFE9] to-[#8DD0FC]',
        'Edukasi Kesehatan': 'bg-[#FF7CEA]',
        'Tips & Gaya Hidup Sehat': 'from-[#8DD0FC] to-[#43B3FC]',
        'Pencegahan & Perawatan': 'from-[#DDB4F6] to-[#F4AFE9]',
    };
    return colors[category] || 'from-[#F4AFE9] to-[#8DD0FC]';
};

const truncateContent = (content: string, length: number = 150) => {
    const stripped = content.replace(/<[^>]*>/g, '');
    return stripped.length > length ? stripped.substring(0, length) + '...' : stripped;
};

const filterByCategory = (category: string) => {
    selectedCategory.value = category;
    router.get('/article', {
        category: category || undefined,
        search: searchInput.value || undefined,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const handleSearch = () => {
    router.get('/article', {
        category: selectedCategory.value || undefined,
        search: searchInput.value || undefined,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const handleSearchKeydown = (e: KeyboardEvent) => {
    if (e.key === 'Enter') {
        handleSearch();
    }
};
</script>

<template>
    <Head title="Article" />
    <div class="min-h-screen overflow-x-hidden pt-16 sm:pt-20 lg:pt-22" style="background: linear-gradient(to left, rgba(141, 208, 252, 0.6) 0%, rgba(221, 180, 246, 0.6) 100%);">
        <Navbar />

        <!-- Hero Section -->
        <section class="relative px-4 pt-8 pb-8 sm:px-6 sm:pt-12 sm:pb-12 lg:px-12 lg:pt-16">
            <!-- Floating badge left -->
            <div class="absolute top-52 left-24 z-10 hidden rounded-full bg-gradient-to-r from-[#F4AFE9] to-[#8DD0FC] px-6 py-2 text-[14px] font-medium text-white xl:block">
                DocDot
            </div>

            <!-- Floating badge right -->
            <div class="absolute top-32 right-24 z-10 hidden rounded-full bg-gradient-to-r from-[#F4AFE9] to-[#8DD0FC] px-6 py-2 text-[14px] font-medium text-white xl:block">
                DocDot
            </div>

            <div class="mx-auto max-w-4xl text-center">
                <h1 class="scroll-animate text-[28px] font-semibold leading-tight text-[#1b1b18] sm:text-[36px] lg:text-[56px]">
                    Cara lebih baik untuk belajar<br class="hidden sm:block" />soal <span style="background: linear-gradient(to right, #BF55FF 0%, #43B3FC 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">kesehatan</span>, setiap hari
                </h1>

                <p class="mt-4 text-[14px] font-light text-[#1b1b18]/80 sm:mt-6 sm:text-[16px] lg:text-[20px]">
                    Temukan jawaban dan wawasan seputar kesehatan di artikel-artikel<br class="hidden lg:block" />pilihan kami. Singkat, jelas, terpercaya!
                </p>

                <!-- Search Box -->
                <div class="mx-auto mt-6 w-full max-w-[550px] overflow-hidden rounded-full border border-[#E0E0E0] bg-white px-4 py-2.5 sm:mt-10 sm:px-6 sm:py-3 lg:mt-16">
                    <div class="flex items-center justify-between">
                        <input 
                            v-model="searchInput"
                            @keydown="handleSearchKeydown"
                            type="text" 
                            placeholder="Butuh info? Ketik topik di sini..."
                            class="w-full border-none bg-transparent text-[14px] text-[#1b1b18] placeholder-[#1b1b18]/40 outline-none focus:outline-none focus:ring-0 sm:text-[15px]"
                        />
                        <button @click="handleSearch" class="flex-shrink-0">
                            <Icon icon="mdi:magnify" class="h-5 w-5 text-[#DDB4F6] transition-colors hover:text-[#8DD0FC] sm:h-6 sm:w-6" />
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Kategori Artikel Section -->
        <section class="bg-white px-4 py-8 sm:px-6 sm:py-12 lg:px-12">
            <h2 class="scroll-animate mb-6 text-center text-[24px] font-semibold text-[#1b1b18] sm:mb-8 sm:text-[28px] lg:text-[36px]">Kategori Artikel</h2>
            
            <!-- Category Tabs -->
            <div class="mb-6 flex flex-wrap justify-center gap-2 sm:mb-10 sm:gap-3 lg:gap-4">
                <button 
                    @click="filterByCategory('')"
                    :class="[
                        'rounded-full px-3 py-1.5 text-[12px] font-medium transition-all sm:px-4 sm:py-2 sm:text-[13px] lg:px-6 lg:text-[14px]',
                        !selectedCategory 
                            ? 'bg-gradient-to-r from-[#F4AFE9] to-[#8DD0FC] text-white' 
                            : 'border border-[#1b1b18]/20 bg-white text-[#1b1b18] hover:border-[#8DD0FC]'
                    ]"
                >
                    Semua
                </button>
                <button 
                    v-for="category in allCategories"
                    :key="category"
                    @click="filterByCategory(category)"
                    :class="[
                        'rounded-full px-3 py-1.5 text-[12px] font-medium transition-all sm:px-4 sm:py-2 sm:text-[13px] lg:px-6 lg:text-[14px]',
                        selectedCategory === category 
                            ? 'bg-gradient-to-r from-[#F4AFE9] to-[#8DD0FC] text-white' 
                            : 'border border-[#1b1b18]/20 bg-white text-[#1b1b18] hover:border-[#8DD0FC]'
                    ]"
                >
                    {{ category }}
                </button>
            </div>

            <!-- Featured Article & Latest Articles -->
            <div v-if="featuredArticle && articles.data.length > 0" class="mx-auto max-w-6xl">
                <div class="flex flex-col gap-4 sm:gap-6 lg:h-[450px] lg:flex-row">
                    <!-- Main Article (Left) -->
                    <Link 
                        :href="`/article/${featuredArticle.slug}`"
                        class="relative h-[250px] flex-[2] overflow-hidden rounded-[16px] transition-transform hover:scale-[1.01] sm:h-[300px] sm:rounded-[20px] lg:h-full"
                        style="background: linear-gradient(to right, #F4AFE9 0%, #8DD0FC 100%);"
                    >
                        <div class="h-full w-full bg-gradient-to-r from-[#F4AFE9] to-[#8DD0FC]"></div>
                        <!-- Full shadow overlay -->
                        <div class="absolute inset-0" style="background: linear-gradient(to top, #005A94 0%, #007BCD 33%, rgba(141, 208, 252, 0.3) 66%, rgba(141, 208, 252, 0) 100%);"></div>
                        <div class="absolute bottom-0 left-0 right-0 p-4 sm:p-6">
                            <span 
                                class="mb-2 inline-block rounded-full px-3 py-1 text-[10px] font-medium text-white sm:text-[11px]"
                                :class="getCategoryColor(featuredArticle.category).includes('bg-') ? getCategoryColor(featuredArticle.category) : 'bg-gradient-to-r ' + getCategoryColor(featuredArticle.category)"
                            >
                                {{ featuredArticle.category }}
                            </span>
                            <h3 class="text-[16px] font-semibold leading-tight text-white sm:text-[18px] lg:text-[20px]">
                                {{ featuredArticle.title }}
                            </h3>
                            <p class="mt-2 flex items-center gap-1 text-[11px] text-white/80 sm:text-[12px]">
                                <Icon icon="mdi:calendar" class="h-4 w-4" />
                                {{ formatDate(featuredArticle.published_at) }}
                            </p>
                        </div>
                    </Link>

                    <!-- Right Articles -->
                    <div class="grid flex-1 gap-3 sm:grid-cols-2 lg:flex lg:flex-col">
                        <Link 
                            v-for="article in articles.data.slice(0, 3)" 
                            :key="article.id"
                            :href="`/article/${article.slug}`"
                            class="flex flex-1 gap-3 overflow-hidden rounded-[12px] bg-[#F8F8F8] p-2.5 transition-all hover:bg-[#F0F0F0] sm:gap-4 sm:rounded-[15px] sm:p-3"
                        >
                            <div class="relative h-[80px] w-[80px] flex-shrink-0 overflow-hidden rounded-[8px] sm:h-full sm:w-[100px] sm:rounded-[10px] lg:w-[140px]">
                                <div class="h-full w-full bg-gradient-to-r from-[#F4AFE9]/30 to-[#8DD0FC]/30"></div>
                                <div class="absolute inset-0 rounded-[8px] sm:rounded-[10px]" style="background: linear-gradient(to top, rgba(0, 90, 148, 0.5) 0%, rgba(0, 123, 205, 0.3) 50%, transparent 100%);"></div>
                            </div>
                            <div class="flex flex-1 flex-col justify-center">
                                <span 
                                    class="mb-1 inline-block w-fit rounded-full px-2 py-0.5 text-[9px] font-medium text-white sm:px-3 sm:py-1 sm:text-[10px]"
                                    :class="getCategoryColor(article.category).includes('bg-') ? getCategoryColor(article.category) : 'bg-gradient-to-r ' + getCategoryColor(article.category)"
                                >
                                    {{ article.category }}
                                </span>
                                <h4 class="line-clamp-2 text-[12px] font-semibold leading-tight text-[#1b1b18] sm:text-[13px] lg:text-[14px]">
                                    {{ article.title }}
                                </h4>
                                <p class="mt-1 flex items-center gap-1 text-[10px] text-[#1b1b18]/60 sm:text-[11px]">
                                    <Icon icon="mdi:calendar" class="h-3 w-3" />
                                    {{ formatDate(article.published_at) }}
                                </p>
                            </div>
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="mx-auto max-w-2xl py-16 text-center">
                <Icon icon="mdi:newspaper-variant-outline" class="mx-auto h-20 w-20 text-[#1b1b18]/20" />
                <h3 class="mt-4 text-[20px] font-semibold text-[#1b1b18]">Belum Ada Artikel</h3>
                <p class="mt-2 text-[14px] text-[#1b1b18]/60">
                    {{ searchQuery ? 'Tidak ditemukan artikel dengan kata kunci tersebut.' : 'Artikel akan segera hadir.' }}
                </p>
                <button 
                    v-if="searchQuery || selectedCategory"
                    @click="filterByCategory('')"
                    class="mt-4 rounded-full bg-gradient-to-r from-[#F4AFE9] to-[#8DD0FC] px-6 py-2 text-[14px] font-medium text-white"
                >
                    Lihat Semua Artikel
                </button>
            </div>
        </section>

        <!-- Artikel Terpopuler Section -->
        <section v-if="popularArticles.length > 0" class="px-4 py-8 sm:px-6 sm:py-12 lg:px-12 lg:py-16">
            <h2 class="scroll-animate mb-6 text-center text-[24px] font-semibold text-[#1b1b18] sm:mb-8 sm:text-[28px] lg:mb-10 lg:text-[36px]">Artikel Terpopuler</h2>
            
            <div class="scroll-animate scroll-animate-delay-1 mx-auto grid max-w-6xl gap-4 sm:grid-cols-2 sm:gap-6 lg:grid-cols-3">
                <Link 
                    v-for="article in popularArticles" 
                    :key="article.id"
                    :href="`/article/${article.slug}`"
                    class="overflow-hidden rounded-xl bg-white transition-transform hover:scale-[1.02]"
                >
                    <div class="h-[160px] w-full bg-gradient-to-r from-[#F4AFE9]/30 to-[#8DD0FC]/30 sm:h-[200px]"></div>
                    <div class="p-4 sm:p-5">
                        <div class="mb-3 flex flex-wrap items-center gap-2">
                            <span 
                                class="rounded-full px-3 py-1 text-[10px] font-medium text-white"
                                :class="getCategoryColor(article.category).includes('bg-') ? getCategoryColor(article.category) : 'bg-gradient-to-r ' + getCategoryColor(article.category)"
                            >
                                {{ article.category }}
                            </span>
                            <span class="flex items-center gap-1 text-[11px] text-[#1b1b18]/60">
                                <Icon icon="mdi:calendar" class="h-3 w-3" />
                                {{ formatDate(article.published_at) }}
                            </span>
                        </div>
                        <h3 class="mb-2 line-clamp-2 text-[16px] font-semibold leading-tight text-[#1b1b18]">
                            {{ article.title }}
                        </h3>
                        <p class="mb-4 line-clamp-3 text-[12px] leading-relaxed text-[#1b1b18]/70">
                            {{ truncateContent(article.content) }}
                        </p>
                        <span class="rounded-full border border-[#1b1b18]/20 px-4 py-2 text-[12px] font-medium text-[#1b1b18]">
                            Selengkapnya
                        </span>
                    </div>
                </Link>
            </div>
        </section>

        <!-- All Articles Grid -->
        <section v-if="articles.data.length > 3" class="scroll-animate bg-white px-4 py-8 sm:px-6 sm:py-12 lg:px-12 lg:py-16">
            <h2 class="mb-6 text-center text-[24px] font-semibold text-[#1b1b18] sm:mb-8 sm:text-[28px] lg:mb-10 lg:text-[36px]">Semua Artikel</h2>
            
            <div class="mx-auto grid max-w-6xl gap-4 sm:grid-cols-2 sm:gap-6 lg:grid-cols-3">
                <Link 
                    v-for="article in articles.data.slice(3)" 
                    :key="article.id"
                    :href="`/article/${article.slug}`"
                    class="overflow-hidden rounded-xl bg-[#F8F8F8] transition-all hover:bg-[#F0F0F0]"
                >
                    <div class="p-4 sm:p-5">
                        <div class="mb-3 flex flex-wrap items-center gap-2">
                            <span 
                                class="rounded-full px-3 py-1 text-[10px] font-medium text-white"
                                :class="getCategoryColor(article.category).includes('bg-') ? getCategoryColor(article.category) : 'bg-gradient-to-r ' + getCategoryColor(article.category)"
                            >
                                {{ article.category }}
                            </span>
                            <span class="flex items-center gap-1 text-[11px] text-[#1b1b18]/60">
                                <Icon icon="mdi:calendar" class="h-3 w-3" />
                                {{ formatDate(article.published_at) }}
                            </span>
                        </div>
                        <h3 class="mb-2 line-clamp-2 text-[16px] font-semibold leading-tight text-[#1b1b18]">
                            {{ article.title }}
                        </h3>
                        <p class="line-clamp-2 text-[12px] leading-relaxed text-[#1b1b18]/70">
                            {{ truncateContent(article.content, 100) }}
                        </p>
                    </div>
                </Link>
            </div>

            <!-- Pagination -->
            <div v-if="articles.last_page > 1" class="mt-6 flex flex-wrap justify-center gap-2 sm:mt-10">
                <template v-for="link in articles.links" :key="link.label">
                    <Link 
                        v-if="link.url"
                        :href="link.url"
                        :class="[
                            'flex h-8 w-8 items-center justify-center rounded-full text-[12px] transition-all sm:h-10 sm:w-10 sm:text-[14px]',
                            link.active 
                                ? 'bg-gradient-to-r from-[#F4AFE9] to-[#8DD0FC] text-white' 
                                : 'bg-white text-[#1b1b18] hover:bg-[#F0F0F0]'
                        ]"
                        v-html="link.label"
                        preserve-scroll
                    />
                    <span 
                        v-else
                        class="flex h-8 w-8 items-center justify-center rounded-full bg-white/50 text-[12px] text-[#1b1b18]/40 sm:h-10 sm:w-10 sm:text-[14px]"
                        v-html="link.label"
                    />
                </template>
            </div>
        </section>
        <section class="scroll-animate px-4 py-8 sm:px-6 sm:py-12 lg:px-12 lg:py-16">
            <h2 class="mb-6 text-center text-[24px] font-semibold text-[#1b1b18] sm:mb-8 sm:text-[28px] lg:mb-10 lg:text-[36px]">5 Tips Kesehatan Harian</h2>
            
            <div class="mx-auto flex max-w-6xl flex-col items-center gap-8 lg:flex-row lg:justify-between lg:px-8">
                <!-- Robot Image -->
                <div class="hidden flex-shrink-0 lg:block">
                    <img src="/images/tips.png" alt="Robot" class="h-[320px] w-auto xl:h-[420px]" />
                </div>

                <!-- Tips List -->
                <div class="flex w-full max-w-[480px] flex-col gap-2 sm:gap-3 lg:mr-16">
                    <div class="flex h-[48px] items-stretch overflow-hidden rounded-lg sm:h-[56px] sm:rounded-xl">
                        <div class="flex w-[60px] items-center justify-center bg-white sm:w-[75px]">
                            <span class="text-[20px] font-bold text-[#F4AFE9] sm:text-[24px]">01</span>
                        </div>
                        <div class="flex flex-1 items-center bg-[#FFD3F8] px-4 sm:px-5">
                            <span class="text-[14px] font-medium text-[#1b1b18] sm:text-[16px]">Konsumsi Makanan Seimbang 🥗</span>
                        </div>
                    </div>
                    <div class="flex h-[48px] items-stretch overflow-hidden rounded-lg sm:h-[56px] sm:rounded-xl">
                        <div class="flex w-[60px] items-center justify-center bg-white sm:w-[75px]">
                            <span class="text-[20px] font-bold text-[#F4AFE9] sm:text-[24px]">02</span>
                        </div>
                        <div class="flex flex-1 items-center bg-[#FFD3F8] px-4 sm:px-5">
                            <span class="text-[14px] font-medium text-[#1b1b18] sm:text-[16px]">Rutin Berolahraga 🏃</span>
                        </div>
                    </div>
                    <div class="flex h-[48px] items-stretch overflow-hidden rounded-lg sm:h-[56px] sm:rounded-xl">
                        <div class="flex w-[60px] items-center justify-center bg-white sm:w-[75px]">
                            <span class="text-[20px] font-bold text-[#F4AFE9] sm:text-[24px]">03</span>
                        </div>
                        <div class="flex flex-1 items-center bg-[#FFD3F8] px-4 sm:px-5">
                            <span class="text-[14px] font-medium text-[#1b1b18] sm:text-[16px]">Istirahat yang cukup 😴</span>
                        </div>
                    </div>
                    <div class="flex h-[48px] items-stretch overflow-hidden rounded-lg sm:h-[56px] sm:rounded-xl">
                        <div class="flex w-[60px] items-center justify-center bg-white sm:w-[75px]">
                            <span class="text-[20px] font-bold text-[#F4AFE9] sm:text-[24px]">04</span>
                        </div>
                        <div class="flex flex-1 items-center bg-[#FFD3F8] px-4 sm:px-5">
                            <span class="text-[14px] font-medium text-[#1b1b18] sm:text-[16px]">Minum Air yang Cukup 💧</span>
                        </div>
                    </div>
                    <div class="flex h-[48px] items-stretch overflow-hidden rounded-lg sm:h-[56px] sm:rounded-xl">
                        <div class="flex w-[60px] items-center justify-center bg-white sm:w-[75px]">
                            <span class="text-[20px] font-bold text-[#F4AFE9] sm:text-[24px]">05</span>
                        </div>
                        <div class="flex flex-1 items-center bg-[#FFD3F8] px-4 sm:px-5">
                            <span class="text-[14px] font-medium text-[#1b1b18] sm:text-[16px]">Kelola Stres dengan Baik 🧘</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Ingin mengtahui Gejala Section -->
        <section class="px-4 py-8 sm:px-6 sm:py-12 lg:mt-16 lg:px-12 lg:pt-40 lg:pb-16">
            <div class="mx-auto max-w-6xl">
                <!-- Mobile Layout -->
                <div class="block rounded-[20px] bg-gradient-to-r from-[#8DD0FC] to-[#DDB4F6] p-6 sm:rounded-[30px] sm:p-8 lg:hidden">
                    <h2 class="flex items-center gap-2 text-[24px] font-bold text-[#1b1b18] sm:gap-3 sm:text-[28px]">
                        Ingin mengtahui
                        <Icon icon="carbon:circle-dash" class="h-6 w-6 text-[#1b1b18] sm:h-8 sm:w-8" />
                    </h2>
                    <h3 class="text-[24px] font-bold text-[#1b1b18] sm:text-[28px]">
                        <span class="text-[#43B3FC]">Gejala</span> anda?
                    </h3>
                    <p class="mt-3 text-[14px] font-light text-[#1b1b18]/80 sm:text-[16px]">
                        Yuk Periksa menggunakan Chatbot Sekarang juga!
                    </p>
                    <Link href="/consultation" class="mt-5 inline-flex w-fit items-center justify-between gap-4 rounded-full border-2 border-[#1b1b18] bg-white px-5 py-2 text-[14px] font-medium text-[#1b1b18] transition-colors hover:bg-[#1b1b18] hover:text-white sm:gap-6 sm:px-6 sm:text-[16px]">
                        Chatbot
                        <Icon icon="mdi:arrow-top-right" class="h-4 w-4 sm:h-5 sm:w-5" />
                    </Link>
                </div>

                <!-- Desktop Layout -->
                <div class="relative hidden h-[400px] items-center rounded-[30px] bg-gradient-to-r from-[#8DD0FC] to-[#DDB4F6] px-10 lg:flex">
                    <div class="absolute inset-0 overflow-hidden rounded-[30px]">
                        <div class="absolute bottom-16 -right-17 h-[50px] w-[500px] rounded-l-full bg-white/20" style="transform: rotate(-35deg); transform-origin: bottom right;"></div>
                    </div>

                    <div class="absolute right-24 top-16 z-10 max-w-sm">
                        <h2 class="flex items-center gap-3 text-[32px] font-bold text-[#1b1b18]">
                            Ingin mengtahui
                            <Icon icon="carbon:circle-dash" class="h-8 w-8 text-[#1b1b18]" />
                        </h2>
                        <h3 class="text-[32px] font-bold text-[#1b1b18]">
                            <span class="text-[#43B3FC]">Gejala</span> anda?
                        </h3>
                        <p class="mt-3 text-[18px] font-light text-[#1b1b18]/80">
                            Yuk Periksa menggunakan<br />Chatbot Sekarang juga!
                        </p>
                        <Link href="/consultation" class="mt-5 inline-flex w-fit items-center justify-between gap-6 rounded-full border-2 border-[#1b1b18] bg-white px-6 py-2 text-[16px] font-medium text-[#1b1b18] transition-colors hover:bg-[#1b1b18] hover:text-white">
                            Chatbot
                            <Icon icon="mdi:arrow-top-right" class="h-5 w-5" />
                        </Link>
                    </div>
                    <div class="absolute left-10 -top-32 z-20">
                        <img src="/images/gejala.png" alt="Gejala" class="w-[600px] max-w-none" />
                    </div>
                </div>
            </div>
        </section>

        <Footer />
    </div>
</template>
