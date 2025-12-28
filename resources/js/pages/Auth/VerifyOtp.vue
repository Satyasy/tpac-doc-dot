<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted, computed } from 'vue';

interface Props {
    email: string;
    canResend: boolean;
    waitSeconds: number;
    remainingToday: number;
    nextCooldown: number;
    otpExpiresIn?: number; // seconds until OTP expires (default 600 = 10 min)
}

const props = defineProps<Props>();
const page = usePage();

const otpInputs = ref<string[]>(['', '', '', '', '', '']);
const inputRefs = ref<HTMLInputElement[]>([]);
const countdown = ref(props.waitSeconds);
const currentCooldown = ref(props.nextCooldown);
const remaining = ref(props.remainingToday);
const otpExpireCountdown = ref(props.otpExpiresIn || 600); // 10 minutes default
let countdownInterval: ReturnType<typeof setInterval> | null = null;
let expireInterval: ReturnType<typeof setInterval> | null = null;

const form = useForm({
    otp: '',
});

const resendForm = useForm({});

const canResendNow = computed(() => countdown.value === 0 && remaining.value > 0);
const isOtpExpired = computed(() => otpExpireCountdown.value <= 0);

const formatTime = (seconds: number): string => {
    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;
    if (mins > 0) {
        return `${mins}m ${secs}s`;
    }
    return `${secs}s`;
};

const formatExpireTime = (seconds: number): string => {
    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
};

const expireProgressPercent = computed(() => {
    const total = props.otpExpiresIn || 600;
    return (otpExpireCountdown.value / total) * 100;
});

const expireColorClass = computed(() => {
    if (otpExpireCountdown.value <= 60) return 'text-red-500'; // Last 1 minute
    if (otpExpireCountdown.value <= 180) return 'text-orange-500'; // Last 3 minutes
    return 'text-[#1b1b18]/70';
});

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

const startExpireCountdown = () => {
    if (expireInterval) {
        clearInterval(expireInterval);
    }
    expireInterval = setInterval(() => {
        if (otpExpireCountdown.value > 0) {
            otpExpireCountdown.value--;
        } else {
            if (expireInterval) {
                clearInterval(expireInterval);
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
            // Reset OTP expire countdown
            otpExpireCountdown.value = props.otpExpiresIn || 600;
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
    // Start OTP expire countdown
    startExpireCountdown();
});

onUnmounted(() => {
    if (countdownInterval) {
        clearInterval(countdownInterval);
    }
    if (expireInterval) {
        clearInterval(expireInterval);
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
                <p class="mb-4 text-center text-[13px] text-[#1b1b18]/70 sm:mb-6 sm:text-[14px]">
                    Please enter the Code that we has sent to your email address
                </p>

                <!-- OTP Expire Countdown -->
                <div class="mb-6 rounded-xl bg-white/70 p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[12px] text-[#1b1b18]/60 sm:text-[13px]">Kode akan kadaluarsa dalam:</span>
                        <span :class="['text-[14px] font-bold sm:text-[16px]', expireColorClass]">
                            {{ isOtpExpired ? 'Expired!' : formatExpireTime(otpExpireCountdown) }}
                        </span>
                    </div>
                    <div class="h-2 w-full overflow-hidden rounded-full bg-gray-200">
                        <div 
                            class="h-full rounded-full transition-all duration-1000"
                            :class="otpExpireCountdown <= 60 ? 'bg-red-500' : otpExpireCountdown <= 180 ? 'bg-orange-500' : 'bg-gradient-to-r from-[#8DD0FC] to-[#F4AFE9]'"
                            :style="{ width: `${expireProgressPercent}%` }"
                        ></div>
                    </div>
                    <p v-if="isOtpExpired" class="mt-2 text-center text-[12px] text-red-500">
                        Kode OTP sudah kadaluarsa. Silakan kirim ulang kode baru.
                    </p>
                </div>

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
                        :disabled="form.processing || form.otp.length !== 6 || isOtpExpired"
                        class="w-full rounded-xl bg-[#8DD0FC] py-3 text-[16px] font-medium text-white transition-opacity hover:opacity-90 disabled:opacity-50"
                    >
                        {{ form.processing ? 'Verifying...' : isOtpExpired ? 'Code Expired' : 'Verify' }}
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
