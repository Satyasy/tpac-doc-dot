<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Icon } from '@iconify/vue';
import { ref, watch, computed } from 'vue';
import Navbar from '@/components/Navbar.vue';
import Footer from '@/components/Footer.vue';
import { useDebounceFn } from '@vueuse/core';
import { useScrollAnimation } from '@/composables/useScrollAnimation';

interface DrugPrice {
    id: number;
    pharmacy_name: string;
    price_min: number;
    price_max: number;
}

interface Drug {
    id: number;
    name: string;
    image: string | null;
    category: string;
    description: string;
    dosage_info: string | null;
    side_effects: string | null;
    warnings: string | null;
    pregnancy_safe: boolean;
    prices: DrugPrice[];
}

interface PaginatedDrugs {
    data: Drug[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    links: {
        url: string | null;
        label: string;
        active: boolean;
    }[];
}

interface Props {
    drugs: PaginatedDrugs;
    categories: string[];
    filters: {
        search: string;
        category: string;
        pregnancy_safe: string;
    };
}

const props = defineProps<Props>();

const searchQuery = ref(props.filters.search);
const selectedCategory = ref(props.filters.category);
const pregnancySafe = ref(props.filters.pregnancy_safe);

const getCategoryIcon = (category: string): string => {
    const icons: Record<string, string> = {
        'Analgesik': 'mdi:pill',
        'Antibiotik': 'mdi:bacteria',
        'Antihistamin': 'mdi:allergy',
        'Antasida': 'mdi:stomach',
        'Vitamin': 'mdi:fruit-cherries',
        'Obat Batuk': 'mdi:lungs',
        'Obat Flu': 'mdi:emoticon-sick-outline',
        'Antiseptik': 'mdi:hand-wash-outline',
    };
    return icons[category] || 'mdi:medical-bag';
};

const getCategoryColor = (category: string): string => {
    const colors: Record<string, string> = {
        'Analgesik': '#F4AFE9',
        'Antibiotik': '#8DD0FC',
        'Antihistamin': '#DDB4F6',
        'Antasida': '#43B3FC',
        'Vitamin': '#FF7CEA',
        'Obat Batuk': '#F4AFE9',
        'Obat Flu': '#8DD0FC',
        'Antiseptik': '#DDB4F6',
    };
    return colors[category] || '#8DD0FC';
};

const formatPrice = (price: number): string => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(price);
};

const getPriceRange = (prices: DrugPrice[]): string => {
    if (prices.length === 0) return 'Harga tidak tersedia';
    
    const minPrice = Math.min(...prices.map(p => p.price_min));
    const maxPrice = Math.max(...prices.map(p => p.price_max));
    
    if (minPrice === maxPrice) {
        return formatPrice(minPrice);
    }
    return `${formatPrice(minPrice)} - ${formatPrice(maxPrice)}`;
};

const truncateText = (text: string, maxLength: number): string => {
    if (text.length <= maxLength) return text;
    return text.substring(0, maxLength) + '...';
};

