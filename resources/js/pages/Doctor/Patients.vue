<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Icon } from '@iconify/vue';
import { ref, computed, onMounted } from 'vue';
import Navbar from '@/components/Navbar.vue';
import Footer from '@/components/Footer.vue';
import axios from 'axios';

interface PatientProfile {
    photo_profile: string | null;
    gender: 'male' | 'female' | null;
    age: number | null;
    height: number | null;
    weight: number | null;
    bmi: number | null;
    bmi_category: string | null;
}

interface Patient {
    id: number;
    name: string;
    email: string;
    profile: PatientProfile | null;
}

interface PatientAlert {
    id: number;
    alert_type: string;
    triggered_text: string;
    matched_keywords: string[];
    is_read: boolean;
    created_at: string;
}

interface DoctorPatient {
    id: number;
    doctor_id: number;
    patient_id: number;
    status: 'pending' | 'accepted' | 'rejected';
    message: string | null;
    created_at: string;
    patient: Patient;
    alerts: PatientAlert[];
    unread_alerts_count: number;
}

interface PendingRequest {
    id: number;
    message: string | null;
    created_at: string;
    patient: Patient;
}

const patients = ref<DoctorPatient[]>([]);
const pendingRequests = ref<PendingRequest[]>([]);
const loading = ref(true);
const activeTab = ref<'patients' | 'requests'>('patients');
const selectedPatient = ref<DoctorPatient | null>(null);
const showPatientDetail = ref(false);
const patientDetail = ref<any>(null);
const loadingDetail = ref(false);

const fetchData = async () => {
    loading.value = true;
    try {
        const [patientsRes, requestsRes] = await Promise.all([
            axios.get('/doctor-patient/my-patients'),
            axios.get('/doctor-patient/pending-requests'),
        ]);
        patients.value = patientsRes.data.patients;
        pendingRequests.value = requestsRes.data.requests;
    } catch (error) {
        console.error('Failed to fetch data:', error);
    } finally {
        loading.value = false;
    }
};

const acceptRequest = async (requestId: number) => {
    try {
        await axios.post(`/doctor-patient/accept/${requestId}`);
        await fetchData();
    } catch (error) {
        console.error('Failed to accept request:', error);
    }
};

const rejectRequest = async (requestId: number) => {
    if (!confirm('Yakin ingin menolak permintaan ini?')) return;
    
    try {
        await axios.post(`/doctor-patient/reject/${requestId}`);
        await fetchData();
    } catch (error) {
        console.error('Failed to reject request:', error);
    }
};

const openPatientDetail = async (doctorPatient: DoctorPatient) => {
    selectedPatient.value = doctorPatient;
    showPatientDetail.value = true;
    loadingDetail.value = true;
    
    try {
        const response = await axios.get(`/doctor-patient/patient/${doctorPatient.id}`);
        patientDetail.value = response.data;
        
        // Mark alerts as read
        if (doctorPatient.unread_alerts_count > 0) {
            await axios.post(`/doctor-patient/patient/${doctorPatient.id}/mark-read`);
            await fetchData();
        }
    } catch (error) {
        console.error('Failed to fetch patient detail:', error);
    } finally {
        loadingDetail.value = false;
    }
};

const closePatientDetail = () => {
    showPatientDetail.value = false;
    selectedPatient.value = null;
    patientDetail.value = null;
};

const removePatient = async (doctorPatientId: number) => {
    if (!confirm('Yakin ingin menghapus pasien ini dari daftar Anda?')) return;
    
    try {
        await axios.delete(`/doctor-patient/patient/${doctorPatientId}`);
        closePatientDetail();
        await fetchData();
    } catch (error) {
        console.error('Failed to remove patient:', error);
    }
};

const totalAlerts = computed(() => {
    return patients.value.reduce((sum, p) => sum + p.unread_alerts_count, 0);
});

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
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

onMounted(() => {
    fetchData();
});
</script>

