<script setup lang="ts">
import { ref, computed } from 'vue';
import { Icon } from '@iconify/vue';
import axios from 'axios';

interface Props {
    userName?: string;
}

const props = defineProps<Props>();

const emit = defineEmits<{
    (e: 'complete', result: MoodResult): void;
    (e: 'close'): void;
    (e: 'chatWithAi', prompt: string): void;
}>();

interface MoodResult {
    mood: number;
    stressLevel: number;
    sleepHours: number;
    activities: string[];
    feelings: string[];
    averageScore: number;
    summary: string;
    suggestions: string[];
}

// Survey steps
const currentStep = ref(0);
const isAnalyzing = ref(false);
const showResult = ref(false);
const result = ref<MoodResult | null>(null);
const isSaving = ref(false);
const saved = ref(false);

// Survey answers
const selectedMood = ref<number | null>(null);
const selectedStress = ref<number | null>(null);
const selectedSleep = ref<number | null>(null);
const selectedActivities = ref<string[]>([]);
const selectedFeelings = ref<string[]>([]);

// Survey data
const moodOptions = [
    { value: 1, emoji: '😢', label: 'Sangat Buruk' },
    { value: 2, emoji: '😔', label: 'Buruk' },
    { value: 3, emoji: '😐', label: 'Biasa' },
    { value: 4, emoji: '😊', label: 'Baik' },
    { value: 5, emoji: '😄', label: 'Sangat Baik' },
];

const stressOptions = [
    { value: 1, emoji: '😌', label: 'Sangat Tenang' },
    { value: 2, emoji: '🙂', label: 'Cukup Tenang' },
    { value: 3, emoji: '😐', label: 'Normal' },
    { value: 4, emoji: '😰', label: 'Cukup Stres' },
    { value: 5, emoji: '😫', label: 'Sangat Stres' },
];

const sleepOptions = [
    { value: 4, label: '< 4 jam' },
    { value: 5, label: '4-5 jam' },
    { value: 6, label: '5-6 jam' },
    { value: 7, label: '6-7 jam' },
    { value: 8, label: '7-8 jam' },
    { value: 9, label: '> 8 jam' },
];

const activityOptions = [
    { id: 'exercise', icon: 'mdi:run', label: 'Olahraga' },
    { id: 'work', icon: 'mdi:briefcase', label: 'Bekerja' },
    { id: 'study', icon: 'mdi:book-open-variant', label: 'Belajar' },
    { id: 'social', icon: 'mdi:account-group', label: 'Sosial' },
    { id: 'relax', icon: 'mdi:sofa', label: 'Istirahat' },
    { id: 'hobby', icon: 'mdi:palette', label: 'Hobi' },
    { id: 'outdoor', icon: 'mdi:tree', label: 'Luar Ruangan' },
    { id: 'screen', icon: 'mdi:cellphone', label: 'Layar/Gadget' },
];

const feelingOptions = [
    { id: 'happy', emoji: '😊', label: 'Senang' },
    { id: 'calm', emoji: '😌', label: 'Tenang' },
    { id: 'grateful', emoji: '🙏', label: 'Bersyukur' },
    { id: 'anxious', emoji: '😰', label: 'Cemas' },
    { id: 'sad', emoji: '😢', label: 'Sedih' },
    { id: 'angry', emoji: '😠', label: 'Marah' },
    { id: 'tired', emoji: '😴', label: 'Lelah' },
    { id: 'excited', emoji: '🤩', label: 'Semangat' },
    { id: 'lonely', emoji: '😔', label: 'Kesepian' },
    { id: 'hopeful', emoji: '✨', label: 'Penuh Harapan' },
];

const steps = [
    { title: 'Mood Hari Ini', description: 'Bagaimana perasaan Anda secara keseluruhan?' },
    { title: 'Tingkat Stres', description: 'Seberapa stres Anda hari ini?' },
    { title: 'Kualitas Tidur', description: 'Berapa lama Anda tidur semalam?' },
    { title: 'Aktivitas', description: 'Apa saja yang Anda lakukan hari ini? (Pilih beberapa)' },
    { title: 'Perasaan', description: 'Emosi apa yang Anda rasakan? (Pilih beberapa)' },
];

