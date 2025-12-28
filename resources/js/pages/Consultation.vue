<script setup lang="ts">
import { ref, onMounted, nextTick, computed, watch } from 'vue';
import { Head, usePage, router } from '@inertiajs/vue3';
import { Icon } from '@iconify/vue';
import Navbar from '@/components/Navbar.vue';
import Footer from '@/components/Footer.vue';
import MoodSurvey from '@/components/MoodSurvey.vue';
import axios from 'axios';
import { marked } from 'marked';
import { useScrollAnimation } from '@/composables/useScrollAnimation';

useScrollAnimation();

// Configure marked for safe rendering
marked.setOptions({
    breaks: true,
    gfm: true,
});

// Render markdown to HTML
const renderMarkdown = (text: string): string => {
    return marked.parse(text) as string;
};

interface Message {
    id: number;
    sender: 'user' | 'ai';
    message: string;
    created_at: string;
}

interface Session {
    id: string;
    title: string;
    created_at: string;
}

interface Props {
    sessions: Session[];
    userRole?: 'patient' | 'doctor';
}

const props = defineProps<Props>();
const page = usePage();

const currentSession = ref<Session | null>(null);
const messages = ref<Message[]>([]);
const newMessage = ref('');
const isLoading = ref(false);
const chatContainer = ref<HTMLElement | null>(null);
const showChatMode = ref(false);
const showQuickActions = ref(true);

const user = computed(() => (page.props.auth as any)?.user);
const isLoggedIn = computed(() => !!user.value);
const userRole = computed(() => props.userRole || 'patient');
const isDoctor = computed(() => userRole.value === 'doctor');

// Mood Survey state
const showMoodSurvey = ref(false);

// Quick actions based on role
const quickActionsPatient = [
    { id: 'mood', icon: 'mdi:emoticon-happy-outline', label: 'Track Mood', color: '#F4AFE9', prompt: null, interactive: true },
    { id: 'health', icon: 'mdi:heart-pulse', label: 'Track Kesehatan', color: '#8DD0FC', prompt: 'Saya ingin mencatat kesehatan fisik saya' },
    { id: 'symptoms', icon: 'mdi:stethoscope', label: 'Pantau Gejala', color: '#DDB4F6', prompt: 'Saya ingin konsultasi tentang gejala yang saya rasakan' },
    { id: 'lifestyle', icon: 'mdi:run-fast', label: 'Gaya Hidup', color: '#43B3FC', prompt: 'Berikan saya rekomendasi gaya hidup sehat' },
    { id: 'chat', icon: 'mdi:chat-processing-outline', label: 'Tanya Bebas', color: '#FF7CEA', prompt: null },
];

const quickActionsDoctor = [
    { id: 'analysis', icon: 'mdi:magnify-scan', label: 'Analisis Penyakit', color: '#8DD0FC', prompt: 'Bantu saya analisis differential diagnosis untuk' },
    { id: 'pharma', icon: 'mdi:pill', label: 'Farmakologi', color: '#F4AFE9', prompt: 'Jelaskan tentang farmakologi dan interaksi obat' },
    { id: 'guideline', icon: 'mdi:clipboard-text-outline', label: 'Guideline Klinis', color: '#DDB4F6', prompt: 'Berikan ringkasan guideline klinis terkini tentang' },
    { id: 'research', icon: 'mdi:flask-outline', label: 'Insight Riset', color: '#43B3FC', prompt: 'Berikan informasi berbasis riset tentang' },
    { id: 'chat', icon: 'mdi:chat-processing-outline', label: 'Chat Bebas', color: '#FF7CEA', prompt: null },
];

const quickActions = computed(() => isDoctor.value ? quickActionsDoctor : quickActionsPatient);

// Watch for messages to switch to chat mode
watch(messages, (newMessages) => {
    if (newMessages.length > 0) {
        showChatMode.value = true;
        showQuickActions.value = false;
    }
}, { deep: true });