<template>
    <Head title="Pasien Saya - DocDot" />

    <div class="min-h-screen pt-16 sm:pt-20 lg:pt-22 font-[Poppins]" style="background: linear-gradient(to left, rgba(141, 208, 252, 0.6) 0%, rgba(221, 180, 246, 0.6) 100%)">
        <Navbar />

        <div class="px-4 py-6 sm:px-6 sm:py-8 lg:px-12">
            <div class="mx-auto max-w-6xl">
                <!-- Breadcrumb -->
                <nav class="mb-4 flex items-center gap-2 text-[12px] sm:mb-6 sm:text-[14px]">
                    <Link href="/" class="text-[#1b1b18]/60 hover:text-[#1b1b18]">Beranda</Link>
                    <Icon icon="mdi:chevron-right" class="h-4 w-4 text-[#1b1b18]/40" />
                    <span class="text-[#1b1b18]">Pasien Saya</span>
                </nav>

                <!-- Header -->
                <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="text-[24px] font-bold text-[#1b1b18] sm:text-[28px]">Pasien Saya</h1>
                        <p class="text-[14px] text-[#1b1b18]/60">Pantau kesehatan pasien yang terhubung dengan Anda</p>
                    </div>
                    
                    <!-- Alert Badge -->
                    <div v-if="totalAlerts > 0" class="flex items-center gap-2 rounded-full bg-red-100 px-4 py-2">
                        <Icon icon="mdi:alert" class="h-5 w-5 text-red-500" />
                        <span class="text-[14px] font-medium text-red-700">{{ totalAlerts }} Alert Baru</span>
                    </div>
                </div>

                <!-- Tabs -->
                <div class="mb-6 flex gap-2 rounded-xl bg-white/50 p-1">
                    <button
                        @click="activeTab = 'patients'"
                        :class="[
                            'flex-1 rounded-lg px-4 py-2 text-[14px] font-medium transition-all',
                            activeTab === 'patients' 
                                ? 'bg-white text-[#1b1b18] shadow-sm' 
                                : 'text-[#1b1b18]/60 hover:text-[#1b1b18]'
                        ]"
                    >
                        <Icon icon="mdi:account-group" class="mr-2 inline h-5 w-5" />
                        Pasien ({{ patients.length }})
                    </button>
                    <button
                        @click="activeTab = 'requests'"
                        :class="[
                            'relative flex-1 rounded-lg px-4 py-2 text-[14px] font-medium transition-all',
                            activeTab === 'requests' 
                                ? 'bg-white text-[#1b1b18] shadow-sm' 
                                : 'text-[#1b1b18]/60 hover:text-[#1b1b18]'
                        ]"
                    >
                        <Icon icon="mdi:account-clock" class="mr-2 inline h-5 w-5" />
                        Permintaan ({{ pendingRequests.length }})
                        <span 
                            v-if="pendingRequests.length > 0"
                            class="absolute -right-1 -top-1 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white"
                        >
                            {{ pendingRequests.length }}
                        </span>
                    </button>
                </div>

                <!-- Loading -->
                <div v-if="loading" class="py-16 text-center">
                    <Icon icon="mdi:loading" class="mx-auto h-12 w-12 animate-spin text-[#8DD0FC]" />
                    <p class="mt-4 text-[14px] text-[#1b1b18]/60">Memuat data...</p>
                </div>

                <!-- Patients Tab -->
                <div v-else-if="activeTab === 'patients'">
                    <div v-if="patients.length === 0" class="rounded-2xl bg-white p-8 text-center">
                        <Icon icon="mdi:account-off" class="mx-auto h-16 w-16 text-[#1b1b18]/20" />
                        <h3 class="mt-4 text-[18px] font-semibold text-[#1b1b18]">Belum Ada Pasien</h3>
                        <p class="mt-2 text-[14px] text-[#1b1b18]/60">
                            Pasien akan muncul di sini setelah Anda menyetujui permintaan mereka
                        </p>
                    </div>

                    <div v-else class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        <button
                            v-for="doctorPatient in patients"
                            :key="doctorPatient.id"
                            @click="openPatientDetail(doctorPatient)"
                            class="relative rounded-2xl bg-white p-4 text-left transition-all hover:shadow-lg sm:p-6"
                        >
                            <!-- Alert Badge -->
                            <div 
                                v-if="doctorPatient.unread_alerts_count > 0"
                                class="absolute -right-2 -top-2 flex h-8 w-8 items-center justify-center rounded-full bg-red-500 text-[12px] font-bold text-white shadow-lg animate-pulse"
                            >
                                {{ doctorPatient.unread_alerts_count }}
                            </div>

                            <div class="flex items-start gap-4">
                                <!-- Avatar -->
                                <div class="h-14 w-14 overflow-hidden rounded-full bg-gradient-to-r from-[#F4AFE9] to-[#8DD0FC]">
                                    <img 
                                        v-if="doctorPatient.patient.profile?.photo_profile"
                                        :src="`/storage/${doctorPatient.patient.profile.photo_profile}`"
                                        alt="Patient"
                                        class="h-full w-full object-cover"
                                    />
                                    <div v-else class="flex h-full w-full items-center justify-center">
                                        <Icon icon="mdi:account" class="h-8 w-8 text-white" />
                                    </div>
                                </div>

                                <div class="flex-1 min-w-0">
                                    <h3 class="truncate text-[16px] font-semibold text-[#1b1b18]">
                                        {{ doctorPatient.patient.name }}
                                    </h3>
                                    <p class="truncate text-[12px] text-[#1b1b18]/60">
                                        {{ doctorPatient.patient.email }}
                                    </p>
                                </div>
                            </div>

                            <!-- Quick Stats -->
                            <div class="mt-4 grid grid-cols-3 gap-2">
                                <div class="rounded-lg bg-[#F8F8F8] p-2 text-center">
                                    <p class="text-[11px] text-[#1b1b18]/50">Usia</p>
                                    <p class="text-[14px] font-semibold text-[#1b1b18]">
                                        {{ doctorPatient.patient.profile?.age || '-' }}
                                    </p>
                                </div>
                                <div class="rounded-lg bg-[#F8F8F8] p-2 text-center">
                                    <p class="text-[11px] text-[#1b1b18]/50">BMI</p>
                                    <p 
                                        class="text-[14px] font-semibold"
                                        :class="getBmiColor(doctorPatient.patient.profile?.bmi_category || null)"
                                    >
                                        {{ doctorPatient.patient.profile?.bmi || '-' }}
                                    </p>
                                </div>
                                <div class="rounded-lg bg-[#F8F8F8] p-2 text-center">
                                    <p class="text-[11px] text-[#1b1b18]/50">Gender</p>
                                    <p class="text-[14px] font-semibold text-[#1b1b18]">
                                        {{ doctorPatient.patient.profile?.gender === 'male' ? 'L' : doctorPatient.patient.profile?.gender === 'female' ? 'P' : '-' }}
                                    </p>
                                </div>
                            </div>

                            <!-- Recent Alerts -->
                            <div 
                                v-if="doctorPatient.alerts.length > 0" 
                                class="mt-4 rounded-lg bg-red-50 p-3"
                            >
                                <p class="flex items-center gap-1 text-[11px] font-medium text-red-600">
                                    <Icon icon="mdi:alert" class="h-4 w-4" />
                                    Alert Terbaru
                                </p>
                                <p class="mt-1 line-clamp-2 text-[12px] text-red-700">
                                    "{{ doctorPatient.alerts[0]?.triggered_text }}"
                                </p>
                            </div>

                            <div class="mt-4 flex items-center justify-between text-[11px] text-[#1b1b18]/40">
                                <span>Terhubung: {{ formatDate(doctorPatient.created_at) }}</span>
                                <Icon icon="mdi:chevron-right" class="h-4 w-4" />
                            </div>
                        </button>
                    </div>
                </div>

                <!-- Requests Tab -->
                <div v-else-if="activeTab === 'requests'">
                    <div v-if="pendingRequests.length === 0" class="rounded-2xl bg-white p-8 text-center">
                        <Icon icon="mdi:inbox-arrow-down-outline" class="mx-auto h-16 w-16 text-[#1b1b18]/20" />
                        <h3 class="mt-4 text-[18px] font-semibold text-[#1b1b18]">Tidak Ada Permintaan</h3>
                        <p class="mt-2 text-[14px] text-[#1b1b18]/60">
                            Permintaan dari pasien akan muncul di sini
                        </p>
                    </div>

                    <div v-else class="space-y-4">
                        <div
                            v-for="request in pendingRequests"
                            :key="request.id"
                            class="rounded-2xl bg-white p-4 sm:p-6"
                        >
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                                <!-- Patient Info -->
                                <div class="flex flex-1 items-center gap-4">
                                    <div class="h-14 w-14 overflow-hidden rounded-full bg-gradient-to-r from-[#F4AFE9] to-[#8DD0FC]">
                                        <img 
                                            v-if="request.patient.profile?.photo_profile"
                                            :src="`/storage/${request.patient.profile.photo_profile}`"
                                            alt="Patient"
                                            class="h-full w-full object-cover"
                                        />
                                        <div v-else class="flex h-full w-full items-center justify-center">
                                            <Icon icon="mdi:account" class="h-8 w-8 text-white" />
                                        </div>
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <h3 class="truncate text-[16px] font-semibold text-[#1b1b18]">
                                            {{ request.patient.name }}
                                        </h3>
                                        <p class="truncate text-[12px] text-[#1b1b18]/60">
                                            {{ request.patient.email }}
                                        </p>
                                        <p class="text-[11px] text-[#1b1b18]/40">
                                            {{ formatDate(request.created_at) }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Message -->
                                <div v-if="request.message" class="flex-1 rounded-lg bg-[#F8F8F8] p-3">
                                    <p class="text-[11px] text-[#1b1b18]/50">Pesan:</p>
                                    <p class="text-[13px] text-[#1b1b18]">{{ request.message }}</p>
                                </div>

                                <!-- Actions -->
                                <div class="flex gap-2">
                                    <button
                                        @click="rejectRequest(request.id)"
                                        class="rounded-xl border border-red-200 px-4 py-2 text-[14px] font-medium text-red-500 transition-colors hover:bg-red-50"
                                    >
                                        Tolak
                                    </button>
                                    <button
                                        @click="acceptRequest(request.id)"
                                        class="rounded-xl bg-gradient-to-r from-[#8DD0FC] to-[#43B3FC] px-4 py-2 text-[14px] font-medium text-white transition-all hover:shadow-lg"
                                    >
                                        Terima
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Patient Detail Modal -->
        <Teleport to="body">
            <div 
                v-if="showPatientDetail"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
                @click.self="closePatientDetail"
            >
                <div class="w-full max-w-4xl rounded-2xl bg-white max-h-[90vh] overflow-hidden flex flex-col">
                    <!-- Header -->
                    <div class="flex items-center justify-between border-b p-4 sm:p-6">
                        <div class="flex items-center gap-4">
                            <div class="h-12 w-12 overflow-hidden rounded-full bg-gradient-to-r from-[#F4AFE9] to-[#8DD0FC]">
                                <img 
                                    v-if="selectedPatient?.patient.profile?.photo_profile"
                                    :src="`/storage/${selectedPatient.patient.profile.photo_profile}`"
                                    alt="Patient"
                                    class="h-full w-full object-cover"
                                />
                                <div v-else class="flex h-full w-full items-center justify-center">
                                    <Icon icon="mdi:account" class="h-6 w-6 text-white" />
                                </div>
                            </div>
                            <div>
                                <h3 class="text-[18px] font-semibold text-[#1b1b18]">
                                    {{ selectedPatient?.patient.name }}
                                </h3>
                                <p class="text-[13px] text-[#1b1b18]/60">
                                    {{ selectedPatient?.patient.email }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button
                                @click="removePatient(selectedPatient!.id)"
                                class="rounded-lg bg-red-50 p-2 text-red-500 transition-colors hover:bg-red-100"
                                title="Hapus pasien"
                            >
                                <Icon icon="mdi:account-remove" class="h-5 w-5" />
                            </button>
                            <button @click="closePatientDetail" class="p-2 rounded-lg hover:bg-gray-100">
                                <Icon icon="mdi:close" class="h-5 w-5 text-[#1b1b18]/60" />
                            </button>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="flex-1 overflow-y-auto p-4 sm:p-6">
                        <!-- Loading -->
                        <div v-if="loadingDetail" class="py-8 text-center">
                            <Icon icon="mdi:loading" class="mx-auto h-8 w-8 animate-spin text-[#8DD0FC]" />
                        </div>

                        <div v-else-if="patientDetail" class="space-y-6">
                            <!-- Health Overview -->
                            <div>
                                <h4 class="mb-3 flex items-center gap-2 text-[16px] font-semibold text-[#1b1b18]">
                                    <Icon icon="mdi:heart-pulse" class="h-5 w-5 text-[#F4AFE9]" />
                                    Data Kesehatan
                                </h4>
                                <div class="grid gap-3 sm:grid-cols-4">
                                    <div class="rounded-xl bg-[#F8F8F8] p-4 text-center">
                                        <p class="text-[11px] text-[#1b1b18]/50">Usia</p>
                                        <p class="text-[20px] font-bold text-[#1b1b18]">
                                            {{ patientDetail.patient.profile?.age || '-' }}
                                        </p>
                                        <p class="text-[11px] text-[#1b1b18]/40">tahun</p>
                                    </div>
                                    <div class="rounded-xl bg-[#F8F8F8] p-4 text-center">
                                        <p class="text-[11px] text-[#1b1b18]/50">Tinggi</p>
                                        <p class="text-[20px] font-bold text-[#1b1b18]">
                                            {{ patientDetail.patient.profile?.height || '-' }}
                                        </p>
                                        <p class="text-[11px] text-[#1b1b18]/40">cm</p>
                                    </div>
                                    <div class="rounded-xl bg-[#F8F8F8] p-4 text-center">
                                        <p class="text-[11px] text-[#1b1b18]/50">Berat</p>
                                        <p class="text-[20px] font-bold text-[#1b1b18]">
                                            {{ patientDetail.patient.profile?.weight || '-' }}
                                        </p>
                                        <p class="text-[11px] text-[#1b1b18]/40">kg</p>
                                    </div>
                                    <div class="rounded-xl bg-[#F8F8F8] p-4 text-center">
                                        <p class="text-[11px] text-[#1b1b18]/50">BMI</p>
                                        <p 
                                            class="text-[20px] font-bold"
                                            :class="getBmiColor(patientDetail.patient.profile?.bmi_category)"
                                        >
                                            {{ patientDetail.patient.profile?.bmi || '-' }}
                                        </p>
                                        <p 
                                            class="text-[11px]"
                                            :class="getBmiColor(patientDetail.patient.profile?.bmi_category)"
                                        >
                                            {{ patientDetail.patient.profile?.bmi_category || '-' }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Alerts Section -->
                            <div v-if="patientDetail.alerts?.length > 0">
                                <h4 class="mb-3 flex items-center gap-2 text-[16px] font-semibold text-red-600">
                                    <Icon icon="mdi:alert" class="h-5 w-5" />
                                    Alert ({{ patientDetail.alerts.length }})
                                </h4>
                                <div class="space-y-2 max-h-60 overflow-y-auto">
                                    <div 
                                        v-for="alert in patientDetail.alerts"
                                        :key="alert.id"
                                        class="rounded-xl border-l-4 border-red-400 bg-red-50 p-4"
                                    >
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <p class="text-[13px] text-red-800">
                                                    "{{ alert.triggered_text }}"
                                                </p>
                                                <div class="mt-2 flex flex-wrap gap-1">
                                                    <span 
                                                        v-for="keyword in alert.matched_keywords"
                                                        :key="keyword"
                                                        class="rounded-full bg-red-200 px-2 py-0.5 text-[10px] font-medium text-red-700"
                                                    >
                                                        {{ keyword }}
                                                    </span>
                                                </div>
                                            </div>
                                            <span class="text-[11px] text-red-400">
                                                {{ formatDate(alert.created_at) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Recent Chat Sessions -->
                            <div v-if="patientDetail.recent_sessions?.length > 0">
                                <h4 class="mb-3 flex items-center gap-2 text-[16px] font-semibold text-[#1b1b18]">
                                    <Icon icon="mdi:chat-outline" class="h-5 w-5 text-[#8DD0FC]" />
                                    Riwayat Konsultasi Terbaru
                                </h4>
                                <div class="space-y-2 max-h-60 overflow-y-auto">
                                    <div 
                                        v-for="session in patientDetail.recent_sessions"
                                        :key="session.id"
                                        class="rounded-xl bg-[#F8F8F8] p-4"
                                    >
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-[14px] font-medium text-[#1b1b18]">
                                                    {{ session.title }}
                                                </p>
                                                <p class="text-[12px] text-[#1b1b18]/60">
                                                    {{ session.messages_count }} pesan
                                                </p>
                                            </div>
                                            <span class="text-[11px] text-[#1b1b18]/40">
                                                {{ formatDate(session.created_at) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Health Logs -->
                            <div class="grid gap-4 sm:grid-cols-2">
                                <!-- Physical Health -->
                                <div v-if="patientDetail.recent_physical_logs?.length > 0">
                                    <h4 class="mb-3 flex items-center gap-2 text-[14px] font-semibold text-[#1b1b18]">
                                        <Icon icon="mdi:run" class="h-5 w-5 text-[#8DD0FC]" />
                                        Log Kesehatan Fisik
                                    </h4>
                                    <div class="space-y-2 max-h-40 overflow-y-auto">
                                        <div 
                                            v-for="log in patientDetail.recent_physical_logs"
                                            :key="log.id"
                                            class="rounded-lg bg-[#F8F8F8] p-3"
                                        >
                                            <div class="flex items-center justify-between text-[12px]">
                                                <span class="text-[#1b1b18]/60">{{ log.activity_type }}</span>
                                                <span class="text-[#1b1b18]/40">{{ formatDate(log.recorded_at) }}</span>
                                            </div>
                                            <p class="mt-1 text-[13px] font-medium text-[#1b1b18]">
                                                {{ log.duration_minutes }} menit
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Mental Health -->
                                <div v-if="patientDetail.recent_mental_logs?.length > 0">
                                    <h4 class="mb-3 flex items-center gap-2 text-[14px] font-semibold text-[#1b1b18]">
                                        <Icon icon="mdi:brain" class="h-5 w-5 text-[#F4AFE9]" />
                                        Log Kesehatan Mental
                                    </h4>
                                    <div class="space-y-2 max-h-40 overflow-y-auto">
                                        <div 
                                            v-for="log in patientDetail.recent_mental_logs"
                                            :key="log.id"
                                            class="rounded-lg bg-[#F8F8F8] p-3"
                                        >
                                            <div class="flex items-center justify-between text-[12px]">
                                                <span class="text-[#1b1b18]/60">Mood: {{ log.mood_level }}/10</span>
                                                <span class="text-[#1b1b18]/40">{{ formatDate(log.recorded_at) }}</span>
                                            </div>
                                            <p v-if="log.notes" class="mt-1 text-[12px] text-[#1b1b18]">
                                                {{ log.notes }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Teleport>

        <Footer />
    </div>
</template>