const canProceed = computed(() => {
    switch (currentStep.value) {
        case 0: return selectedMood.value !== null;
        case 1: return selectedStress.value !== null;
        case 2: return selectedSleep.value !== null;
        case 3: return selectedActivities.value.length > 0;
        case 4: return selectedFeelings.value.length > 0;
        default: return false;
    }
});

const progress = computed(() => ((currentStep.value + 1) / steps.length) * 100);

const toggleActivity = (id: string) => {
    const index = selectedActivities.value.indexOf(id);
    if (index > -1) {
        selectedActivities.value.splice(index, 1);
    } else {
        selectedActivities.value.push(id);
    }
};

const toggleFeeling = (id: string) => {
    const index = selectedFeelings.value.indexOf(id);
    if (index > -1) {
        selectedFeelings.value.splice(index, 1);
    } else {
        selectedFeelings.value.push(id);
    }
};

const nextStep = () => {
    if (currentStep.value < steps.length - 1) {
        currentStep.value++;
    } else {
        analyzeResults();
    }
};

const prevStep = () => {
    if (currentStep.value > 0) {
        currentStep.value--;
    }
};

const analyzeResults = async () => {
    isAnalyzing.value = true;
    
    // Calculate average score
    const moodScore = selectedMood.value || 3;
    const stressScore = 6 - (selectedStress.value || 3); // Invert stress (low stress = high score)
    const sleepScore = selectedSleep.value! >= 7 ? 5 : selectedSleep.value! >= 6 ? 4 : selectedSleep.value! >= 5 ? 3 : 2;
    
    const averageScore = Math.round(((moodScore + stressScore + sleepScore) / 3) * 10) / 10;
    
    // Generate summary based on scores
    let summary = '';
    let suggestions: string[] = [];
    
    if (averageScore >= 4) {
        summary = `Selamat ${props.userName || 'kamu'}! 🎉 Kondisi mental dan fisikmu hari ini **sangat baik**. Mood positif, tingkat stres terkendali, dan tidur yang cukup menunjukkan kamu sedang dalam kondisi prima.`;
        suggestions = [
            'Pertahankan pola tidur yang baik',
            'Lanjutkan aktivitas yang membuat Anda bahagia',
            'Bagikan energi positif kepada orang sekitar',
        ];
    } else if (averageScore >= 3) {
        summary = `Hai ${props.userName || 'kamu'}! 😊 Kondisimu hari ini **cukup baik**. Ada beberapa area yang bisa ditingkatkan, tapi secara keseluruhan kamu dalam kondisi yang stabil.`;
        suggestions = [
            'Coba tidur lebih awal malam ini',
            'Luangkan waktu untuk aktivitas yang menyenangkan',
            'Praktikkan teknik pernapasan jika merasa stres',
        ];
    } else if (averageScore >= 2) {
        summary = `Hai ${props.userName || 'kamu'}, sepertinya hari ini **kurang baik** ya. 😔 Tidak apa-apa, semua orang punya hari yang berat. Yang penting adalah mengenali perasaanmu dan mengambil langkah kecil untuk merasa lebih baik.`;
        suggestions = [
            'Istirahat yang cukup sangat penting',
            'Cobalah olahraga ringan seperti jalan kaki 15 menit',
            'Berbicara dengan orang terdekat bisa membantu',
            'Pertimbangkan konsultasi dengan profesional jika berlanjut',
        ];
    } else {
        summary = `${props.userName || 'Kamu'}, sepertinya sedang tidak baik-baik saja. 💙 Kami ada di sini untukmu. Jangan ragu untuk mencari bantuan dari orang terdekat atau profesional kesehatan mental.`;
        suggestions = [
            'Prioritaskan istirahat dan tidur yang cukup',
            'Hindari isolasi, tetap terhubung dengan orang terdekat',
            'Pertimbangkan untuk berbicara dengan psikolog atau konselor',
            'Ingat: meminta bantuan adalah tanda keberanian',
        ];
    }
    
    // Add activity-based suggestions
    if (!selectedActivities.value.includes('exercise')) {
        suggestions.push('Coba tambahkan aktivitas fisik ringan ke rutinitas harian');
    }
    if (selectedActivities.value.includes('screen') && selectedActivities.value.length <= 2) {
        suggestions.push('Kurangi waktu di depan layar, terutama sebelum tidur');
    }
    
    // Simulate API delay for better UX
    await new Promise(resolve => setTimeout(resolve, 1500));
    
    result.value = {
        mood: selectedMood.value!,
        stressLevel: selectedStress.value!,
        sleepHours: selectedSleep.value!,
        activities: selectedActivities.value,
        feelings: selectedFeelings.value,
        averageScore,
        summary,
        suggestions,
    };
    
    isAnalyzing.value = false;
    showResult.value = true;
    
    // Auto-save to health dashboard when results are shown
    await saveToHealthDashboard();
};