// Handle quick action click
const handleQuickAction = async (action: typeof quickActionsPatient[0]) => {
    if (action.id === 'chat') {
        // Just switch to chat mode without sending message
        showQuickActions.value = false;
        showChatMode.value = true;
        return;
    }

    // Handle interactive mood tracking
    if (action.id === 'mood') {
        if (!isLoggedIn.value) {
            router.visit('/login');
            return;
        }
        showMoodSurvey.value = true;
        return;
    }

    // For other actions, send the prompt
    if (action.prompt) {
        showQuickActions.value = false;
        showChatMode.value = true;
        newMessage.value = action.prompt;
        await nextTick();
        sendMessage();
    }
};

// Handle mood survey completion
const handleMoodSurveyComplete = (result: any) => {
    showMoodSurvey.value = false;
    // Optionally show a success toast or notification
};

// Handle chat with AI from mood survey
const handleMoodChatWithAi = async (prompt: string) => {
    showMoodSurvey.value = false;
    showQuickActions.value = false;
    showChatMode.value = true;
    
    // Create session if needed
    if (!currentSession.value) {
        await createSession();
    }
    
    newMessage.value = prompt;
    await nextTick();
    sendMessage();
};

// Create new session
const createSession = async () => {
    try {
        const response = await axios.post('/consultation/session');
        currentSession.value = response.data.session;
        messages.value = [];
        return true;
    } catch (error) {
        console.error('Failed to create session:', error);
        return false;
    }
};

// Load messages for a session
const loadMessages = async (session: Session) => {
    currentSession.value = session;
    try {
        const response = await axios.get(`/consultation/session/${session.id}/messages`);
        messages.value = response.data.messages;
        if (response.data.messages.length > 0) {
            showChatMode.value = true;
            showQuickActions.value = false;
        }
        await nextTick();
        scrollToBottom();
    } catch (error) {
        console.error('Failed to load messages:', error);
    }
};

// Send message
const sendMessage = async () => {
    if (!newMessage.value.trim() || isLoading.value) return;

    // If not logged in, save prompt and redirect to login
    if (!isLoggedIn.value) {
        localStorage.setItem('pendingPrompt', newMessage.value);
        router.visit('/login');
        return;
    }

    // Create session if not exists
    if (!currentSession.value) {
        const created = await createSession();
        if (!created) return;
    }

    const messageText = newMessage.value;
    newMessage.value = '';
    isLoading.value = true;
    showChatMode.value = true;
    showQuickActions.value = false;

    // Add user message immediately
    messages.value.push({
        id: Date.now(),
        sender: 'user',
        message: messageText,
        created_at: new Date().toISOString(),
    });

    await nextTick();
    scrollToBottom();

    try {
        const response = await axios.post(`/consultation/session/${currentSession.value!.id}/message`, {
            message: messageText,
        });

        // Replace temp message with real one and add AI response
        messages.value.pop();
        messages.value.push(response.data.user_message);
        messages.value.push(response.data.ai_message);

        await nextTick();
        scrollToBottom();
    } catch (error) {
        console.error('Failed to send message:', error);
        messages.value.pop();
        messages.value.push({
            id: Date.now(),
            sender: 'ai',
            message: 'Maaf, terjadi kesalahan. Silakan coba lagi.',
            created_at: new Date().toISOString(),
        });
    } finally {
        isLoading.value = false;
    }
};

const scrollToBottom = () => {
    if (chatContainer.value) {
        chatContainer.value.scrollTop = chatContainer.value.scrollHeight;
    }
};

const handleKeydown = (e: KeyboardEvent) => {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        sendMessage();
    }
};

// Start new consultation
const startNewChat = () => {
    showChatMode.value = true;
};

// Process pending prompt after login
const processPendingPrompt = async () => {
    const pendingPrompt = localStorage.getItem('pendingPrompt');
    if (pendingPrompt && isLoggedIn.value) {
        localStorage.removeItem('pendingPrompt');
        newMessage.value = pendingPrompt;
        showChatMode.value = true;
        showQuickActions.value = false;
        
        // Auto submit after a short delay
        await nextTick();
        sendMessage();
    }
};

onMounted(async () => {
    if (isLoggedIn.value) {
        // Check for pending prompt first
        const pendingPrompt = localStorage.getItem('pendingPrompt');
        
        if (pendingPrompt) {
            // Create new session and process pending prompt
            await createSession();
            processPendingPrompt();
        } else if (props.sessions.length === 0) {
            await createSession();
        } else {
            await loadMessages(props.sessions[0]);
        }
    }
});
</script>

