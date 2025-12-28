<script setup lang="ts">
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { Icon } from '@iconify/vue';
import { ref, computed } from 'vue';
import Navbar from '@/components/Navbar.vue';
import Footer from '@/components/Footer.vue';
import type { AppPageProps } from '@/types';

interface UserProfile {
    gender: 'male' | 'female' | null;
    photo_profile: string | null;
    birth_date: string | null;
    height: number | null;
    weight: number | null;
    bmi: number | null;
    bmi_category: string | null;
    age: number | null;
}

interface User {
    id: number;
    name: string;
    email: string;
    phone: string | null;
    email_verified_at: string | null;
    created_at: string;
}

interface Props {
    user: User;
    profile: UserProfile | null;
    stats: {
        chat_sessions: number;
        total_messages: number;
    };
}

const props = defineProps<Props>();

const activeTab = ref<'overview' | 'edit'>('overview');
const showPhotoModal = ref(false);
const photoInput = ref<HTMLInputElement | null>(null);
const photoPreview = ref<string | null>(null);

const form = useForm({
    name: props.user.name,
    phone: props.user.phone || '',
    gender: props.profile?.gender || '',
    birth_date: props.profile?.birth_date || '',
    height: props.profile?.height || '',
    weight: props.profile?.weight || '',
});

const photoForm = useForm({
    photo: null as File | null,
});

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
};

const formatDateShort = (date: string) => {
    return new Date(date).toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    });
};

const getBmiColor = (category: string | null) => {
    switch (category) {
        case 'Underweight': return 'text-blue-500';
        case 'Normal': return 'text-green-500';
        case 'Overweight': return 'text-yellow-500';
        case 'Obese': return 'text-red-500';
        default: return 'text-gray-500';
    }
};

const getBmiProgress = (bmi: number | null) => {
    if (!bmi) return 0;
    // Map BMI 15-40 to 0-100%
    return Math.min(100, Math.max(0, ((bmi - 15) / 25) * 100));
};

const avatarUrl = computed(() => {
    if (props.profile?.photo_profile) {
        return `/storage/${props.profile.photo_profile}`;
    }
    return null;
});

const submitProfile = () => {
    form.put('/profile', {
        preserveScroll: true,
        onSuccess: () => {
            activeTab.value = 'overview';
        },
    });
};

const selectPhoto = () => {
    photoInput.value?.click();
};

const handlePhotoChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];
    
    if (file) {
        photoForm.photo = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            photoPreview.value = e.target?.result as string;
        };
        reader.readAsDataURL(file);
        showPhotoModal.value = true;
    }
};

const uploadPhoto = () => {
    if (!photoForm.photo) return;
    
    photoForm.post('/profile/photo', {
        preserveScroll: true,
        onSuccess: () => {
            showPhotoModal.value = false;
            photoPreview.value = null;
            photoForm.reset();
        },
    });
};

const cancelPhotoUpload = () => {
    showPhotoModal.value = false;
    photoPreview.value = null;
    photoForm.reset();
};
</script>

