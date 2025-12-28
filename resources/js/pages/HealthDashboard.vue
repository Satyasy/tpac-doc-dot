<script setup lang="ts">
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { Icon } from '@iconify/vue';
import { ref, computed } from 'vue';
import Navbar from '@/components/Navbar.vue';
import Footer from '@/components/Footer.vue';
import { useScrollAnimation } from '@/composables/useScrollAnimation';

useScrollAnimation();

interface PhysicalLog {
    id: number;
    weight_kg: number | null;
    blood_pressure: string | null;
    activity_minutes: number | null;
    logged_at: string;
}

interface MentalLog {
    id: number;
    mood: number;
    stress_level: number;
    sleep_hours: number | null;
    note: string | null;
    logged_at: string;
}

interface HealthInsight {
    id: number;
    type: string;
    summary: string;
    risk_level: string | null;
    created_at: string;
}

interface UserProfile {
    height: number | null;
    weight: number | null;
    bmi: number | null;
    bmi_category: string | null;
}

interface Props {
    profile: UserProfile | null;
    physicalLogs: PhysicalLog[];
    mentalLogs: MentalLog[];
    insights: HealthInsight[];
    stats: {
        avg_mood: number;
        avg_stress: number;
        avg_sleep: number;
        total_activity_minutes: number;
        logs_this_week: number;
    };
}

const props = defineProps<Props>();

const activeTab = ref<'physical' | 'mental'>('physical');
const showPhysicalModal = ref(false);
const showMentalModal = ref(false);

const physicalForm = useForm({
    weight_kg: '',
    blood_pressure: '',
    activity_minutes: '',
    logged_at: new Date().toISOString().split('T')[0],
});

const mentalForm = useForm({
    mood: 3,
    stress_level: 3,
    sleep_hours: '',
    note: '',
    logged_at: new Date().toISOString().split('T')[0],
});

const submitPhysicalLog = () => {
    physicalForm.post('/health-dashboard/physical', {
        preserveScroll: true,
        onSuccess: () => {
            showPhysicalModal.value = false;
            physicalForm.reset();
        },
    });
};

const submitMentalLog = () => {
    mentalForm.post('/health-dashboard/mental', {
        preserveScroll: true,
        onSuccess: () => {
            showMentalModal.value = false;
            mentalForm.reset();
        },
    });
};

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
};

const getMoodEmoji = (mood: number) => {
    const emojis = ['😢', '😟', '😐', '🙂', '😄'];
    return emojis[mood - 1] || '😐';
};

const getMoodLabel = (mood: number) => {
    const labels = ['Sangat Buruk', 'Buruk', 'Biasa', 'Baik', 'Sangat Baik'];
    return labels[mood - 1] || 'Biasa';
};

const getStressColor = (level: number) => {
    if (level <= 2) return 'bg-green-500';
    if (level <= 3) return 'bg-yellow-500';
    return 'bg-red-500';
};

const getBmiColor = (category: string | null) => {
    switch (category) {
        case 'Underweight': return 'text-blue-500 bg-blue-50';
        case 'Normal': return 'text-green-500 bg-green-50';
        case 'Overweight': return 'text-yellow-500 bg-yellow-50';
        case 'Obese': return 'text-red-500 bg-red-50';
        default: return 'text-gray-500 bg-gray-50';
    }
};

const getRiskColor = (risk: string | null) => {
    switch (risk?.toLowerCase()) {
        case 'low': return 'bg-green-100 text-green-700';
        case 'medium': return 'bg-yellow-100 text-yellow-700';
        case 'high': return 'bg-red-100 text-red-700';
        default: return 'bg-gray-100 text-gray-700';
    }
};
</script>