<template>
    <Head title="Konsultasi - DocDot" />

    <div class="min-h-screen bg-[#FAFAFA] pt-16 sm:pt-20 lg:pt-22">
        <Navbar />

        <!-- Landing Mode: Show when no messages and not in chat mode -->
        <div v-if="!showChatMode" class="overflow-x-hidden">
            <!-- Hero Section -->
            <section class="relative overflow-hidden px-4 pt-6 sm:px-6 sm:pt-8 lg:px-12">
                <!-- Background gradient blob -->
                <div class="absolute top-20 left-1/2 h-[300px] w-[500px] -translate-x-1/2 rounded-full bg-gradient-to-r from-[#8DD0FC]/30 via-[#DDB4F6]/20 to-[#F4AFE9]/30 blur-3xl sm:h-[400px] sm:w-[600px] lg:h-[500px] lg:w-[800px]"></div>

                <div class="relative z-10 mx-auto max-w-4xl pt-8 text-center sm:pt-12 lg:pt-16">
                    <!-- Floating card top right -->
                    <div class="scroll-animate absolute top-4 -right-40 hidden rounded-full bg-white px-6 py-3 text-[16px] font-normal shadow-lg xl:block">
                        Ayo mulai sekarang!
                    </div>

                    <h1 class="scroll-animate text-[24px] font-semibold leading-tight text-[#1b1b18] sm:text-[32px] lg:text-[56px]">
                        Get trusted insights about your<br class="hidden sm:block" />symptoms with <span style="background: linear-gradient(to right, #C360FF 0%, #54BBFF 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">DocDot</span>
                    </h1>

                    <p class="scroll-animate scroll-animate-delay-1 mt-4 text-[14px] font-light text-[#1b1b18]/80 sm:mt-6 sm:text-[16px] lg:text-[20px]">
                        Describe what you're feeling, DocDot will help decode it for you.
                    </p>

                    <!-- Floating card left -->
                    <div class="scroll-animate absolute bottom-50 -left-50 hidden rounded-xl bg-white px-5 py-3 shadow-lg xl:block">
                        <p class="text-[14px] font-semibold text-[#1b1b18]">Konsultasi Sekarang! 🌟 9.10</p>
                    </div>

                    <!-- Get Started Button -->
                    <button 
                        @click="startNewChat"
                        class="scroll-animate scroll-animate-delay-2 mt-6 inline-flex items-center gap-2 rounded-full border-2 border-[#1b1b18] px-6 py-2.5 text-[14px] font-medium text-[#1b1b18] transition-colors hover:bg-[#1b1b18] hover:text-white sm:mt-8 sm:px-8 sm:py-3 sm:text-[18px]"
                    >
                        Get Started
                        <Icon icon="mdi:arrow-right" class="h-4 w-4 sm:h-5 sm:w-5" />
                    </button>

                    <!-- Chat Input Box with gradient border -->
                    <div class="scroll-animate scroll-animate-delay-3 mx-auto mt-8 w-full max-w-[600px] overflow-hidden rounded-xl p-[2px] sm:mt-12" style="background: linear-gradient(to left, #8DD0FC 0%, #DDB4F6 100%);">
                        <div class="overflow-hidden rounded-xl bg-white px-4 py-3 sm:px-6 sm:py-4">
                            <input 
                                v-model="newMessage"
                                @keydown="handleKeydown"
                                type="text" 
                                placeholder="Type your symptoms here..."
                                class="w-full border-none bg-transparent text-[14px] text-[#1b1b18] placeholder-[#1b1b18]/40 outline-none focus:outline-none focus:ring-0 sm:text-[16px]"
                            />
                            <div class="mt-3 flex items-center justify-between sm:mt-4">
                                <div class="flex flex-wrap items-center gap-2 sm:gap-4">
                                    <button class="flex items-center gap-1.5 text-[12px] text-[#54BBFF] sm:gap-2 sm:text-[13px]">
                                        <Icon icon="mdi:auto-fix" class="h-3.5 w-3.5 sm:h-4 sm:w-4" />
                                        Identify symptoms
                                    </button>
                                    <button class="hidden items-center gap-2 text-[13px] text-[#1b1b18]/60 sm:flex">
                                        <Icon icon="mdi:image-outline" class="h-4 w-4" />
                                        Upload photo
                                    </button>
                                </div>
                                <button 
                                    @click="sendMessage"
                                    :disabled="!newMessage.trim()"
                                    class="flex h-7 w-7 items-center justify-center text-[#54BBFF] transition-colors hover:text-[#43A8E8] disabled:opacity-50 sm:h-8 sm:w-8"
                                >
                                    <Icon icon="mdi:send" class="h-4 w-4 sm:h-5 sm:w-5" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- How does it work Section -->
            <section class="relative px-4 py-6 sm:px-6 sm:py-8 lg:mt-30 lg:px-12">
                <div class="scroll-animate mx-auto max-w-6xl overflow-visible rounded-[20px] px-4 pb-12 pt-6 sm:rounded-[30px] sm:px-8 sm:pb-16 sm:pt-8 lg:px-16" style="background: rgba(255, 255, 255, 0.4);">
                    <h2 class="mb-6 text-center text-[22px] font-semibold text-[#1b1b18] sm:mb-8 sm:text-[28px] lg:text-[36px]">How does it work?</h2>
                    
                    <div class="flex flex-col items-center justify-center gap-4 sm:gap-8 lg:flex-row lg:items-start">
                        <!-- Card 1 -->
                        <div class="scroll-animate scroll-animate-delay-1 h-auto w-full max-w-[260px] overflow-hidden rounded-[16px] bg-white p-4 text-center shadow-sm sm:min-h-[280px] sm:rounded-[20px] sm:p-6 lg:mt-8 lg:h-[300px]">
                            <Icon icon="mingcute:search-line" class="mx-auto mb-3 h-8 w-8 text-[#9EC9FB] sm:mb-4 sm:h-10 sm:w-10" />
                            <h3 class="mb-2 text-[14px] font-semibold text-[#1b1b18] sm:mb-3 sm:text-[16px]">Input Gejala atau Pertanyaan</h3>
                            <p class="text-[12px] leading-relaxed text-[#1b1b18]/70 sm:text-[13px]">
                                Pengguna cukup mengetik keluhan atau pertanyaan kesehatan, misalnya "sakit kepala", "demam sejak kemarin", atau "tips pola makan sehat untuk remaja."
                            </p>
                        </div>

                        <!-- Card 2 (higher) -->
                        <div class="scroll-animate scroll-animate-delay-2 h-auto w-full max-w-[260px] overflow-hidden rounded-[16px] bg-white p-4 text-center shadow-sm sm:min-h-[280px] sm:rounded-[20px] sm:p-6 lg:-mt-4 lg:h-[300px]">
                            <Icon icon="mage:robot-wink" class="mx-auto mb-3 h-8 w-8 text-[#9EC9FB] sm:mb-4 sm:h-10 sm:w-10" />
                            <h3 class="mb-2 text-[14px] font-semibold text-[#1b1b18] sm:mb-3 sm:text-[16px]">Analisis dengan AI Medis</h3>
                            <p class="text-[12px] leading-relaxed text-[#1b1b18]/70 sm:text-[13px]">
                                DocDot memproses input dengan teknologi AI, membandingkan dengan data medis terpercaya, lalu memberikan informasi seputar kemungkinan penyebab, tips perawatan awal, dan rekomendasi gaya hidup.
                            </p>
                        </div>

                        <!-- Card 3 -->
                        <div class="scroll-animate scroll-animate-delay-3 h-auto w-full max-w-[260px] overflow-hidden rounded-[16px] bg-white p-4 text-center shadow-sm sm:min-h-[280px] sm:rounded-[20px] sm:p-6 lg:mt-8 lg:h-[300px]">
                            <Icon icon="mdi:clipboard-text-outline" class="mx-auto mb-3 h-8 w-8 text-[#CCBAF8] sm:mb-4 sm:h-10 sm:w-10" />
                            <h3 class="mb-2 text-[14px] font-semibold text-[#1b1b18] sm:mb-3 sm:text-[16px]">Saran & Rekomendasi Lanjutan</h3>
                            <p class="text-[12px] leading-relaxed text-[#1b1b18]/70 sm:text-[13px]">
                                DocDot menampilkan hasil analisis dalam bahasa yang mudah dipahami. Jika gejala serius, chatbot akan menyarankan pengguna untuk segera berkonsultasi dengan tenaga medis profesional.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Testimonial Section -->
            <section class="mt-8 mb-8 w-full py-8 sm:mt-16 sm:mb-16 sm:py-12" style="background: rgba(255, 255, 255, 0.3);">
                <div class="px-4 sm:px-6 lg:px-12">
                    <h2 class="scroll-animate mb-6 text-left text-[22px] font-semibold text-[#1b1b18] sm:mb-10 sm:text-[28px] lg:text-[32px]">
                        Testimonial dari Pengguna <span class="text-[#54BBFF]">DocDot</span>
                    </h2>
                    
                    <div class="scroll-animate scroll-animate-delay-1 grid gap-4 sm:grid-cols-2 sm:gap-6 lg:grid-cols-3">
                        <!-- Card 1 -->
                        <div class="rounded-[16px] bg-[#FAF1FF] p-4 sm:rounded-[20px] sm:p-6">
                            <div class="mb-3 flex items-center justify-between sm:mb-4">
                                <div class="flex items-center gap-2 sm:gap-3">
                                    <img src="https://i.pravatar.cc/48?img=11" class="h-10 w-10 rounded-full object-cover sm:h-12 sm:w-12" />
                                    <div>
                                        <p class="text-[14px] font-semibold text-[#1b1b18] sm:text-[16px]">Andi Pratama</p>
                                        <p class="text-[11px] text-[#1b1b18]/60 sm:text-[12px]">@andipratama</p>
                                    </div>
                                </div>
                                <div class="flex gap-0.5 sm:gap-1">
                                    <Icon icon="mdi:star" class="h-3.5 w-3.5 text-[#FFD700] sm:h-4 sm:w-4" />
                                    <Icon icon="mdi:star" class="h-3.5 w-3.5 text-[#FFD700] sm:h-4 sm:w-4" />
                                    <Icon icon="mdi:star" class="h-3.5 w-3.5 text-[#FFD700] sm:h-4 sm:w-4" />
                                    <Icon icon="mdi:star" class="h-3.5 w-3.5 text-[#FFD700] sm:h-4 sm:w-4" />
                                    <Icon icon="mdi:star" class="h-3.5 w-3.5 text-[#FFD700] sm:h-4 sm:w-4" />
                                </div>
                            </div>
                            <p class="text-[13px] leading-relaxed text-[#1b1b18]/80 sm:text-[14px]">
                                DocDot sangat membantu saya memahami gejala yang saya rasakan. Penjelasannya detail dan mudah dipahami, plus selalu mengingatkan untuk konsultasi ke dokter jika perlu.
                            </p>
                        </div>

                        <!-- Card 2 -->
                        <div class="rounded-[16px] bg-[#FAF1FF] p-4 sm:rounded-[20px] sm:p-6">
                            <div class="mb-3 flex items-center justify-between sm:mb-4">
                                <div class="flex items-center gap-2 sm:gap-3">
                                    <img src="https://i.pravatar.cc/48?img=12" class="h-10 w-10 rounded-full object-cover sm:h-12 sm:w-12" />
                                    <div>
                                        <p class="text-[14px] font-semibold text-[#1b1b18] sm:text-[16px]">Siti Rahayu</p>
                                        <p class="text-[11px] text-[#1b1b18]/60 sm:text-[12px]">@sitirahayu</p>
                                    </div>
                                </div>
                                <div class="flex gap-0.5 sm:gap-1">
                                    <Icon icon="mdi:star" class="h-3.5 w-3.5 text-[#FFD700] sm:h-4 sm:w-4" />
                                    <Icon icon="mdi:star" class="h-3.5 w-3.5 text-[#FFD700] sm:h-4 sm:w-4" />
                                    <Icon icon="mdi:star" class="h-3.5 w-3.5 text-[#FFD700] sm:h-4 sm:w-4" />
                                    <Icon icon="mdi:star" class="h-3.5 w-3.5 text-[#FFD700] sm:h-4 sm:w-4" />
                                    <Icon icon="mdi:star" class="h-3.5 w-3.5 text-[#FFD700] sm:h-4 sm:w-4" />
                                </div>
                            </div>
                            <p class="text-[13px] leading-relaxed text-[#1b1b18]/80 sm:text-[14px]">
                                Sebagai ibu rumah tangga, DocDot sangat berguna untuk mendapat informasi awal tentang kesehatan keluarga. Responnya cepat dan informatif!
                            </p>
                        </div>

                        <!-- Card 3 -->
                        <div class="rounded-[16px] bg-[#FAF1FF] p-4 sm:col-span-2 sm:rounded-[20px] sm:p-6 lg:col-span-1">
                            <div class="mb-3 flex items-center justify-between sm:mb-4">
                                <div class="flex items-center gap-2 sm:gap-3">
                                    <img src="https://i.pravatar.cc/48?img=13" class="h-10 w-10 rounded-full object-cover sm:h-12 sm:w-12" />
                                    <div>
                                        <p class="text-[14px] font-semibold text-[#1b1b18] sm:text-[16px]">Budi Santoso</p>
                                        <p class="text-[11px] text-[#1b1b18]/60 sm:text-[12px]">@budisantoso</p>
                                    </div>
                                </div>
                                <div class="flex gap-0.5 sm:gap-1">
                                    <Icon icon="mdi:star" class="h-3.5 w-3.5 text-[#FFD700] sm:h-4 sm:w-4" />
                                    <Icon icon="mdi:star" class="h-3.5 w-3.5 text-[#FFD700] sm:h-4 sm:w-4" />
                                    <Icon icon="mdi:star" class="h-3.5 w-3.5 text-[#FFD700] sm:h-4 sm:w-4" />
                                    <Icon icon="mdi:star" class="h-3.5 w-3.5 text-[#FFD700] sm:h-4 sm:w-4" />
                                    <Icon icon="mdi:star-half-full" class="h-3.5 w-3.5 text-[#FFD700] sm:h-4 sm:w-4" />
                                </div>
                            </div>
                            <p class="text-[13px] leading-relaxed text-[#1b1b18]/80 sm:text-[14px]">
                                Fitur konsultasi 24 jam sangat membantu ketika butuh informasi kesehatan di malam hari. Terima kasih DocDot!
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <Footer />
        </div>

        <!-- Chat Mode: Show when in chat mode -->
        <div v-else class="flex h-[calc(100vh-72px)] flex-col px-4 pb-4 sm:h-[calc(100vh-80px)] sm:px-6 sm:pb-6 lg:px-12">
            <!-- Chat Messages Area -->
            <div 
                ref="chatContainer"
                class="flex-1 overflow-y-auto py-4 sm:py-8"
            >
                <div class="mx-auto max-w-4xl space-y-4 sm:space-y-6">
                    <!-- Welcome message with Quick Actions if no messages -->
                    <div v-if="messages.length === 0 && showQuickActions" class="flex flex-col items-center justify-center py-8 sm:py-12">
                        <h2 class="mb-2 text-xl font-semibold text-[#1b1b18] sm:text-2xl">
                            Hallo{{ user?.name ? `, ${user.name}` : '' }}! 👋
                        </h2>
                        <p class="mb-6 text-center text-[14px] text-[#1b1b18]/70 sm:mb-8 sm:text-base">
                            {{ isDoctor ? 'Pilih aksi cepat atau mulai konsultasi medis' : 'Apa yang ingin Anda lakukan hari ini?' }}
                        </p>
                        
                        <!-- Quick Actions Grid -->
                        <div class="grid w-full max-w-2xl grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-4 lg:grid-cols-5">
                            <button
                                v-for="action in quickActions"
                                :key="action.id"
                                @click="handleQuickAction(action)"
                                class="group flex flex-col items-center gap-2 rounded-xl bg-white p-4 shadow-sm transition-all hover:-translate-y-1 hover:shadow-md sm:gap-3 sm:rounded-2xl sm:p-5"
                            >
                                <div 
                                    class="flex h-12 w-12 items-center justify-center rounded-full transition-transform group-hover:scale-110 sm:h-14 sm:w-14"
                                    :style="{ backgroundColor: `${action.color}20` }"
                                >
                                    <Icon 
                                        :icon="action.icon" 
                                        class="h-6 w-6 sm:h-7 sm:w-7"
                                        :style="{ color: action.color }"
                                    />
                                </div>
                                <span class="text-center text-[12px] font-medium text-[#1b1b18] sm:text-[13px]">
                                    {{ action.label }}
                                </span>
                            </button>
                        </div>

                        <!-- Role indicator for doctors -->
                        <div v-if="isDoctor" class="mt-6 flex items-center gap-2 rounded-full bg-blue-50 px-4 py-2 sm:mt-8">
                            <Icon icon="mdi:stethoscope" class="h-4 w-4 text-blue-600" />
                            <span class="text-[12px] font-medium text-blue-700 sm:text-[13px]">Mode Dokter Aktif</span>
                        </div>
                    </div>

                    <!-- Simple welcome if quick actions hidden but no messages -->
                    <div v-else-if="messages.length === 0" class="flex flex-col items-center justify-center py-12 sm:py-20">
                        <h2 class="mb-2 text-xl font-semibold text-[#1b1b18] sm:text-2xl">
                            Hallo{{ user?.name ? `, ${user.name}` : '' }}!
                        </h2>
                        <p class="text-center text-[14px] text-[#1b1b18]/70 sm:text-base">
                            {{ isDoctor ? 'Ketik pertanyaan klinis atau riset Anda' : 'Ceritakan gejala atau keluhan kesehatan Anda' }}
                        </p>
                        <p v-if="!isLoggedIn" class="mt-2 text-center text-xs text-[#1b1b18]/50 sm:text-sm">
                            Silakan login untuk memulai konsultasi
                        </p>
                    </div>

                    <!-- Messages -->
                    <template v-for="msg in messages" :key="msg.id">
                        <!-- User Message (right side with tail at top) -->
                        <div v-if="msg.sender === 'user'" class="flex justify-end">
                            <div class="relative max-w-[85%] sm:max-w-[70%]">
                                <div class="rounded-2xl rounded-tr-md bg-[#8DD0FC]/50 px-4 py-3 sm:px-5 sm:py-4">
                                    <p class="text-[14px] text-[#1b1b18] sm:text-[15px]">{{ msg.message }}</p>
                                </div>
                                <!-- Tail pointing right top -->
                                <div 
                                    class="absolute -right-2 top-0 h-4 w-4 bg-[#8DD0FC]/50"
                                    style="clip-path: polygon(0 0, 100% 0, 0 100%)"
                                ></div>
                            </div>
                        </div>

                        <!-- AI Message (left side with tail at top) -->
                        <div v-else class="flex justify-start">
                            <div class="relative max-w-[85%] sm:max-w-[70%]">
                                <div class="rounded-2xl rounded-tl-md bg-[#DDB4F6]/50 px-4 py-3 sm:px-5 sm:py-4">
                                    <div 
                                        class="prose prose-sm max-w-none text-[14px] text-[#1b1b18] prose-p:my-2 prose-ul:my-2 prose-ol:my-2 prose-li:my-0.5 prose-headings:text-[#1b1b18] prose-strong:text-[#1b1b18] prose-em:text-[#1b1b18] sm:text-[15px]"
                                        v-html="renderMarkdown(msg.message)"
                                    ></div>
                                    <!-- Disclaimer -->
                                    <div class="mt-2 border-t border-[#1b1b18]/10 pt-2 sm:mt-3">
                                        <p class="flex items-start gap-1.5 text-[10px] italic text-[#1b1b18]/60 sm:items-center sm:text-[11px]">
                                            <Icon icon="mdi:information-outline" class="h-3 w-3 flex-shrink-0 sm:h-3.5 sm:w-3.5" />
                                            <span>Informasi ini hanya sebagai referensi dan tidak menggantikan konsultasi dengan dokter profesional.</span>
                                        </p>
                                    </div>
                                </div>
                                <!-- Tail pointing left top -->
                                <div 
                                    class="absolute -left-2 top-0 h-4 w-4 bg-[#DDB4F6]/50"
                                    style="clip-path: polygon(0 0, 100% 0, 100% 100%)"
                                ></div>
                            </div>
                        </div>
                    </template>

                    <!-- Loading indicator -->
                    <div v-if="isLoading" class="flex justify-start">
                        <div class="relative">
                            <div class="rounded-2xl rounded-tl-md bg-[#DDB4F6]/50 px-5 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="h-2 w-2 animate-bounce rounded-full bg-[#1b1b18]/50" style="animation-delay: 0ms"></div>
                                    <div class="h-2 w-2 animate-bounce rounded-full bg-[#1b1b18]/50" style="animation-delay: 150ms"></div>
                                    <div class="h-2 w-2 animate-bounce rounded-full bg-[#1b1b18]/50" style="animation-delay: 300ms"></div>
                                </div>
                            </div>
                            <!-- Tail pointing left top -->
                            <div 
                                class="absolute -left-2 top-0 h-4 w-4 bg-[#DDB4F6]/50"
                                style="clip-path: polygon(0 0, 100% 0, 100% 100%)"
                            ></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Input Area -->
            <div class="mx-auto w-full max-w-4xl">
                <div class="rounded-xl bg-white p-3 shadow-lg sm:rounded-2xl sm:p-4">
                    <!-- Doctor mode indicator -->
                    <div v-if="isDoctor" class="mb-2 flex items-center gap-2 rounded-lg bg-blue-50 px-3 py-1.5 sm:mb-3">
                        <Icon icon="mdi:stethoscope" class="h-3.5 w-3.5 text-blue-600 sm:h-4 sm:w-4" />
                        <span class="text-[11px] text-blue-700 sm:text-[12px]">Mode Dokter: Respons akan lebih teknis dan profesional</span>
                    </div>

                    <div class="mb-2 sm:mb-3">
                        <textarea
                            v-model="newMessage"
                            @keydown="handleKeydown"
                            :placeholder="isDoctor ? 'Ketik pertanyaan klinis atau riset...' : 'Type your symptoms here...'"
                            rows="2"
                            class="w-full resize-none border-none bg-transparent text-[14px] text-[#1b1b18] placeholder-[#1b1b18]/50 focus:outline-none sm:text-[15px]"
                        ></textarea>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div class="flex flex-wrap items-center gap-2 sm:gap-4">
                            <button 
                                v-if="!showQuickActions && messages.length > 0"
                                @click="showQuickActions = true; messages = [];"
                                class="flex items-center gap-1.5 text-[12px] text-[#1b1b18]/70 transition-colors hover:text-[#1b1b18] sm:gap-2 sm:text-[13px]"
                            >
                                <Icon icon="mdi:restart" class="h-4 w-4 sm:h-5 sm:w-5" />
                                <span class="hidden sm:inline">New Chat</span>
                            </button>
                            <button class="flex items-center gap-1.5 text-[12px] text-[#1b1b18]/70 transition-colors hover:text-[#1b1b18] sm:gap-2 sm:text-[13px]">
                                <Icon icon="mdi:magnify" class="h-4 w-4 sm:h-5 sm:w-5" />
                                <span class="hidden sm:inline">{{ isDoctor ? 'Cari referensi' : 'Identify symptoms' }}</span>
                            </button>
                        </div>
                        
                        <button 
                            @click="sendMessage"
                            :disabled="!newMessage.trim() || isLoading"
                            class="flex h-9 w-9 items-center justify-center rounded-full bg-gradient-to-r from-[#F4AFE9] to-[#8DD0FC] transition-opacity hover:opacity-90 disabled:opacity-50 sm:h-10 sm:w-10"
                        >
                            <Icon icon="mdi:send" class="h-4 w-4 text-white sm:h-5 sm:w-5" />
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mood Survey Modal -->
        <MoodSurvey 
            v-if="showMoodSurvey"
            :user-name="user?.name"
            @close="showMoodSurvey = false"
            @complete="handleMoodSurveyComplete"
            @chat-with-ai="handleMoodChatWithAi"
        />
    </div>
</template>
