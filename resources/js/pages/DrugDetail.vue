<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Icon } from '@iconify/vue';
import { ref } from 'vue';
import Navbar from '@/components/Navbar.vue';
import Footer from '@/components/Footer.vue';

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

interface Props {
    drug: Drug;
    relatedDrugs: Drug[];
}

const props = defineProps<Props>();

const activeTab = ref<'info' | 'dosage' | 'side-effects' | 'warnings'>('info');

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

const tabs = [
    { id: 'info', label: 'Informasi', icon: 'mdi:information-outline' },
    { id: 'dosage', label: 'Dosis', icon: 'mdi:clock-outline' },
    { id: 'side-effects', label: 'Efek Samping', icon: 'mdi:alert-circle-outline' },
    { id: 'warnings', label: 'Peringatan', icon: 'mdi:alert-outline' },
];
</script>

<template>
    <Head :title="`${drug.name} - Katalog Obat - DocDot`" />

    <div class="min-h-screen pt-16 sm:pt-20 lg:pt-22 font-[Poppins]" style="background: linear-gradient(to left, rgba(141, 208, 252, 0.6) 0%, rgba(221, 180, 246, 0.6) 100%)">
        <Navbar />

        <div class="px-4 py-6 sm:px-6 sm:py-8 lg:px-12">
            <div class="mx-auto max-w-5xl">
                <!-- Breadcrumb -->
                <nav class="mb-4 flex flex-wrap items-center gap-1 text-[12px] sm:mb-6 sm:gap-2 sm:text-[14px]">
                    <Link href="/" class="text-[#1b1b18]/60 hover:text-[#1b1b18]">Beranda</Link>
                    <Icon icon="mdi:chevron-right" class="h-3 w-3 text-[#1b1b18]/40 sm:h-4 sm:w-4" />
                    <Link href="/drug-catalog" class="text-[#1b1b18]/60 hover:text-[#1b1b18]">Katalog Obat</Link>
                    <Icon icon="mdi:chevron-right" class="h-3 w-3 text-[#1b1b18]/40 sm:h-4 sm:w-4" />
                    <span class="truncate text-[#1b1b18]">{{ drug.name }}</span>
                </nav>

                <!-- Back Button -->
                <Link 
                    href="/drug-catalog"
                    class="mb-4 inline-flex items-center gap-2 text-[13px] text-[#1b1b18]/70 transition-colors hover:text-[#1b1b18] sm:mb-6 sm:text-[14px]"
                >
                    <Icon icon="mdi:arrow-left" class="h-4 w-4" />
                    Kembali ke Katalog
                </Link>

                <div class="grid gap-4 sm:gap-6 lg:grid-cols-3">
                    <!-- Main Content -->
                    <div class="space-y-4 sm:space-y-6 lg:col-span-2">
                        <!-- Drug Header Card -->
                        <div class="overflow-hidden rounded-xl bg-white sm:rounded-2xl">
                            <!-- Header Banner with Image -->
                            <div 
                                class="relative flex h-40 items-center justify-center overflow-hidden bg-[#f8f8f8] sm:h-56 lg:h-64"
                                :style="{ background: drug.image ? '#f8f8f8' : `linear-gradient(135deg, ${getCategoryColor(drug.category)}60, ${getCategoryColor(drug.category)}30)` }"
                            >
                                <img 
                                    v-if="drug.image"
                                    :src="`/storage/${drug.image}`"
                                    :alt="drug.name"
                                    class="h-full w-full object-contain p-4"
                                />
                                <Icon 
                                    v-else
                                    :icon="getCategoryIcon(drug.category)" 
                                    class="h-16 w-16 sm:h-24 sm:w-24"
                                    :style="{ color: getCategoryColor(drug.category) }"
                                />
                            </div>

                            <div class="p-4 sm:p-6">
                                <!-- Category & Safety Badge -->
                                <div class="mb-3 flex flex-wrap items-center gap-2 sm:gap-3">
                                    <span 
                                        class="rounded-full px-2 py-0.5 text-[10px] font-medium sm:px-3 sm:py-1 sm:text-[12px]"
                                        :style="{ 
                                            backgroundColor: `${getCategoryColor(drug.category)}20`,
                                            color: getCategoryColor(drug.category)
                                        }"
                                    >
                                        {{ drug.category }}
                                    </span>
                                    <span
                                        v-if="drug.pregnancy_safe"
                                        class="flex items-center gap-1 rounded-full bg-green-100 px-2 py-0.5 text-[10px] font-medium text-green-600 sm:px-3 sm:py-1 sm:text-[12px]"
                                    >
                                        <Icon icon="mdi:mother-heart" class="h-3 w-3 sm:h-4 sm:w-4" />
                                        <span class="hidden sm:inline">Aman untuk Ibu Hamil</span>
                                        <span class="sm:hidden">Aman Bumil</span>
                                    </span>
                                    <span
                                        v-else
                                        class="flex items-center gap-1 rounded-full bg-red-100 px-2 py-0.5 text-[10px] font-medium text-red-600 sm:px-3 sm:py-1 sm:text-[12px]"
                                    >
                                        <Icon icon="mdi:alert" class="h-3 w-3 sm:h-4 sm:w-4" />
                                        <span class="hidden sm:inline">Tidak Aman untuk Ibu Hamil</span>
                                        <span class="sm:hidden">Tidak Aman Bumil</span>
                                    </span>
                                </div>

                                <!-- Drug Name -->
                                <h1 class="mb-2 text-[22px] font-bold text-[#1b1b18] sm:text-[28px] lg:text-[32px]">
                                    {{ drug.name }}
                                </h1>

                                <!-- Price Range -->
                                <div class="flex items-center gap-2 text-[15px] sm:text-[18px]">
                                    <Icon icon="mdi:tag-outline" class="h-4 w-4 text-[#8DD0FC] sm:h-5 sm:w-5" />
                                    <span class="font-semibold text-[#1b1b18]">
                                        {{ getPriceRange(drug.prices) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Tabs -->
                        <div class="rounded-xl bg-white p-3 sm:rounded-2xl sm:p-4 lg:p-6">
                            <!-- Tab Buttons -->
                            <div class="mb-4 flex gap-1 overflow-x-auto pb-1 sm:mb-6 sm:gap-2 sm:pb-0">
                                <button
                                    v-for="tab in tabs"
                                    :key="tab.id"
                                    @click="activeTab = tab.id as typeof activeTab"
                                    :class="[
                                        'flex items-center gap-1 whitespace-nowrap rounded-lg px-2.5 py-1.5 text-[12px] font-medium transition-all sm:gap-2 sm:rounded-xl sm:px-4 sm:py-2 sm:text-[14px]',
                                        activeTab === tab.id
                                            ? 'bg-gradient-to-r from-[#F4AFE9] to-[#8DD0FC] text-white'
                                            : 'bg-[#F8F8F8] text-[#1b1b18]/70 hover:bg-[#F4AFE9]/10'
                                    ]"
                                >
                                    <Icon :icon="tab.icon" class="h-3.5 w-3.5 sm:h-4 sm:w-4" />
                                    <span class="hidden sm:inline">{{ tab.label }}</span>
                                </button>
                            </div>

                            <!-- Tab Content -->
                            <div class="min-h-[200px]">
                                <!-- Info Tab -->
                                <div v-if="activeTab === 'info'">
                                    <h3 class="mb-3 text-[18px] font-semibold text-[#1b1b18]">Deskripsi Obat</h3>
                                    <p class="whitespace-pre-line text-[14px] leading-relaxed text-[#1b1b18]/70">
                                        {{ drug.description }}
                                    </p>
                                </div>

                                <!-- Dosage Tab -->
                                <div v-if="activeTab === 'dosage'">
                                    <h3 class="mb-3 text-[18px] font-semibold text-[#1b1b18]">Informasi Dosis</h3>
                                    <div v-if="drug.dosage_info" class="whitespace-pre-line text-[14px] leading-relaxed text-[#1b1b18]/70">
                                        {{ drug.dosage_info }}
                                    </div>
                                    <div v-else class="flex flex-col items-center py-8 text-center">
                                        <Icon icon="mdi:file-document-outline" class="h-12 w-12 text-[#1b1b18]/20" />
                                        <p class="mt-2 text-[14px] text-[#1b1b18]/60">
                                            Informasi dosis belum tersedia
                                        </p>
                                    </div>
                                </div>

                                <!-- Side Effects Tab -->
                                <div v-if="activeTab === 'side-effects'">
                                    <h3 class="mb-3 text-[18px] font-semibold text-[#1b1b18]">Efek Samping</h3>
                                    <div v-if="drug.side_effects" class="whitespace-pre-line text-[14px] leading-relaxed text-[#1b1b18]/70">
                                        {{ drug.side_effects }}
                                    </div>
                                    <div v-else class="flex flex-col items-center py-8 text-center">
                                        <Icon icon="mdi:check-circle-outline" class="h-12 w-12 text-green-400" />
                                        <p class="mt-2 text-[14px] text-[#1b1b18]/60">
                                            Tidak ada efek samping yang tercatat
                                        </p>
                                    </div>
                                </div>

                                <!-- Warnings Tab -->
                                <div v-if="activeTab === 'warnings'">
                                    <h3 class="mb-3 text-[18px] font-semibold text-[#1b1b18]">Peringatan</h3>
                                    <div v-if="drug.warnings" class="rounded-xl bg-yellow-50 p-4">
                                        <div class="flex items-start gap-3">
                                            <Icon icon="mdi:alert" class="mt-0.5 h-5 w-5 flex-shrink-0 text-yellow-600" />
                                            <div class="whitespace-pre-line text-[14px] leading-relaxed text-yellow-800">
                                                {{ drug.warnings }}
                                            </div>
                                        </div>
                                    </div>
                                    <div v-else class="flex flex-col items-center py-8 text-center">
                                        <Icon icon="mdi:shield-check-outline" class="h-12 w-12 text-green-400" />
                                        <p class="mt-2 text-[14px] text-[#1b1b18]/60">
                                            Tidak ada peringatan khusus
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-4 sm:space-y-6">
                        <!-- Price Comparison Card -->
                        <div class="rounded-xl bg-white p-4 sm:rounded-2xl sm:p-6">
                            <h3 class="mb-3 flex items-center gap-2 text-[16px] font-semibold text-[#1b1b18] sm:mb-4 sm:text-[18px]">
                                <Icon icon="mdi:store" class="h-4 w-4 text-[#8DD0FC] sm:h-5 sm:w-5" />
                                Harga di Apotek
                            </h3>

                            <div v-if="drug.prices.length > 0" class="space-y-2 sm:space-y-3">
                                <div
                                    v-for="price in drug.prices"
                                    :key="price.id"
                                    class="rounded-lg bg-[#F8F8F8] p-3 sm:rounded-xl sm:p-4"
                                >
                                    <p class="mb-1 text-[13px] font-medium text-[#1b1b18] sm:text-[14px]">
                                        {{ price.pharmacy_name }}
                                    </p>
                                    <p class="text-[14px] font-semibold text-[#8DD0FC] sm:text-[16px]">
                                        {{ formatPrice(price.price_min) }}
                                        <span v-if="price.price_min !== price.price_max">
                                            - {{ formatPrice(price.price_max) }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                            <div v-else class="py-4 text-center sm:py-6">
                                <Icon icon="mdi:store-off" class="mx-auto h-8 w-8 text-[#1b1b18]/20 sm:h-10 sm:w-10" />
                                <p class="mt-2 text-[12px] text-[#1b1b18]/60 sm:text-[13px]">
                                    Harga belum tersedia
                                </p>
                            </div>
                        </div>

                        <!-- Disclaimer -->
                        <div class="rounded-xl bg-gradient-to-br from-[#F4AFE9]/20 to-[#8DD0FC]/20 p-3 sm:rounded-2xl sm:p-4">
                            <div class="flex items-start gap-2 sm:gap-3">
                                <Icon icon="mdi:information" class="mt-0.5 h-4 w-4 flex-shrink-0 text-[#8DD0FC] sm:h-5 sm:w-5" />
                                <div class="text-[11px] text-[#1b1b18]/70 sm:text-[12px]">
                                    <p class="mb-1 font-medium text-[#1b1b18]">Disclaimer</p>
                                    <p>
                                        Informasi obat ini hanya untuk referensi. Selalu konsultasikan dengan dokter atau apoteker sebelum menggunakan obat.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Consult CTA -->
                        <Link
                            href="/consultation"
                            class="flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#F4AFE9] to-[#8DD0FC] p-3 text-[13px] font-medium text-white transition-all hover:shadow-lg sm:rounded-2xl sm:p-4 sm:text-[14px]"
                        >
                            <Icon icon="mdi:robot" class="h-4 w-4 sm:h-5 sm:w-5" />
                            Tanyakan informasi lebih dengan AI
                        </Link>
                    </div>
                </div>

                <!-- Related Drugs -->
                <div v-if="relatedDrugs.length > 0" class="mt-6 sm:mt-8">
                    <h2 class="mb-3 text-[18px] font-semibold text-[#1b1b18] sm:mb-4 sm:text-[20px]">Obat Terkait</h2>
                    <div class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4">
                        <Link
                            v-for="related in relatedDrugs"
                            :key="related.id"
                            :href="`/drug-catalog/${related.id}`"
                            class="group flex items-center gap-3 rounded-xl bg-white p-4 transition-all hover:shadow-md"
                        >
                            <div 
                                class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl"
                                :style="{ backgroundColor: `${getCategoryColor(related.category)}20` }"
                            >
                                <Icon 
                                    :icon="getCategoryIcon(related.category)" 
                                    class="h-6 w-6"
                                    :style="{ color: getCategoryColor(related.category) }"
                                />
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-[14px] font-medium text-[#1b1b18] group-hover:text-[#8DD0FC]">
                                    {{ related.name }}
                                </p>
                                <p class="text-[12px] text-[#1b1b18]/60">
                                    {{ related.category }}
                                </p>
                            </div>
                            <Icon icon="mdi:chevron-right" class="h-5 w-5 text-[#1b1b18]/30 group-hover:text-[#8DD0FC]" />
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <Footer />
    </div>
</template>