<template>
    <Head title="Profile - DocDot" />

    <div class="min-h-screen pt-16 sm:pt-20 lg:pt-22 font-[Poppins]" style="background: linear-gradient(to left, rgba(141, 208, 252, 0.6) 0%, rgba(221, 180, 246, 0.6) 100%)">
        <Navbar />

        <div class="px-4 py-6 sm:px-6 sm:py-8 lg:px-12">
            <div class="mx-auto max-w-5xl">
                <!-- Breadcrumb -->
                <nav class="mb-4 flex items-center gap-2 text-[12px] sm:mb-6 sm:text-[14px]">
                    <Link href="/" class="text-[#1b1b18]/60 hover:text-[#1b1b18]">Beranda</Link>
                    <Icon icon="mdi:chevron-right" class="h-4 w-4 text-[#1b1b18]/40" />
                    <span class="text-[#1b1b18]">Profile</span>
                </nav>

                <!-- Profile Header Card -->
                <div class="mb-4 overflow-hidden rounded-2xl bg-white/70 backdrop-blur-sm sm:mb-6 sm:rounded-3xl">
                    <div class="relative h-24 bg-gradient-to-r from-[#F4AFE9] to-[#8DD0FC] sm:h-32">
                        <!-- Decorative elements -->
                        <div class="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-white/10"></div>
                        <div class="absolute -left-5 bottom-0 h-24 w-24 rounded-full bg-white/10"></div>
                    </div>
                    
                    <div class="relative px-4 pb-4 sm:px-6 sm:pb-6 lg:px-8">
                        <!-- Avatar -->
                        <div class="-mt-12 mb-3 flex items-end gap-3 sm:-mt-16 sm:mb-4 sm:gap-4">
                            <div class="relative">
                                <div 
                                    v-if="avatarUrl"
                                    class="h-20 w-20 overflow-hidden rounded-full border-4 border-white shadow-lg sm:h-28 sm:w-28"
                                >
                                    <img :src="avatarUrl" alt="Profile" class="h-full w-full object-cover" />
                                </div>
                                <div 
                                    v-else
                                    class="flex h-20 w-20 items-center justify-center rounded-full border-4 border-white bg-gradient-to-r from-[#F4AFE9] to-[#8DD0FC] shadow-lg sm:h-28 sm:w-28"
                                >
                                    <Icon icon="mdi:account" class="h-12 w-12 text-white sm:h-16 sm:w-16" />
                                </div>
                                
                                <!-- Edit Photo Button -->
                                <button 
                                    @click="selectPhoto"
                                    class="absolute bottom-0 right-0 flex h-7 w-7 items-center justify-center rounded-full bg-white shadow-md transition-transform hover:scale-110 sm:h-9 sm:w-9"
                                >
                                    <Icon icon="mdi:camera" class="h-4 w-4 text-[#1b1b18] sm:h-5 sm:w-5" />
                                </button>
                                <input 
                                    ref="photoInput"
                                    type="file"
                                    accept="image/*"
                                    class="hidden"
                                    @change="handlePhotoChange"
                                />
                            </div>
                            
                            <div class="mb-2 flex-1 min-w-0">
                                <h1 class="truncate text-[18px] font-bold text-[#1b1b18] sm:text-[24px] lg:text-[28px]">{{ user.name }}</h1>
                                <p class="flex items-center gap-2 text-[12px] text-[#1b1b18]/60 sm:text-[14px]">
                                    <Icon icon="mdi:calendar-account" class="h-4 w-4" />
                                    <span class="hidden sm:inline">Member sejak</span> {{ formatDateShort(user.created_at) }}
                                </p>
                            </div>

                            <!-- Tab Buttons -->
                            <div class="hidden gap-2 sm:flex">
                                <button 
                                    @click="activeTab = 'overview'"
                                    :class="[
                                        'rounded-full px-5 py-2 text-[14px] font-medium transition-all',
                                        activeTab === 'overview' 
                                            ? 'bg-gradient-to-r from-[#F4AFE9] to-[#8DD0FC] text-white' 
                                            : 'bg-white text-[#1b1b18] hover:bg-[#F8F8F8]'
                                    ]"
                                >
                                    Overview
                                </button>
                                <button 
                                    @click="activeTab = 'edit'"
                                    :class="[
                                        'rounded-full px-5 py-2 text-[14px] font-medium transition-all',
                                        activeTab === 'edit' 
                                            ? 'bg-gradient-to-r from-[#F4AFE9] to-[#8DD0FC] text-white' 
                                            : 'bg-white text-[#1b1b18] hover:bg-[#F8F8F8]'
                                    ]"
                                >
                                    Edit Profile
                                </button>
                            </div>
                        </div>

                        <!-- Mobile Tab Buttons -->
                        <div class="flex gap-2 sm:hidden">
                            <button 
                                @click="activeTab = 'overview'"
                                :class="[
                                    'flex-1 rounded-full px-4 py-2 text-[13px] font-medium transition-all',
                                    activeTab === 'overview' 
                                        ? 'bg-gradient-to-r from-[#F4AFE9] to-[#8DD0FC] text-white' 
                                        : 'bg-white text-[#1b1b18]'
                                ]"
                            >
                                Overview
                            </button>
                            <button 
                                @click="activeTab = 'edit'"
                                :class="[
                                    'flex-1 rounded-full px-4 py-2 text-[13px] font-medium transition-all',
                                    activeTab === 'edit' 
                                        ? 'bg-gradient-to-r from-[#F4AFE9] to-[#8DD0FC] text-white' 
                                        : 'bg-white text-[#1b1b18]'
                                ]"
                            >
                                Edit Profile
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Overview Tab -->
                <div v-if="activeTab === 'overview'" class="grid gap-4 sm:gap-6 lg:grid-cols-3">
                    <!-- Left Column - Basic Info & Stats -->
                    <div class="space-y-4 sm:space-y-6 lg:col-span-2">
                        <!-- Basic Info Card -->
                        <div class="rounded-xl bg-white p-4 sm:rounded-2xl sm:p-6">
                            <h2 class="mb-3 flex items-center gap-2 text-[16px] font-semibold text-[#1b1b18] sm:mb-4 sm:text-[18px]">
                                <Icon icon="mdi:account-details" class="h-5 w-5 text-[#8DD0FC]" />
                                Informasi Dasar
                            </h2>
                            
                            <div class="grid gap-3 sm:grid-cols-2 sm:gap-4">
                                <!-- Name -->
                                <div class="flex items-center gap-3 rounded-xl bg-[#F8F8F8] p-4">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-[#F4AFE9]/20">
                                        <Icon icon="mdi:account-outline" class="h-5 w-5 text-[#F4AFE9]" />
                                    </div>
                                    <div>
                                        <p class="text-[11px] text-[#1b1b18]/50">Nama Lengkap</p>
                                        <p class="text-[14px] font-medium text-[#1b1b18]">{{ user.name }}</p>
                                    </div>
                                </div>

                                <!-- Email -->
                                <div class="flex items-center gap-3 rounded-xl bg-[#F8F8F8] p-4">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-[#8DD0FC]/20">
                                        <Icon icon="mdi:email-outline" class="h-5 w-5 text-[#8DD0FC]" />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-[11px] text-[#1b1b18]/50">Email</p>
                                        <p class="truncate text-[14px] font-medium text-[#1b1b18]">{{ user.email }}</p>
                                    </div>
                                    <div v-if="user.email_verified_at" class="flex-shrink-0">
                                        <Icon icon="mdi:check-decagram" class="h-5 w-5 text-green-500" />
                                    </div>
                                </div>

                                <!-- Phone -->
                                <div class="flex items-center gap-3 rounded-xl bg-[#F8F8F8] p-4">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-[#DDB4F6]/20">
                                        <Icon icon="mdi:phone-outline" class="h-5 w-5 text-[#DDB4F6]" />
                                    </div>
                                    <div>
                                        <p class="text-[11px] text-[#1b1b18]/50">Telepon</p>
                                        <p class="text-[14px] font-medium text-[#1b1b18]">{{ user.phone || 'Belum diisi' }}</p>
                                    </div>
                                </div>

                                <!-- Gender -->
                                <div class="flex items-center gap-3 rounded-xl bg-[#F8F8F8] p-4">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-[#FF7CEA]/20">
                                        <Icon :icon="profile?.gender === 'male' ? 'mdi:gender-male' : profile?.gender === 'female' ? 'mdi:gender-female' : 'mdi:gender-male-female'" class="h-5 w-5 text-[#FF7CEA]" />
                                    </div>
                                    <div>
                                        <p class="text-[11px] text-[#1b1b18]/50">Jenis Kelamin</p>
                                        <p class="text-[14px] font-medium text-[#1b1b18]">
                                            {{ profile?.gender === 'male' ? 'Laki-laki' : profile?.gender === 'female' ? 'Perempuan' : 'Belum diisi' }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Age/Birth Date -->
                                <div class="flex items-center gap-3 rounded-xl bg-[#F8F8F8] p-4">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-[#43B3FC]/20">
                                        <Icon icon="mdi:cake-variant-outline" class="h-5 w-5 text-[#43B3FC]" />
                                    </div>
                                    <div>
                                        <p class="text-[11px] text-[#1b1b18]/50">Usia</p>
                                        <p class="text-[14px] font-medium text-[#1b1b18]">
                                            {{ profile?.age ? `${profile.age} tahun` : 'Belum diisi' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Activity Stats -->
                        <div class="rounded-xl bg-white p-4 sm:rounded-2xl sm:p-6">
                            <h2 class="mb-3 flex items-center gap-2 text-[16px] font-semibold text-[#1b1b18] sm:mb-4 sm:text-[18px]">
                                <Icon icon="mdi:chart-line" class="h-5 w-5 text-[#8DD0FC]" />
                                Aktivitas
                            </h2>
                            
                            <div class="grid gap-3 sm:grid-cols-2 sm:gap-4">
                                <Link 
                                    href="/chat-history"
                                    class="group flex items-center gap-4 rounded-xl bg-gradient-to-r from-[#F4AFE9]/10 to-[#8DD0FC]/10 p-4 transition-all hover:from-[#F4AFE9]/20 hover:to-[#8DD0FC]/20"
                                >
                                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gradient-to-r from-[#F4AFE9] to-[#8DD0FC]">
                                        <Icon icon="mdi:message-text-outline" class="h-6 w-6 text-white" />
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-[24px] font-bold text-[#1b1b18]">{{ stats.chat_sessions }}</p>
                                        <p class="text-[12px] text-[#1b1b18]/60">Sesi Konsultasi</p>
                                    </div>
                                    <Icon icon="mdi:arrow-right" class="h-5 w-5 text-[#1b1b18]/30 transition-transform group-hover:translate-x-1 group-hover:text-[#8DD0FC]" />
                                </Link>

                                <div class="flex items-center gap-4 rounded-xl bg-gradient-to-r from-[#8DD0FC]/10 to-[#43B3FC]/10 p-4">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gradient-to-r from-[#8DD0FC] to-[#43B3FC]">
                                        <Icon icon="mdi:chat-processing-outline" class="h-6 w-6 text-white" />
                                    </div>
                                    <div>
                                        <p class="text-[24px] font-bold text-[#1b1b18]">{{ stats.total_messages }}</p>
                                        <p class="text-[12px] text-[#1b1b18]/60">Total Pesan</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Health Info -->
                    <div class="space-y-4 sm:space-y-6">
                        <!-- BMI Card -->
                        <div class="rounded-xl bg-white p-4 sm:rounded-2xl sm:p-6">
                            <h2 class="mb-3 flex items-center gap-2 text-[16px] font-semibold text-[#1b1b18] sm:mb-4 sm:text-[18px]">
                                <Icon icon="mdi:scale-bathroom" class="h-5 w-5 text-[#8DD0FC]" />
                                BMI
                            </h2>
                            
                            <div v-if="profile?.bmi" class="text-center">
                                <div class="mb-2 text-[36px] font-bold sm:text-[48px]" :class="getBmiColor(profile.bmi_category)">
                                    {{ profile.bmi }}
                                </div>
                                <p class="mb-4 text-[14px] font-medium" :class="getBmiColor(profile.bmi_category)">
                                    {{ profile.bmi_category }}
                                </p>
                                
                                <!-- BMI Scale -->
                                <div class="mb-2 h-3 overflow-hidden rounded-full bg-gray-200">
                                    <div class="relative h-full w-full bg-gradient-to-r from-blue-400 via-green-400 via-yellow-400 to-red-400">
                                        <div 
                                            class="absolute top-0 h-full w-1 bg-[#1b1b18]"
                                            :style="{ left: `${getBmiProgress(profile.bmi)}%` }"
                                        ></div>
                                    </div>
                                </div>
                                <div class="flex justify-between text-[10px] text-[#1b1b18]/50">
                                    <span>15</span>
                                    <span>18.5</span>
                                    <span>25</span>
                                    <span>30</span>
                                    <span>40</span>
                                </div>
                            </div>
                            <div v-else class="py-8 text-center">
                                <Icon icon="mdi:scale-off" class="mx-auto h-12 w-12 text-[#1b1b18]/20" />
                                <p class="mt-2 text-[13px] text-[#1b1b18]/60">
                                    Isi tinggi dan berat badan untuk melihat BMI
                                </p>
                                <button 
                                    @click="activeTab = 'edit'"
                                    class="mt-3 text-[13px] font-medium text-[#8DD0FC] hover:underline"
                                >
                                    Lengkapi Profil
                                </button>
                            </div>
                        </div>

                        <!-- Body Stats -->
                        <div class="rounded-xl bg-white p-4 sm:rounded-2xl sm:p-6">
                            <h2 class="mb-3 flex items-center gap-2 text-[16px] font-semibold text-[#1b1b18] sm:mb-4 sm:text-[18px]">
                                <Icon icon="mdi:human" class="h-5 w-5 text-[#8DD0FC]" />
                                Data Tubuh
                            </h2>
                            
                            <div class="space-y-2 sm:space-y-3">
                                <div class="flex items-center justify-between rounded-xl bg-[#F8F8F8] p-4">
                                    <div class="flex items-center gap-3">
                                        <Icon icon="mdi:human-male-height" class="h-5 w-5 text-[#8DD0FC]" />
                                        <span class="text-[14px] text-[#1b1b18]/70">Tinggi Badan</span>
                                    </div>
                                    <span class="text-[14px] font-semibold text-[#1b1b18]">
                                        {{ profile?.height ? `${profile.height} cm` : '-' }}
                                    </span>
                                </div>

                                <div class="flex items-center justify-between rounded-xl bg-[#F8F8F8] p-4">
                                    <div class="flex items-center gap-3">
                                        <Icon icon="mdi:weight" class="h-5 w-5 text-[#F4AFE9]" />
                                        <span class="text-[14px] text-[#1b1b18]/70">Berat Badan</span>
                                    </div>
                                    <span class="text-[14px] font-semibold text-[#1b1b18]">
                                        {{ profile?.weight ? `${profile.weight} kg` : '-' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="rounded-xl bg-white p-4 sm:rounded-2xl sm:p-6">
                            <h2 class="mb-3 text-[16px] font-semibold text-[#1b1b18] sm:mb-4 sm:text-[18px]">Aksi Cepat</h2>
                            
                            <div class="space-y-1 sm:space-y-2">
                                <Link 
                                    href="/consultation"
                                    class="flex items-center gap-3 rounded-xl p-3 transition-colors hover:bg-[#8DD0FC]/10"
                                >
                                    <Icon icon="mdi:robot" class="h-5 w-5 text-[#8DD0FC]" />
                                    <span class="text-[14px] text-[#1b1b18]">Konsultasi Baru</span>
                                </Link>
                                <Link 
                                    href="/article"
                                    class="flex items-center gap-3 rounded-xl p-3 transition-colors hover:bg-[#F4AFE9]/10"
                                >
                                    <Icon icon="mdi:newspaper-variant-outline" class="h-5 w-5 text-[#F4AFE9]" />
                                    <span class="text-[14px] text-[#1b1b18]">Baca Artikel</span>
                                </Link>
                                <Link 
                                    href="/drug-catalog"
                                    class="flex items-center gap-3 rounded-xl p-3 transition-colors hover:bg-[#DDB4F6]/10"
                                >
                                    <Icon icon="mdi:pill" class="h-5 w-5 text-[#DDB4F6]" />
                                    <span class="text-[14px] text-[#1b1b18]">Katalog Obat</span>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edit Tab -->
                <div v-if="activeTab === 'edit'" class="mx-auto max-w-2xl">
                    <div class="rounded-xl bg-white p-4 sm:rounded-2xl sm:p-6 lg:p-8">
                        <h2 class="mb-4 text-[18px] font-semibold text-[#1b1b18] sm:mb-6 sm:text-[20px]">Edit Profil</h2>
                        
                        <form @submit.prevent="submitProfile" class="space-y-4 sm:space-y-5">
                            <!-- Name -->
                            <div>
                                <label class="mb-1 block text-[13px] font-medium text-[#1b1b18]/70">
                                    Nama Lengkap <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    v-model="form.name"
                                    type="text"
                                    class="w-full rounded-xl border border-[#1b1b18]/10 bg-[#F8F8F8] px-4 py-3 text-[14px] outline-none focus:border-[#8DD0FC] focus:ring-0"
                                    placeholder="Masukkan nama lengkap"
                                />
                                <p v-if="form.errors.name" class="mt-1 text-[12px] text-red-500">{{ form.errors.name }}</p>
                            </div>

                            <!-- Phone -->
                            <div>
                                <label class="mb-1 block text-[13px] font-medium text-[#1b1b18]/70">
                                    Nomor Telepon
                                </label>
                                <input 
                                    v-model="form.phone"
                                    type="tel"
                                    class="w-full rounded-xl border border-[#1b1b18]/10 bg-[#F8F8F8] px-4 py-3 text-[14px] outline-none focus:border-[#8DD0FC] focus:ring-0"
                                    placeholder="08xxxxxxxxxx"
                                />
                                <p v-if="form.errors.phone" class="mt-1 text-[12px] text-red-500">{{ form.errors.phone }}</p>
                            </div>

                            <!-- Gender -->
                            <div>
                                <label class="mb-1 block text-[13px] font-medium text-[#1b1b18]/70">
                                    Jenis Kelamin
                                </label>
                                <div class="flex gap-4">
                                    <label 
                                        :class="[
                                            'flex flex-1 cursor-pointer items-center justify-center gap-2 rounded-xl border-2 p-4 transition-all',
                                            form.gender === 'male' 
                                                ? 'border-[#8DD0FC] bg-[#8DD0FC]/10' 
                                                : 'border-[#1b1b18]/10 hover:border-[#8DD0FC]/50'
                                        ]"
                                    >
                                        <input type="radio" v-model="form.gender" value="male" class="hidden" />
                                        <Icon icon="mdi:gender-male" class="h-6 w-6 text-[#8DD0FC]" />
                                        <span class="text-[14px] font-medium text-[#1b1b18]">Laki-laki</span>
                                    </label>
                                    <label 
                                        :class="[
                                            'flex flex-1 cursor-pointer items-center justify-center gap-2 rounded-xl border-2 p-4 transition-all',
                                            form.gender === 'female' 
                                                ? 'border-[#F4AFE9] bg-[#F4AFE9]/10' 
                                                : 'border-[#1b1b18]/10 hover:border-[#F4AFE9]/50'
                                        ]"
                                    >
                                        <input type="radio" v-model="form.gender" value="female" class="hidden" />
                                        <Icon icon="mdi:gender-female" class="h-6 w-6 text-[#F4AFE9]" />
                                        <span class="text-[14px] font-medium text-[#1b1b18]">Perempuan</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Birth Date -->
                            <div>
                                <label class="mb-1 block text-[13px] font-medium text-[#1b1b18]/70">
                                    Tanggal Lahir
                                </label>
                                <input 
                                    v-model="form.birth_date"
                                    type="date"
                                    class="w-full rounded-xl border border-[#1b1b18]/10 bg-[#F8F8F8] px-4 py-3 text-[14px] outline-none focus:border-[#8DD0FC] focus:ring-0"
                                />
                                <p v-if="form.errors.birth_date" class="mt-1 text-[12px] text-red-500">{{ form.errors.birth_date }}</p>
                            </div>

                            <!-- Height & Weight -->
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="mb-1 block text-[13px] font-medium text-[#1b1b18]/70">
                                        Tinggi Badan (cm)
                                    </label>
                                    <input 
                                        v-model="form.height"
                                        type="number"
                                        min="50"
                                        max="250"
                                        class="w-full rounded-xl border border-[#1b1b18]/10 bg-[#F8F8F8] px-4 py-3 text-[14px] outline-none focus:border-[#8DD0FC] focus:ring-0"
                                        placeholder="170"
                                    />
                                    <p v-if="form.errors.height" class="mt-1 text-[12px] text-red-500">{{ form.errors.height }}</p>
                                </div>
                                <div>
                                    <label class="mb-1 block text-[13px] font-medium text-[#1b1b18]/70">
                                        Berat Badan (kg)
                                    </label>
                                    <input 
                                        v-model="form.weight"
                                        type="number"
                                        min="20"
                                        max="300"
                                        class="w-full rounded-xl border border-[#1b1b18]/10 bg-[#F8F8F8] px-4 py-3 text-[14px] outline-none focus:border-[#8DD0FC] focus:ring-0"
                                        placeholder="65"
                                    />
                                    <p v-if="form.errors.weight" class="mt-1 text-[12px] text-red-500">{{ form.errors.weight }}</p>
                                </div>
                            </div>

                            <!-- Buttons -->
                            <div class="flex gap-3 pt-4">
                                <button 
                                    type="button"
                                    @click="activeTab = 'overview'"
                                    class="flex-1 rounded-xl border border-[#1b1b18]/20 py-3 text-[14px] font-medium text-[#1b1b18] transition-colors hover:bg-[#F8F8F8]"
                                >
                                    Batal
                                </button>
                                <button 
                                    type="submit"
                                    :disabled="form.processing"
                                    class="flex flex-1 items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#F4AFE9] to-[#8DD0FC] py-3 text-[14px] font-medium text-white transition-all hover:shadow-lg disabled:opacity-50"
                                >
                                    <Icon v-if="form.processing" icon="mdi:loading" class="h-5 w-5 animate-spin" />
                                    {{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Photo Upload Modal -->
        <Teleport to="body">
            <div 
                v-if="showPhotoModal"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
                @click.self="cancelPhotoUpload"
            >
                <div class="w-full max-w-md rounded-2xl bg-white p-6">
                    <h3 class="mb-4 text-[18px] font-semibold text-[#1b1b18]">Ubah Foto Profil</h3>
                    
                    <div v-if="photoPreview" class="mb-4 flex justify-center">
                        <div class="h-40 w-40 overflow-hidden rounded-full border-4 border-[#8DD0FC]">
                            <img :src="photoPreview" alt="Preview" class="h-full w-full object-cover" />
                        </div>
                    </div>
                    
                    <p v-if="photoForm.errors.photo" class="mb-4 text-center text-[13px] text-red-500">
                        {{ photoForm.errors.photo }}
                    </p>
                    
                    <div class="flex gap-3">
                        <button 
                            @click="cancelPhotoUpload"
                            class="flex-1 rounded-xl border border-[#1b1b18]/20 py-3 text-[14px] font-medium text-[#1b1b18] transition-colors hover:bg-[#F8F8F8]"
                        >
                            Batal
                        </button>
                        <button 
                            @click="uploadPhoto"
                            :disabled="photoForm.processing"
                            class="flex flex-1 items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#F4AFE9] to-[#8DD0FC] py-3 text-[14px] font-medium text-white transition-all hover:shadow-lg disabled:opacity-50"
                        >
                            <Icon v-if="photoForm.processing" icon="mdi:loading" class="h-5 w-5 animate-spin" />
                            {{ photoForm.processing ? 'Mengupload...' : 'Upload' }}
                        </button>
                    </div>
                </div>
            </div>
        </Teleport>

        <Footer />
    </div>
</template>