const saveToHealthDashboard = async () => {
    if (!result.value) return;
    
    isSaving.value = true;
    try {
        await axios.post('/api/mental-health/log', {
            mood: result.value.mood,
            stress_level: result.value.stressLevel,
            sleep_hours: result.value.sleepHours,
            note: `Aktivitas: ${result.value.activities.join(', ')}. Perasaan: ${result.value.feelings.join(', ')}. Skor rata-rata: ${result.value.averageScore}`,
        });
        saved.value = true;
    } catch (error) {
        console.error('Failed to save mood log:', error);
    } finally {
        isSaving.value = false;
    }
};

const chatWithAi = () => {
    const prompt = `Berdasarkan tracking mood saya hari ini: Mood ${selectedMood.value}/5, Stress ${selectedStress.value}/5, Tidur ${selectedSleep.value} jam. Aktivitas: ${selectedActivities.value.join(', ')}. Perasaan: ${selectedFeelings.value.join(', ')}. Skor rata-rata: ${result.value?.averageScore}. Tolong berikan saran lebih detail untuk meningkatkan kondisi mental saya.`;
    emit('chatWithAi', prompt);
};

const getMoodEmoji = (value: number) => moodOptions.find(m => m.value === value)?.emoji || '😐';
const getStressEmoji = (value: number) => stressOptions.find(s => s.value === value)?.emoji || '😐';

const getScoreColor = (score: number) => {
    if (score >= 4) return 'text-green-600';
    if (score >= 3) return 'text-blue-600';
    if (score >= 2) return 'text-yellow-600';
    return 'text-red-600';
};

const getScoreBg = (score: number) => {
    if (score >= 4) return 'bg-green-100';
    if (score >= 3) return 'bg-blue-100';
    if (score >= 2) return 'bg-yellow-100';
    return 'bg-red-100';
};
</script>