const applyFilters = useDebounceFn(() => {
    router.get('/drug-catalog', {
        search: searchQuery.value || undefined,
        category: selectedCategory.value || undefined,
        pregnancy_safe: pregnancySafe.value || undefined,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
}, 300);

const clearFilters = () => {
    searchQuery.value = '';
    selectedCategory.value = '';
    pregnancySafe.value = '';
    router.get('/drug-catalog', {}, {
        preserveState: true,
        preserveScroll: true,
    });
};

watch([searchQuery], () => {
    applyFilters();
});

const handleFilterChange = () => {
    applyFilters();
};

const goToPage = (url: string | null) => {
    if (url) {
        router.get(url, {}, {
            preserveState: true,
            preserveScroll: true,
        });
    }
};

const hasActiveFilters = computed(() => {
    return searchQuery.value || selectedCategory.value || pregnancySafe.value;
});

useScrollAnimation();
</script>

<template>
    <Head title="Katalog Obat - DocDot" />

    <div class="min-h-screen bg-[#FAFAFA] pt-16 sm:pt-20 lg:pt-22 font-[Poppins]">
        <Navbar />

        <div class="px-4 py-6 sm:px-6 sm:py-8 lg:px-12">
            <div class="mx-auto max-w-7xl">
                <!-- Breadcrumb -->
                <nav class="mb-4 flex items-center gap-2 text-[12px] sm:mb-6 sm:text-[14px]">
                    <Link href="/" class="text-[#1b1b18]/60 hover:text-[#1b1b18]">Beranda</Link>
                    <Icon icon="mdi:chevron-right" class="h-3 w-3 text-[#1b1b18]/40 sm:h-4 sm:w-4" />
                    <span class="text-[#1b1b18]">Katalog Obat</span>
                </nav>

                <!-- Header -->
                <div class="scroll-animate mb-6 sm:mb-8">
                    <h1 class="mb-2 text-[24px] font-bold text-[#1b1b18] sm:text-[28px] lg:text-[36px]">
                        Katalog Obat
                    </h1>
                    <p class="text-[13px] text-[#1b1b18]/70 sm:text-[14px] lg:text-[16px]">
                        Temukan informasi lengkap tentang obat-obatan umum untuk kebutuhan kesehatan Anda
                    </p>
                </div>

                <!-- Search & Filters -->
                <div class="scroll-animate mb-6 rounded-xl bg-white p-3 sm:mb-8 sm:rounded-2xl sm:p-4 lg:p-6">
                    <div class="flex flex-col gap-3 sm:gap-4 lg:flex-row lg:items-center">
                        <!-- Search Input -->
                        <div class="relative flex-1">
                            <Icon icon="mdi:magnify" class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-[#1b1b18]/40 sm:left-4 sm:h-5 sm:w-5" />
                            <input
                                v-model="searchQuery"
                                type="text"
                                placeholder="Cari nama obat, kategori..."
                                class="w-full rounded-lg border border-[#1b1b18]/10 bg-[#F8F8F8] py-2.5 pl-10 pr-3 text-[13px] outline-none focus:border-[#8DD0FC] focus:ring-0 sm:rounded-xl sm:py-3 sm:pl-12 sm:pr-4 sm:text-[14px]"
                            />
                        </div>

                        <!-- Category Filter -->
                        <div class="flex flex-wrap gap-2 sm:gap-3">
                            <select
                                v-model="selectedCategory"
                                @change="handleFilterChange"
                                class="flex-1 rounded-lg border border-[#1b1b18]/10 bg-[#F8F8F8] px-3 py-2.5 text-[13px] outline-none focus:border-[#8DD0FC] focus:ring-0 sm:flex-none sm:rounded-xl sm:px-4 sm:py-3 sm:text-[14px]"
                            >
                                <option value="">Semua Kategori</option>
                                <option v-for="category in categories" :key="category" :value="category">
                                    {{ category }}
                                </option>
                            </select>

                            <!-- Pregnancy Safe Filter -->
                            <select
                                v-model="pregnancySafe"
                                @change="handleFilterChange"
                                class="flex-1 rounded-lg border border-[#1b1b18]/10 bg-[#F8F8F8] px-3 py-2.5 text-[13px] outline-none focus:border-[#8DD0FC] focus:ring-0 sm:flex-none sm:rounded-xl sm:px-4 sm:py-3 sm:text-[14px]"
                            >
                                <option value="">Status Kehamilan</option>
                                <option value="1">Aman untuk Ibu Hamil</option>
                                <option value="0">Tidak Aman</option>
                            </select>

                            <!-- Clear Filters -->
                            <button
                                v-if="hasActiveFilters"
                                @click="clearFilters"
                                class="flex items-center gap-1.5 rounded-lg border border-[#1b1b18]/10 px-3 py-2.5 text-[13px] text-[#1b1b18]/70 transition-colors hover:bg-[#F8F8F8] sm:gap-2 sm:rounded-xl sm:px-4 sm:py-3 sm:text-[14px]"
                            >
                                <Icon icon="mdi:filter-remove" class="h-4 w-4" />
                                <span class="hidden sm:inline">Clear</span>
                            </button>
                        </div>
                    </div>

                    <!-- Active Filters Tags -->
                    <div v-if="hasActiveFilters" class="mt-4 flex flex-wrap gap-2">
                        <span
                            v-if="searchQuery"
                            class="inline-flex items-center gap-1 rounded-full bg-[#8DD0FC]/10 px-3 py-1 text-[12px] text-[#1b1b18]"
                        >
                            <Icon icon="mdi:magnify" class="h-3 w-3" />
                            "{{ searchQuery }}"
                            <button @click="searchQuery = ''; applyFilters()" class="ml-1 hover:text-red-500">
                                <Icon icon="mdi:close" class="h-3 w-3" />
                            </button>
                        </span>
                        <span
                            v-if="selectedCategory"
                            class="inline-flex items-center gap-1 rounded-full bg-[#F4AFE9]/10 px-3 py-1 text-[12px] text-[#1b1b18]"
                        >
                            <Icon icon="mdi:tag" class="h-3 w-3" />
                            {{ selectedCategory }}
                            <button @click="selectedCategory = ''; handleFilterChange()" class="ml-1 hover:text-red-500">
                                <Icon icon="mdi:close" class="h-3 w-3" />
                            </button>
                        </span>
                        <span
                            v-if="pregnancySafe"
                            class="inline-flex items-center gap-1 rounded-full bg-[#DDB4F6]/10 px-3 py-1 text-[12px] text-[#1b1b18]"
                        >
                            <Icon icon="mdi:mother-heart" class="h-3 w-3" />
                            {{ pregnancySafe === '1' ? 'Aman untuk Ibu Hamil' : 'Tidak Aman untuk Ibu Hamil' }}
                            <button @click="pregnancySafe = ''; handleFilterChange()" class="ml-1 hover:text-red-500">
                                <Icon icon="mdi:close" class="h-3 w-3" />
                            </button>
                        </span>
                    </div>
                </div>

                <!-- Results Info -->
                <div class="mb-4 flex items-center justify-between sm:mb-6">
                    <p class="text-[12px] text-[#1b1b18]/70 sm:text-[14px]">
                        Menampilkan <span class="font-medium text-[#1b1b18]">{{ drugs.data.length }}</span> dari 
                        <span class="font-medium text-[#1b1b18]">{{ drugs.total }}</span> obat
                    </p>
                </div>

                <!-- Drug Grid -->
                <div v-if="drugs.data.length > 0" class="scroll-animate mb-6 grid gap-4 sm:mb-8 sm:grid-cols-2 sm:gap-6 lg:grid-cols-3 xl:grid-cols-4">
                    <Link
                        v-for="drug in drugs.data"
                        :key="drug.id"
                        :href="`/drug-catalog/${drug.id}`"
                        class="group overflow-hidden rounded-xl bg-white transition-all hover:-translate-y-1 hover:shadow-lg sm:rounded-2xl"
                    >
                        <!-- Card Header with Image -->
                        <div 
                            class="relative flex h-28 items-center justify-center overflow-hidden sm:h-36"
                            :style="{ background: drug.image ? '#f8f8f8' : `linear-gradient(135deg, ${getCategoryColor(drug.category)}40, ${getCategoryColor(drug.category)}20)` }"
                        >
                            <img 
                                v-if="drug.image"
                                :src="`/storage/${drug.image}`"
                                :alt="drug.name"
                                class="h-full w-full object-contain p-2 transition-transform group-hover:scale-105"
                            />
                            <Icon 
                                v-else
                                :icon="getCategoryIcon(drug.category)" 
                                class="h-12 w-12 transition-transform group-hover:scale-110 sm:h-16 sm:w-16"
                                :style="{ color: getCategoryColor(drug.category) }"
                            />
                        </div>

                        <!-- Card Body -->
                        <div class="p-3 sm:p-4">
                            <!-- Category Badge -->
                            <div class="mb-2 flex items-center justify-between">
                                <span 
                                    class="rounded-full px-2 py-0.5 text-[11px] font-medium"
                                    :style="{ 
                                        backgroundColor: `${getCategoryColor(drug.category)}20`,
                                        color: getCategoryColor(drug.category)
                                    }"
                                >
                                    {{ drug.category }}
                                </span>
                                <span
                                    v-if="drug.pregnancy_safe"
                                    class="flex items-center gap-0.5 rounded-full bg-green-100 px-2 py-0.5 text-[10px] font-medium text-green-600"
                                >
                                    <Icon icon="mdi:mother-heart" class="h-3 w-3" />
                                    Aman
                                </span>
                            </div>

                            <!-- Drug Name -->
                            <h3 class="mb-2 text-[14px] font-semibold text-[#1b1b18] group-hover:text-[#8DD0FC] sm:text-[16px]">
                                {{ drug.name }}
                            </h3>

                            <!-- Description -->
                            <p class="mb-2 line-clamp-2 text-[12px] text-[#1b1b18]/60 sm:mb-3 sm:text-[13px]">
                                {{ truncateText(drug.description, 80) }}
                            </p>

                            <!-- Price Range -->
                            <div class="flex items-center gap-1.5 sm:gap-2">
                                <Icon icon="mdi:tag-outline" class="h-3.5 w-3.5 text-[#8DD0FC] sm:h-4 sm:w-4" />
                                <span class="text-[12px] font-medium text-[#1b1b18] sm:text-[13px]">
                                    {{ getPriceRange(drug.prices) }}
                                </span>
                            </div>
                        </div>
                    </Link>
                </div>

                <!-- Empty State -->
                <div v-else class="py-12 text-center sm:py-16">
                    <Icon icon="mdi:pill-off" class="mx-auto h-16 w-16 text-[#1b1b18]/20 sm:h-20 sm:w-20" />
                    <h3 class="mt-3 text-[16px] font-semibold text-[#1b1b18] sm:mt-4 sm:text-[18px]">Obat tidak ditemukan</h3>
                    <p class="mt-2 text-[13px] text-[#1b1b18]/60 sm:text-[14px]">
                        Coba ubah filter atau kata kunci pencarian Anda
                    </p>
                    <button
                        @click="clearFilters"
                        class="mt-4 rounded-lg bg-gradient-to-r from-[#F4AFE9] to-[#8DD0FC] px-5 py-2 text-[13px] font-medium text-white transition-all hover:shadow-lg sm:rounded-xl sm:px-6 sm:text-[14px]"
                    >
                        Reset Filter
                    </button>
                </div>

                <!-- Pagination -->
                <div v-if="drugs.last_page > 1" class="flex flex-wrap items-center justify-center gap-1.5 sm:gap-2">
                    <button
                        v-for="link in drugs.links"
                        :key="link.label"
                        @click="goToPage(link.url)"
                        :disabled="!link.url"
                        :class="[
                            'flex h-8 min-w-[32px] items-center justify-center rounded-lg px-2 text-[12px] transition-all sm:h-10 sm:min-w-[40px] sm:rounded-xl sm:px-3 sm:text-[14px]',
                            link.active 
                                ? 'bg-gradient-to-r from-[#F4AFE9] to-[#8DD0FC] font-medium text-white' 
                                : link.url 
                                    ? 'bg-white text-[#1b1b18] hover:bg-[#F8F8F8]' 
                                    : 'bg-white/50 text-[#1b1b18]/30 cursor-not-allowed'
                        ]"
                        v-html="link.label"
                    />
                </div>
            </div>
        </div>

        <Footer />
    </div>
</template>