<template>
    <Head title="Health Dashboard - DocDot" />

    <div class="min-h-screen pt-16 sm:pt-20 lg:pt-22 font-[Poppins]" style="background: linear-gradient(to left, rgba(141, 208, 252, 0.6) 0%, rgba(221, 180, 246, 0.6) 100%)">
        <Navbar />

        <div class="px-4 py-6 sm:px-6 sm:py-8 lg:px-12">
            <div class="mx-auto max-w-7xl">
                <!-- Breadcrumb -->
                <nav class="mb-4 flex items-center gap-2 text-[13px] sm:mb-6 sm:text-[14px]">
                    <Link href="/" class="text-[#1b1b18]/60 hover:text-[#1b1b18]">Beranda</Link>
                    <Icon icon="mdi:chevron-right" class="h-4 w-4 text-[#1b1b18]/40" />
                    <span class="text-[#1b1b18]">Health Dashboard</span>
                </nav>

                <!-- Header -->
                <div class="scroll-animate mb-6 sm:mb-8">
                    <h1 class="mb-2 text-[24px] font-bold text-[#1b1b18] sm:text-[28px] lg:text-[36px]">
                        Health <span class="bg-gradient-to-r from-[#BF55FF] to-[#43B3FC] bg-clip-text text-transparent">Dashboard</span>
                    </h1>
                    <p class="text-[13px] text-[#1b1b18]/70 sm:text-[14px] lg:text-[16px]">
                        Pantau dan catat kondisi kesehatan Anda secara berkala
                    </p>
                </div>

                <!-- Stats Cards -->
                <div class="scroll-animate mb-6 grid grid-cols-2 gap-3 sm:mb-8 sm:gap-4 lg:grid-cols-4">
                    <!-- BMI Card -->
                    <div class="rounded-xl bg-white p-3 sm:rounded-2xl sm:p-5">
                        <div class="flex items-start justify-between sm:items-center">
                            <div class="min-w-0 flex-1">
                                <p class="text-[11px] text-[#1b1b18]/60 sm:text-[13px]">BMI Anda</p>
                                <p class="mt-0.5 text-[22px] font-bold text-[#1b1b18] sm:mt-1 sm:text-[28px]">
                                    {{ profile?.bmi ?? '-' }}
                                </p>
                                <span 
                                    v-if="profile?.bmi_category"
                                    class="mt-1 inline-block rounded-full px-2 py-0.5 text-[9px] font-medium sm:mt-2 sm:px-3 sm:py-1 sm:text-[11px]"
                                    :class="getBmiColor(profile.bmi_category)"
                                >
                                    {{ profile.bmi_category }}
                                </span>
                            </div>
                            <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full bg-gradient-to-r from-[#F4AFE9] to-[#8DD0FC] sm:h-12 sm:w-12">
                                <Icon icon="mdi:scale-bathroom" class="h-4 w-4 text-white sm:h-6 sm:w-6" />
                            </div>
                        </div>
                    </div>

                    <!-- Average Mood -->
                    <div class="rounded-xl bg-white p-3 sm:rounded-2xl sm:p-5">
                        <div class="flex items-start justify-between sm:items-center">
                            <div class="min-w-0 flex-1">
                                <p class="text-[11px] text-[#1b1b18]/60 sm:text-[13px]">Rata-rata Mood</p>
                                <p class="mt-0.5 text-[22px] font-bold text-[#1b1b18] sm:mt-1 sm:text-[28px]">
                                    {{ stats.avg_mood.toFixed(1) }}
                                </p>
                                <p class="mt-0.5 text-[10px] text-[#1b1b18]/60 sm:mt-1 sm:text-[13px]">
                                    <span class="sm:hidden">{{ getMoodEmoji(Math.round(stats.avg_mood)) }}</span>
                                    <span class="hidden sm:inline">{{ getMoodEmoji(Math.round(stats.avg_mood)) }} {{ getMoodLabel(Math.round(stats.avg_mood)) }}</span>
                                </p>
                            </div>
                            <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full bg-gradient-to-r from-[#8DD0FC] to-[#43B3FC] sm:h-12 sm:w-12">
                                <Icon icon="mdi:emoticon-outline" class="h-4 w-4 text-white sm:h-6 sm:w-6" />
                            </div>
                        </div>
                    </div>

                    <!-- Average Sleep -->
                    <div class="rounded-xl bg-white p-3 sm:rounded-2xl sm:p-5">
                        <div class="flex items-start justify-between sm:items-center">
                            <div class="min-w-0 flex-1">
                                <p class="text-[11px] text-[#1b1b18]/60 sm:text-[13px]">Rata-rata Tidur</p>
                                <p class="mt-0.5 text-[22px] font-bold text-[#1b1b18] sm:mt-1 sm:text-[28px]">
                                    {{ stats.avg_sleep.toFixed(1) }}
                                </p>
                                <p class="mt-0.5 text-[10px] text-[#1b1b18]/60 sm:mt-1 sm:text-[13px]">jam/malam</p>
                            </div>
                            <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full bg-gradient-to-r from-[#DDB4F6] to-[#BF55FF] sm:h-12 sm:w-12">
                                <Icon icon="mdi:sleep" class="h-4 w-4 text-white sm:h-6 sm:w-6" />
                            </div>
                        </div>
                    </div>

                    <!-- Weekly Activity -->
                    <div class="rounded-xl bg-white p-3 sm:rounded-2xl sm:p-5">
                        <div class="flex items-start justify-between sm:items-center">
                            <div class="min-w-0 flex-1">
                                <p class="text-[11px] text-[#1b1b18]/60 sm:text-[13px]">Aktivitas Minggu Ini</p>
                                <p class="mt-0.5 text-[22px] font-bold text-[#1b1b18] sm:mt-1 sm:text-[28px]">
                                    {{ stats.total_activity_minutes }}
                                </p>
                                <p class="mt-0.5 text-[10px] text-[#1b1b18]/60 sm:mt-1 sm:text-[13px]">menit</p>
                            </div>
                            <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full bg-gradient-to-r from-[#43B3FC] to-[#8DD0FC] sm:h-12 sm:w-12">
                                <Icon icon="mdi:run" class="h-4 w-4 text-white sm:h-6 sm:w-6" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="grid gap-6 sm:gap-8 lg:grid-cols-3">
                    <!-- Left Column - Logs -->
                    <div class="lg:col-span-2">
                        <!-- Tab Buttons -->
                        <div class="scroll-animate mb-4 flex gap-2 sm:mb-6">
                            <button
                                @click="activeTab = 'physical'"
                                class="flex flex-1 items-center justify-center gap-1.5 rounded-full px-4 py-2 text-[13px] font-medium transition-all sm:flex-none sm:gap-2 sm:px-5 sm:py-2.5 sm:text-[14px]"
                                :class="activeTab === 'physical' ? 'bg-gradient-to-r from-[#F4AFE9] to-[#8DD0FC] text-white' : 'bg-white text-[#1b1b18]'"
                            >
                                <Icon icon="mdi:heart-pulse" class="h-4 w-4 sm:h-5 sm:w-5" />
                                Fisik
                            </button>
                            <button
                                @click="activeTab = 'mental'"
                                class="flex flex-1 items-center justify-center gap-1.5 rounded-full px-4 py-2 text-[13px] font-medium transition-all sm:flex-none sm:gap-2 sm:px-5 sm:py-2.5 sm:text-[14px]"
                                :class="activeTab === 'mental' ? 'bg-gradient-to-r from-[#DDB4F6] to-[#BF55FF] text-white' : 'bg-white text-[#1b1b18]'"
                            >
                                <Icon icon="mdi:head-heart-outline" class="h-4 w-4 sm:h-5 sm:w-5" />
                                Mental
                            </button>
                        </div>

                        <!-- Physical Logs -->
                        <div v-if="activeTab === 'physical'" class="rounded-2xl bg-white p-4 sm:p-6">
                            <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <h2 class="text-[16px] font-semibold text-[#1b1b18] sm:text-[18px]">Log Kesehatan Fisik</h2>
                                <button
                                    @click="showPhysicalModal = true"
                                    class="flex w-full items-center justify-center gap-2 rounded-full bg-gradient-to-r from-[#F4AFE9] to-[#8DD0FC] px-4 py-2 text-[13px] font-medium text-white transition-all hover:shadow-lg sm:w-auto"
                                >
                                    <Icon icon="mdi:plus" class="h-4 w-4" />
                                    Tambah Log
                                </button>
                            </div>

                            <div v-if="physicalLogs.length > 0" class="space-y-3">
                                <div
                                    v-for="log in physicalLogs"
                                    :key="log.id"
                                    class="flex flex-col gap-3 rounded-xl bg-[#F8F8F8] p-3 sm:flex-row sm:items-center sm:justify-between sm:p-4"
                                >
                                    <div class="flex items-center gap-3 sm:gap-4">
                                        <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full bg-gradient-to-r from-[#F4AFE9]/30 to-[#8DD0FC]/30 sm:h-10 sm:w-10">
                                            <Icon icon="mdi:calendar" class="h-4 w-4 text-[#1b1b18]/60 sm:h-5 sm:w-5" />
                                        </div>
                                        <div>
                                            <p class="text-[13px] font-medium text-[#1b1b18] sm:text-[14px]">{{ formatDate(log.logged_at) }}</p>
                                            <div class="mt-1 flex flex-wrap gap-2 text-[11px] text-[#1b1b18]/60 sm:gap-3 sm:text-[12px]">
                                                <span v-if="log.weight_kg" class="flex items-center gap-1">
                                                    <Icon icon="mdi:scale" class="h-3 w-3" />
                                                    {{ log.weight_kg }} kg
                                                </span>
                                                <span v-if="log.blood_pressure" class="flex items-center gap-1">
                                                    <Icon icon="mdi:heart-pulse" class="h-3 w-3" />
                                                    {{ log.blood_pressure }}
                                                </span>
                                                <span v-if="log.activity_minutes" class="flex items-center gap-1">
                                                    <Icon icon="mdi:run" class="h-3 w-3" />
                                                    {{ log.activity_minutes }} menit
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div v-else class="py-10 text-center sm:py-12">
                                <Icon icon="mdi:clipboard-text-outline" class="mx-auto h-12 w-12 text-[#1b1b18]/20 sm:h-16 sm:w-16" />
                                <p class="mt-4 text-[13px] text-[#1b1b18]/60 sm:text-[14px]">Belum ada log kesehatan fisik</p>
                                <button
                                    @click="showPhysicalModal = true"
                                    class="mt-4 text-[13px] font-medium text-[#43B3FC] hover:underline sm:text-[14px]"
                                >
                                    Tambah log pertama Anda
                                </button>
                            </div>
                        </div>

                        <!-- Mental Logs -->
                        <div v-if="activeTab === 'mental'" class="rounded-2xl bg-white p-4 sm:p-6">
                            <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <h2 class="text-[16px] font-semibold text-[#1b1b18] sm:text-[18px]">Log Kesehatan Mental</h2>
                                <button
                                    @click="showMentalModal = true"
                                    class="flex w-full items-center justify-center gap-2 rounded-full bg-gradient-to-r from-[#DDB4F6] to-[#BF55FF] px-4 py-2 text-[13px] font-medium text-white transition-all hover:shadow-lg sm:w-auto"
                                >
                                    <Icon icon="mdi:plus" class="h-4 w-4" />
                                    Tambah Log
                                </button>
                            </div>

                            <div v-if="mentalLogs.length > 0" class="space-y-3">
                                <div
                                    v-for="log in mentalLogs"
                                    :key="log.id"
                                    class="rounded-xl bg-[#F8F8F8] p-3 sm:p-4"
                                >
                                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                        <div class="flex items-center gap-3 sm:gap-4">
                                            <div class="text-[28px] sm:text-[32px]">{{ getMoodEmoji(log.mood) }}</div>
                                            <div>
                                                <p class="text-[13px] font-medium text-[#1b1b18] sm:text-[14px]">{{ formatDate(log.logged_at) }}</p>
                                                <p class="text-[11px] text-[#1b1b18]/60 sm:text-[12px]">{{ getMoodLabel(log.mood) }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-4 pl-[44px] sm:gap-3 sm:pl-0">
                                            <div class="text-left sm:text-center">
                                                <p class="text-[10px] text-[#1b1b18]/60 sm:text-[11px]">Stres</p>
                                                <div class="mt-1 flex gap-0.5">
                                                    <div 
                                                        v-for="i in 5" 
                                                        :key="i"
                                                        class="h-2 w-3 rounded-sm sm:w-4"
                                                        :class="i <= log.stress_level ? getStressColor(log.stress_level) : 'bg-gray-200'"
                                                    />
                                                </div>
                                            </div>
                                            <div v-if="log.sleep_hours" class="text-left sm:text-center">
                                                <p class="text-[10px] text-[#1b1b18]/60 sm:text-[11px]">Tidur</p>
                                                <p class="text-[13px] font-medium text-[#1b1b18] sm:text-[14px]">{{ log.sleep_hours }}h</p>
                                            </div>
                                        </div>
                                    </div>
                                    <p v-if="log.note" class="mt-3 rounded-lg bg-white p-2 text-[12px] text-[#1b1b18]/70 sm:p-3 sm:text-[13px]">
                                        {{ log.note }}
                                    </p>
                                </div>
                            </div>

                            <div v-else class="py-10 text-center sm:py-12">
                                <Icon icon="mdi:head-heart-outline" class="mx-auto h-12 w-12 text-[#1b1b18]/20 sm:h-16 sm:w-16" />
                                <p class="mt-4 text-[13px] text-[#1b1b18]/60 sm:text-[14px]">Belum ada log kesehatan mental</p>
                                <button
                                    @click="showMentalModal = true"
                                    class="mt-4 text-[13px] font-medium text-[#BF55FF] hover:underline sm:text-[14px]"
                                >
                                    Catat mood Anda hari ini
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Insights -->
                    <div class="scroll-animate scroll-animate-delay-2">
                        <div class="rounded-2xl bg-white p-4 sm:p-6">
                            <h2 class="mb-4 text-[16px] font-semibold text-[#1b1b18] sm:text-[18px]">
                                <Icon icon="mdi:lightbulb-outline" class="mr-2 inline h-5 w-5 text-[#F4AFE9]" />
                                Health Insights
                            </h2>

                            <div v-if="insights.length > 0" class="space-y-3">
                                <div
                                    v-for="insight in insights"
                                    :key="insight.id"
                                    class="rounded-xl bg-[#F8F8F8] p-3 sm:p-4"
                                >
                                    <div class="flex flex-wrap items-start justify-between gap-2">
                                        <span class="rounded-full bg-[#8DD0FC]/20 px-2 py-0.5 text-[10px] font-medium text-[#1b1b18] sm:text-[11px]">
                                            {{ insight.type }}
                                        </span>
                                        <span 
                                            v-if="insight.risk_level"
                                            class="rounded-full px-2 py-0.5 text-[10px] font-medium sm:text-[11px]"
                                            :class="getRiskColor(insight.risk_level)"
                                        >
                                            {{ insight.risk_level }}
                                        </span>
                                    </div>
                                    <p class="mt-2 text-[12px] text-[#1b1b18]/80 sm:text-[13px]">{{ insight.summary }}</p>
                                </div>
                            </div>

                            <div v-else class="py-6 text-center sm:py-8">
                                <Icon icon="mdi:lightbulb-off-outline" class="mx-auto h-10 w-10 text-[#1b1b18]/20 sm:h-12 sm:w-12" />
                                <p class="mt-3 text-[12px] text-[#1b1b18]/60 sm:text-[13px]">
                                    Belum ada insight. Terus catat kesehatan Anda untuk mendapatkan insight!
                                </p>
                            </div>
                        </div>

                        <!-- Quick Tips -->
                        <div class="mt-4 rounded-2xl bg-gradient-to-br from-[#F4AFE9]/30 to-[#8DD0FC]/30 p-4 sm:mt-6 sm:p-6">
                            <h3 class="text-[14px] font-semibold text-[#1b1b18] sm:text-[16px]">💡 Tips Hari Ini</h3>
                            <ul class="mt-3 space-y-2 text-[12px] text-[#1b1b18]/80 sm:text-[13px]">
                                <li class="flex items-start gap-2">
                                    <Icon icon="mdi:check-circle" class="mt-0.5 h-4 w-4 flex-shrink-0 text-green-500" />
                                    Minum minimal 8 gelas air per hari
                                </li>
                                <li class="flex items-start gap-2">
                                    <Icon icon="mdi:check-circle" class="mt-0.5 h-4 w-4 flex-shrink-0 text-green-500" />
                                    Tidur 7-9 jam setiap malam
                                </li>
                                <li class="flex items-start gap-2">
                                    <Icon icon="mdi:check-circle" class="mt-0.5 h-4 w-4 flex-shrink-0 text-green-500" />
                                    Olahraga minimal 30 menit per hari
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <Footer />
    </div>

    <!-- Physical Log Modal -->
    <Teleport to="body">
        <div v-if="showPhysicalModal" class="fixed inset-0 z-50 flex items-end justify-center bg-black/50 p-0 sm:items-center sm:p-4">
            <div class="max-h-[90vh] w-full overflow-y-auto rounded-t-2xl bg-white p-5 sm:max-w-md sm:rounded-2xl sm:p-6">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-[16px] font-semibold text-[#1b1b18] sm:text-[18px]">Tambah Log Fisik</h3>
                    <button @click="showPhysicalModal = false" class="text-[#1b1b18]/40 hover:text-[#1b1b18]">
                        <Icon icon="mdi:close" class="h-6 w-6" />
                    </button>
                </div>

                <form @submit.prevent="submitPhysicalLog" class="space-y-4">
                    <div>
                        <label class="mb-1 block text-[12px] font-medium text-[#1b1b18] sm:text-[13px]">Tanggal</label>
                        <input
                            v-model="physicalForm.logged_at"
                            type="date"
                            class="w-full rounded-xl border border-[#1b1b18]/10 bg-[#F8F8F8] px-3 py-2.5 text-[13px] outline-none focus:border-[#8DD0FC] sm:px-4 sm:py-3 sm:text-[14px]"
                        />
                    </div>
                    <div>
                        <label class="mb-1 block text-[12px] font-medium text-[#1b1b18] sm:text-[13px]">Berat Badan (kg)</label>
                        <input
                            v-model="physicalForm.weight_kg"
                            type="number"
                            step="0.1"
                            placeholder="Contoh: 65.5"
                            class="w-full rounded-xl border border-[#1b1b18]/10 bg-[#F8F8F8] px-3 py-2.5 text-[13px] outline-none focus:border-[#8DD0FC] sm:px-4 sm:py-3 sm:text-[14px]"
                        />
                    </div>
                    <div>
                        <label class="mb-1 block text-[12px] font-medium text-[#1b1b18] sm:text-[13px]">Tekanan Darah</label>
                        <input
                            v-model="physicalForm.blood_pressure"
                            type="text"
                            placeholder="Contoh: 120/80"
                            class="w-full rounded-xl border border-[#1b1b18]/10 bg-[#F8F8F8] px-3 py-2.5 text-[13px] outline-none focus:border-[#8DD0FC] sm:px-4 sm:py-3 sm:text-[14px]"
                        />
                    </div>
                    <div>
                        <label class="mb-1 block text-[12px] font-medium text-[#1b1b18] sm:text-[13px]">Durasi Aktivitas (menit)</label>
                        <input
                            v-model="physicalForm.activity_minutes"
                            type="number"
                            placeholder="Contoh: 30"
                            class="w-full rounded-xl border border-[#1b1b18]/10 bg-[#F8F8F8] px-3 py-2.5 text-[13px] outline-none focus:border-[#8DD0FC] sm:px-4 sm:py-3 sm:text-[14px]"
                        />
                    </div>
                    <div class="flex gap-3 pt-2">
                        <button
                            type="button"
                            @click="showPhysicalModal = false"
                            class="flex-1 rounded-xl border border-[#1b1b18]/10 py-2.5 text-[13px] font-medium text-[#1b1b18] transition-colors hover:bg-[#F8F8F8] sm:py-3 sm:text-[14px]"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="physicalForm.processing"
                            class="flex-1 rounded-xl bg-gradient-to-r from-[#F4AFE9] to-[#8DD0FC] py-2.5 text-[13px] font-medium text-white transition-all hover:shadow-lg disabled:opacity-50 sm:py-3 sm:text-[14px]"
                        >
                            {{ physicalForm.processing ? 'Menyimpan...' : 'Simpan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </Teleport>

    <!-- Mental Log Modal -->
    <Teleport to="body">
        <div v-if="showMentalModal" class="fixed inset-0 z-50 flex items-end justify-center bg-black/50 p-0 sm:items-center sm:p-4">
            <div class="max-h-[90vh] w-full overflow-y-auto rounded-t-2xl bg-white p-5 sm:max-w-md sm:rounded-2xl sm:p-6">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-[16px] font-semibold text-[#1b1b18] sm:text-[18px]">Catat Mood Hari Ini</h3>
                    <button @click="showMentalModal = false" class="text-[#1b1b18]/40 hover:text-[#1b1b18]">
                        <Icon icon="mdi:close" class="h-6 w-6" />
                    </button>
                </div>

                <form @submit.prevent="submitMentalLog" class="space-y-4">
                    <div>
                        <label class="mb-1 block text-[12px] font-medium text-[#1b1b18] sm:text-[13px]">Tanggal</label>
                        <input
                            v-model="mentalForm.logged_at"
                            type="date"
                            class="w-full rounded-xl border border-[#1b1b18]/10 bg-[#F8F8F8] px-3 py-2.5 text-[13px] outline-none focus:border-[#8DD0FC] sm:px-4 sm:py-3 sm:text-[14px]"
                        />
                    </div>
                    <div>
                        <label class="mb-2 block text-[12px] font-medium text-[#1b1b18] sm:mb-3 sm:text-[13px]">Bagaimana mood Anda?</label>
                        <div class="flex justify-between">
                            <button
                                v-for="i in 5"
                                :key="i"
                                type="button"
                                @click="mentalForm.mood = i"
                                class="flex flex-col items-center gap-0.5 rounded-lg p-1.5 transition-all sm:gap-1 sm:rounded-xl sm:p-3"
                                :class="mentalForm.mood === i ? 'bg-[#DDB4F6]/30 scale-110' : 'hover:bg-[#F8F8F8]'"
                            >
                                <span class="text-[22px] sm:text-[28px]">{{ getMoodEmoji(i) }}</span>
                                <span class="hidden text-[10px] text-[#1b1b18]/60 sm:block">{{ getMoodLabel(i) }}</span>
                            </button>
                        </div>
                    </div>
                    <div>
                        <label class="mb-2 block text-[12px] font-medium text-[#1b1b18] sm:text-[13px]">Level Stres (1-5)</label>
                        <input
                            v-model="mentalForm.stress_level"
                            type="range"
                            min="1"
                            max="5"
                            class="w-full accent-[#BF55FF]"
                        />
                        <div class="flex justify-between text-[10px] text-[#1b1b18]/60 sm:text-[11px]">
                            <span>Rendah</span>
                            <span>Tinggi</span>
                        </div>
                    </div>
                    <div>
                        <label class="mb-1 block text-[12px] font-medium text-[#1b1b18] sm:text-[13px]">Jam Tidur Semalam</label>
                        <input
                            v-model="mentalForm.sleep_hours"
                            type="number"
                            step="0.5"
                            placeholder="Contoh: 7.5"
                            class="w-full rounded-xl border border-[#1b1b18]/10 bg-[#F8F8F8] px-3 py-2.5 text-[13px] outline-none focus:border-[#8DD0FC] sm:px-4 sm:py-3 sm:text-[14px]"
                        />
                    </div>
                    <div>
                        <label class="mb-1 block text-[12px] font-medium text-[#1b1b18] sm:text-[13px]">Catatan (opsional)</label>
                        <textarea
                            v-model="mentalForm.note"
                            rows="3"
                            placeholder="Bagaimana perasaan Anda hari ini?"
                            class="w-full rounded-xl border border-[#1b1b18]/10 bg-[#F8F8F8] px-3 py-2.5 text-[13px] outline-none focus:border-[#8DD0FC] sm:px-4 sm:py-3 sm:text-[14px]"
                        />
                    </div>
                    <div class="flex gap-3 pt-2">
                        <button
                            type="button"
                            @click="showMentalModal = false"
                            class="flex-1 rounded-xl border border-[#1b1b18]/10 py-2.5 text-[13px] font-medium text-[#1b1b18] transition-colors hover:bg-[#F8F8F8] sm:py-3 sm:text-[14px]"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="mentalForm.processing"
                            class="flex-1 rounded-xl bg-gradient-to-r from-[#DDB4F6] to-[#BF55FF] py-2.5 text-[13px] font-medium text-white transition-all hover:shadow-lg disabled:opacity-50 sm:py-3 sm:text-[14px]"
                        >
                            {{ mentalForm.processing ? 'Menyimpan...' : 'Simpan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </Teleport>
</template>