<template>
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="relative w-full max-w-lg rounded-2xl bg-white shadow-xl">
            <!-- Close button -->
            <button 
                @click="emit('close')"
                class="absolute right-4 top-4 text-gray-400 hover:text-gray-600"
            >
                <Icon icon="mdi:close" class="h-5 w-5" />
            </button>

            <!-- Analyzing state -->
            <div v-if="isAnalyzing" class="flex flex-col items-center justify-center p-8">
                <div class="mb-4 h-16 w-16 animate-spin rounded-full border-4 border-[#F4AFE9] border-t-transparent"></div>
                <h3 class="text-lg font-semibold text-[#1b1b18]">Menganalisis hasil...</h3>
                <p class="mt-2 text-sm text-gray-500">Sedang memproses data mood kamu</p>
            </div>

            <!-- Result state -->
            <div v-else-if="showResult && result" class="p-6">
                <h2 class="mb-4 text-center text-xl font-semibold text-[#1b1b18]">Hasil Track Mood 🎯</h2>
                
                <!-- Score card -->
                <div :class="['mb-4 rounded-xl p-4 text-center', getScoreBg(result.averageScore)]">
                    <div class="mb-2 text-4xl">{{ getMoodEmoji(result.mood) }}</div>
                    <div :class="['text-3xl font-bold', getScoreColor(result.averageScore)]">
                        {{ result.averageScore }}/5
                    </div>
                    <p class="mt-1 text-sm text-gray-600">Skor Kesejahteraan</p>
                </div>

                <!-- Summary -->
                <div class="mb-4 rounded-xl bg-gray-50 p-4">
                    <p class="text-sm text-[#1b1b18]" v-html="result.summary.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')"></p>
                </div>

                <!-- Quick stats -->
                <div class="mb-4 grid grid-cols-3 gap-2">
                    <div class="rounded-lg bg-[#F4AFE9]/20 p-2 text-center">
                        <div class="text-lg">{{ getMoodEmoji(result.mood) }}</div>
                        <p class="text-[10px] text-gray-600">Mood</p>
                    </div>
                    <div class="rounded-lg bg-[#8DD0FC]/20 p-2 text-center">
                        <div class="text-lg">{{ getStressEmoji(result.stressLevel) }}</div>
                        <p class="text-[10px] text-gray-600">Stres</p>
                    </div>
                    <div class="rounded-lg bg-[#DDB4F6]/20 p-2 text-center">
                        <div class="text-lg font-semibold text-[#1b1b18]">{{ result.sleepHours }}h</div>
                        <p class="text-[10px] text-gray-600">Tidur</p>
                    </div>
                </div>

                <!-- Suggestions -->
                <div class="mb-4">
                    <h4 class="mb-2 text-sm font-semibold text-[#1b1b18]">💡 Saran untuk Kamu</h4>
                    <ul class="space-y-1">
                        <li v-for="(suggestion, idx) in result.suggestions.slice(0, 4)" :key="idx" class="flex items-start gap-2 text-xs text-gray-600">
                            <Icon icon="mdi:check-circle" class="mt-0.5 h-3 w-3 flex-shrink-0 text-green-500" />
                            {{ suggestion }}
                        </li>
                    </ul>
                </div>

                <!-- Action buttons -->
                <div class="space-y-2">
                    <button
                        v-if="!saved"
                        @click="saveToHealthDashboard"
                        :disabled="isSaving"
                        class="flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#F4AFE9] to-[#8DD0FC] px-4 py-3 text-sm font-medium text-white transition-opacity hover:opacity-90 disabled:opacity-50"
                    >
                        <Icon v-if="isSaving" icon="mdi:loading" class="h-4 w-4 animate-spin" />
                        <Icon v-else icon="mdi:content-save" class="h-4 w-4" />
                        {{ isSaving ? 'Menyimpan...' : 'Simpan ke Health Dashboard' }}
                    </button>
                    <div v-else class="flex items-center justify-center gap-2 rounded-xl bg-green-100 px-4 py-3 text-sm font-medium text-green-700">
                        <Icon icon="mdi:check-circle" class="h-4 w-4" />
                        Tersimpan di Health Dashboard!
                    </div>

                    <button
                        v-if="result.averageScore < 3.5"
                        @click="chatWithAi"
                        class="flex w-full items-center justify-center gap-2 rounded-xl border-2 border-[#8DD0FC] px-4 py-3 text-sm font-medium text-[#1b1b18] transition-colors hover:bg-[#8DD0FC]/10"
                    >
                        <Icon icon="mdi:chat-processing-outline" class="h-4 w-4" />
                        Konsultasi lebih lanjut dengan AI
                    </button>

                    <button
                        @click="emit('close')"
                        class="w-full rounded-xl px-4 py-2.5 text-sm text-gray-500 transition-colors hover:bg-gray-100"
                    >
                        Tutup
                    </button>
                </div>
            </div>

            <!-- Survey steps -->
            <div v-else class="p-6">
                <!-- Progress bar -->
                <div class="mb-6">
                    <div class="mb-2 flex items-center justify-between">
                        <span class="text-xs text-gray-500">Langkah {{ currentStep + 1 }} dari {{ steps.length }}</span>
                        <span class="text-xs font-medium text-[#F4AFE9]">{{ Math.round(progress) }}%</span>
                    </div>
                    <div class="h-2 w-full overflow-hidden rounded-full bg-gray-100">
                        <div 
                            class="h-full rounded-full bg-gradient-to-r from-[#F4AFE9] to-[#8DD0FC] transition-all duration-300"
                            :style="{ width: `${progress}%` }"
                        ></div>
                    </div>
                </div>

                <!-- Step content -->
                <div class="mb-6">
                    <h3 class="mb-1 text-lg font-semibold text-[#1b1b18]">{{ steps[currentStep].title }}</h3>
                    <p class="text-sm text-gray-500">{{ steps[currentStep].description }}</p>
                </div>

                <!-- Step 0: Mood -->
                <div v-if="currentStep === 0" class="mb-6 flex justify-center gap-3">
                    <button
                        v-for="option in moodOptions"
                        :key="option.value"
                        @click="selectedMood = option.value"
                        :class="[
                            'flex flex-col items-center gap-1 rounded-xl p-3 transition-all',
                            selectedMood === option.value 
                                ? 'bg-[#F4AFE9]/30 ring-2 ring-[#F4AFE9]' 
                                : 'bg-gray-50 hover:bg-gray-100'
                        ]"
                    >
                        <span class="text-2xl">{{ option.emoji }}</span>
                        <span class="text-[10px] text-gray-600">{{ option.label }}</span>
                    </button>
                </div>

                <!-- Step 1: Stress -->
                <div v-if="currentStep === 1" class="mb-6 flex justify-center gap-3">
                    <button
                        v-for="option in stressOptions"
                        :key="option.value"
                        @click="selectedStress = option.value"
                        :class="[
                            'flex flex-col items-center gap-1 rounded-xl p-3 transition-all',
                            selectedStress === option.value 
                                ? 'bg-[#8DD0FC]/30 ring-2 ring-[#8DD0FC]' 
                                : 'bg-gray-50 hover:bg-gray-100'
                        ]"
                    >
                        <span class="text-2xl">{{ option.emoji }}</span>
                        <span class="text-[10px] text-gray-600">{{ option.label }}</span>
                    </button>
                </div>

                <!-- Step 2: Sleep -->
                <div v-if="currentStep === 2" class="mb-6 grid grid-cols-3 gap-2">
                    <button
                        v-for="option in sleepOptions"
                        :key="option.value"
                        @click="selectedSleep = option.value"
                        :class="[
                            'rounded-xl p-3 text-center transition-all',
                            selectedSleep === option.value 
                                ? 'bg-[#DDB4F6]/30 ring-2 ring-[#DDB4F6]' 
                                : 'bg-gray-50 hover:bg-gray-100'
                        ]"
                    >
                        <span class="text-sm font-medium text-[#1b1b18]">{{ option.label }}</span>
                    </button>
                </div>

                <!-- Step 3: Activities -->
                <div v-if="currentStep === 3" class="mb-6 grid grid-cols-4 gap-2">
                    <button
                        v-for="option in activityOptions"
                        :key="option.id"
                        @click="toggleActivity(option.id)"
                        :class="[
                            'flex flex-col items-center gap-1 rounded-xl p-3 transition-all',
                            selectedActivities.includes(option.id)
                                ? 'bg-[#8DD0FC]/30 ring-2 ring-[#8DD0FC]' 
                                : 'bg-gray-50 hover:bg-gray-100'
                        ]"
                    >
                        <Icon :icon="option.icon" class="h-5 w-5 text-[#1b1b18]" />
                        <span class="text-[10px] text-gray-600">{{ option.label }}</span>
                    </button>
                </div>

                <!-- Step 4: Feelings -->
                <div v-if="currentStep === 4" class="mb-6 grid grid-cols-5 gap-2">
                    <button
                        v-for="option in feelingOptions"
                        :key="option.id"
                        @click="toggleFeeling(option.id)"
                        :class="[
                            'flex flex-col items-center gap-1 rounded-xl p-2 transition-all',
                            selectedFeelings.includes(option.id)
                                ? 'bg-[#F4AFE9]/30 ring-2 ring-[#F4AFE9]' 
                                : 'bg-gray-50 hover:bg-gray-100'
                        ]"
                    >
                        <span class="text-xl">{{ option.emoji }}</span>
                        <span class="text-[9px] text-gray-600">{{ option.label }}</span>
                    </button>
                </div>

                <!-- Navigation buttons -->
                <div class="flex gap-3">
                    <button
                        v-if="currentStep > 0"
                        @click="prevStep"
                        class="flex-1 rounded-xl border border-gray-200 px-4 py-2.5 text-sm font-medium text-gray-600 transition-colors hover:bg-gray-50"
                    >
                        Kembali
                    </button>
                    <button
                        @click="nextStep"
                        :disabled="!canProceed"
                        class="flex-1 rounded-xl bg-gradient-to-r from-[#F4AFE9] to-[#8DD0FC] px-4 py-2.5 text-sm font-medium text-white transition-opacity hover:opacity-90 disabled:opacity-50"
                    >
                        {{ currentStep === steps.length - 1 ? 'Lihat Hasil' : 'Lanjut' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
