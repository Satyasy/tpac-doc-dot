<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted, computed } from 'vue';

interface Props {
    email: string;
    canResend: boolean;
    waitSeconds: number;
    remainingToday: number;
    nextCooldown: number;
}

const props = defineProps<Props>();
const page = usePage();

const otpInputs = ref<string[]>(['', '', '', '', '', '']);
const inputRefs = ref<HTMLInputElement[]>([]);
const countdown = ref(props.waitSeconds);
const currentCooldown = ref(props.nextCooldown);
const remaining = ref(props.remainingToday);
let countdownInterval: ReturnType<typeof setInterval> | null = null;

const form = useForm({
    otp: '',
});

const resendForm = useForm({});

const canResendNow = computed(() => countdown.value === 0 && remaining.value > 0);

const formatTime = (seconds: number): string => {
    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;
    if (mins > 0) {
        return `${mins}m ${secs}s`;
    }
    return `${secs}s`;
};

const startCountdown = (seconds: number) => {
    countdown.value = seconds;
    if (countdownInterval) {
        clearInterval(countdownInterval);
    }
    countdownInterval = setInterval(() => {
        if (countdown.value > 0) {
            countdown.value--;
        } else {
            if (countdownInterval) {
                clearInterval(countdownInterval);
            }
        }
    }, 1000);
};

const handleInput = (index: number, event: Event) => {
    const target = event.target as HTMLInputElement;
    const value = target.value;

    if (!/^\d*$/.test(value)) {
        target.value = otpInputs.value[index];
        return;
    }

    otpInputs.value[index] = value.slice(-1);

    if (value && index < 5) {
        inputRefs.value[index + 1]?.focus();
    }

    form.otp = otpInputs.value.join('');
};

const handleKeydown = (index: number, event: KeyboardEvent) => {
    if (event.key === 'Backspace' && !otpInputs.value[index] && index > 0) {
        inputRefs.value[index - 1]?.focus();
    }
};

const handlePaste = (event: ClipboardEvent) => {
    event.preventDefault();
    const pastedData = event.clipboardData?.getData('text') || '';
    const digits = pastedData.replace(/\D/g, '').slice(0, 6);

    digits.split('').forEach((digit, index) => {
        if (index < 6) {
            otpInputs.value[index] = digit;
        }
    });

    form.otp = otpInputs.value.join('');
    const lastIndex = Math.min(digits.length, 5);
    inputRefs.value[lastIndex]?.focus();
};

const submit = () => {
    form.post('/verify-otp');
};

const resendOtp = () => {
    if (!canResendNow.value) return;
    
    resendForm.post('/resend-otp', {
        onSuccess: () => {
            remaining.value = Math.max(0, remaining.value - 1);
            startCountdown(currentCooldown.value);
            // Increase cooldown for next time
            const cooldowns = [30, 60, 120, 300, 300];
            const nextIndex = Math.min(5 - remaining.value, cooldowns.length - 1);
            currentCooldown.value = cooldowns[nextIndex];
        },
    });
};

const setInputRef = (el: HTMLInputElement | null, index: number) => {
    if (el) {
        inputRefs.value[index] = el;
    }
};

onMounted(() => {
    if (props.waitSeconds > 0) {
        startCountdown(props.waitSeconds);
    }
});

onUnmounted(() => {
    if (countdownInterval) {
        clearInterval(countdownInterval);
    }
});
</script>

<template>
    <Head title="Verifikasi OTP - DocDot" />

    <div
        class="flex min-h-screen flex-col"
        style="background: linear-gradient(to left, rgba(141, 208, 252, 0.6) 0%, rgba(221, 180, 246, 0.6) 100%)"
    >
        <div class="flex flex-1 items-center justify-center px-4 py-8 sm:px-6 sm:py-12">
            <div class="w-full max-w-lg rounded-2xl bg-white/50 p-6 backdrop-blur-sm sm:rounded-3xl sm:p-10">
                <h1 class="mb-1 text-center text-[22px] font-bold text-[#1b1b18] sm:mb-2 sm:text-[28px]">
                    OTP Verification
                </h1>
                <p class="mb-6 text-center text-[13px] text-[#1b1b18]/70 sm:mb-8 sm:text-[14px]">
                    Please enter the Code that we has sent to your email address
                </p>

                <form @submit.prevent="submit">
                    <div class="mb-8 flex justify-center gap-3">
                        <input
                            v-for="(_, index) in 6"
                            :key="index"
                            :ref="(el) => setInputRef(el as HTMLInputElement, index)"
                            v-model="otpInputs[index]"
                            type="text"
                            maxlength="1"
                            class="h-14 w-14 rounded-xl border-none bg-white text-center text-[24px] font-semibold text-[#1b1b18] shadow-sm focus:outline-none focus:ring-2 focus:ring-[#8DD0FC]"
                            @input="handleInput(index, $event)"
                            @keydown="handleKeydown(index, $event)"
                            @paste="handlePaste"
                        />
                    </div>

                    <p v-if="form.errors.otp" class="mb-4 text-center text-sm text-red-500">
                        {{ form.errors.otp }}
                    </p>

                    <button
                        type="submit"
                        :disabled="form.processing || form.otp.length !== 6"
                        class="w-full rounded-xl bg-[#8DD0FC] py-3 text-[16px] font-medium text-white transition-opacity hover:opacity-90 disabled:opacity-50"
                    >
                        {{ form.processing ? 'Verifying...' : 'Verify' }}
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <p v-if="resendForm.errors.resend" class="mb-2 text-sm text-red-500">
                        {{ resendForm.errors.resend }}
                    </p>
                    
                    <p v-if="remaining > 0" class="text-[14px] text-[#1b1b18]/70">
                        Did not receive the code?
                        <button
                            v-if="canResendNow"
                            type="button"
                            :disabled="resendForm.processing"
                            class="font-medium text-[#FF7CEA] hover:underline disabled:opacity-50"
                            @click="resendOtp"
                        >
                            {{ resendForm.processing ? 'Sending...' : 'resend code' }}
                        </button>
                        <span v-else class="font-medium text-[#1b1b18]/50">
                            wait {{ formatTime(countdown) }}
                        </span>
                    </p>
                    
                    <p v-else class="text-[14px] text-[#1b1b18]/70">
                        Batas resend harian tercapai. <span class="font-medium text-[#FF7CEA]">Coba lagi besok</span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
