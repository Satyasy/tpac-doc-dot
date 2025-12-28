<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import Navbar from '@/components/Navbar.vue';
import Footer from '@/components/Footer.vue';
import { Icon } from '@iconify/vue';
import { computed } from 'vue';

interface Article {
    id: number;
    title: string;
    slug: string;
    content: string;
    category: string;
    source: string | null;
    published_at: string;
}

interface Props {
    article: Article;
    relatedArticles: Article[];
}

const props = defineProps<Props>();

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
</script>

<template>
    <Head :title="article.title + ' - DocDot'" />
    <div class="min-h-screen overflow-x-hidden pt-16 sm:pt-20 lg:pt-22" style="background: linear-gradient(to left, rgba(141, 208, 252, 0.6) 0%, rgba(221, 180, 246, 0.6) 100%);">
        <Navbar />

        <!-- Article Header -->
        <section class="px-4 pt-8 pb-6 sm:px-6 sm:pt-12 sm:pb-8 lg:px-12">
            <div class="mx-auto max-w-4xl">
                <!-- Breadcrumb -->
                <div class="mb-4 flex flex-wrap items-center gap-1 text-[12px] text-[#1b1b18]/60 sm:mb-6 sm:gap-2 sm:text-[14px]">
                    <Link href="/" class="hover:text-[#1b1b18]">Home</Link>
                    <Icon icon="mdi:chevron-right" class="h-3 w-3 sm:h-4 sm:w-4" />
                    <Link href="/article" class="hover:text-[#1b1b18]">Artikel</Link>
                    <Icon icon="mdi:chevron-right" class="h-3 w-3 sm:h-4 sm:w-4" />
                    <span class="truncate text-[#1b1b18]">{{ article.category }}</span>
                </div>

                <!-- Category Badge -->
                <span 
                    class="mb-3 inline-block rounded-full px-3 py-1 text-[10px] font-medium text-white sm:mb-4 sm:px-4 sm:py-1.5 sm:text-[12px]"
                    :class="getCategoryColor(article.category).includes('bg-') ? getCategoryColor(article.category) : 'bg-gradient-to-r ' + getCategoryColor(article.category)"
                >
                    {{ article.category }}
                </span>

                <!-- Title -->
                <h1 class="mb-3 text-[24px] font-bold leading-tight text-[#1b1b18] sm:mb-4 sm:text-[30px] lg:text-[36px]">
                    {{ article.title }}
                </h1>

                <!-- Meta -->
                <div class="flex flex-wrap items-center gap-3 text-[12px] text-[#1b1b18]/60 sm:gap-4 sm:text-[14px]">
                    <span class="flex items-center gap-1">
                        <Icon icon="mdi:calendar" class="h-3.5 w-3.5 sm:h-4 sm:w-4" />
                        {{ formatDate(article.published_at) }}
                    </span>
                    <span v-if="article.source" class="flex items-center gap-1">
                        <Icon icon="mdi:link-variant" class="h-3.5 w-3.5 sm:h-4 sm:w-4" />
                        {{ article.source }}
                    </span>
                </div>
            </div>
        </section>

        <!-- Article Content -->
        <section class="px-4 pb-12 sm:px-6 sm:pb-16 lg:px-12">
            <div class="mx-auto max-w-4xl">
                <div class="rounded-2xl bg-white/70 p-4 backdrop-blur-sm sm:rounded-[30px] sm:p-8 lg:p-12">
                    <div 
                        class="prose prose-sm max-w-none text-[#1b1b18] prose-headings:text-[#1b1b18] prose-p:text-[#1b1b18]/80 prose-a:text-[#FF7CEA] prose-strong:text-[#1b1b18] sm:prose-lg"
                        v-html="article.content"
                    ></div>
                </div>

                <!-- Back Button -->
                <div class="mt-6 flex justify-center sm:mt-8">
                    <Link 
                        href="/article" 
                        class="flex items-center gap-2 rounded-full border border-[#1b1b18]/20 bg-white px-4 py-2 text-[13px] font-medium text-[#1b1b18] transition-all hover:bg-[#1b1b18] hover:text-white sm:px-6 sm:py-3 sm:text-[14px]"
                    >
                        <Icon icon="mdi:arrow-left" class="h-4 w-4 sm:h-5 sm:w-5" />
                        Kembali ke Artikel
                    </Link>
                </div>
            </div>
        </section>

        <!-- Related Articles -->
        <section v-if="relatedArticles.length > 0" class="bg-white px-4 py-12 sm:px-6 sm:py-16 lg:px-12">
            <h2 class="mb-6 text-center text-[24px] font-semibold text-[#1b1b18] sm:mb-10 sm:text-[32px]">Artikel Terkait</h2>
            
            <div class="mx-auto grid max-w-6xl gap-4 sm:grid-cols-2 sm:gap-6 lg:grid-cols-3">
                <Link 
                    v-for="related in relatedArticles" 
                    :key="related.id"
                    :href="`/article/${related.slug}`"
                    class="overflow-hidden rounded-lg bg-[#F8F8F8] transition-transform hover:scale-[1.02] sm:rounded-xl"
                >
                    <div class="p-4 sm:p-5">
                        <div class="mb-2 flex flex-wrap items-center gap-2 sm:mb-3 sm:gap-3">
                            <span 
                                class="rounded-full px-2 py-0.5 text-[9px] font-medium text-white sm:px-3 sm:py-1 sm:text-[10px]"
                                :class="getCategoryColor(related.category).includes('bg-') ? getCategoryColor(related.category) : 'bg-gradient-to-r ' + getCategoryColor(related.category)"
                            >
                                {{ related.category }}
                            </span>
                            <span class="flex items-center gap-1 text-[10px] text-[#1b1b18]/60 sm:text-[11px]">
                                <Icon icon="mdi:calendar" class="h-3 w-3" />
                                {{ formatDate(related.published_at) }}
                            </span>
                        </div>
                        <h3 class="line-clamp-2 text-[14px] font-semibold leading-tight text-[#1b1b18] sm:text-[16px]">
                            {{ related.title }}
                        </h3>
                    </div>
                </Link>
            </div>
        </section>

        <Footer />
    </div>
</template>
