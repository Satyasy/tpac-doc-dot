import { ref, onMounted, onUnmounted } from 'vue';

export function useScrollAnimation() {
    const observerRef = ref<IntersectionObserver | null>(null);

    const initScrollAnimation = () => {
        observerRef.value = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-fade-in-up');
                        entry.target.classList.remove('opacity-0', 'translate-y-8');
                    }
                });
            },
            {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px',
            }
        );

        // Observe all elements with scroll-animate class
        document.querySelectorAll('.scroll-animate').forEach((el) => {
            observerRef.value?.observe(el);
        });
    };

    const cleanup = () => {
        observerRef.value?.disconnect();
    };

    onMounted(() => {
        // Small delay to ensure DOM is ready
        setTimeout(initScrollAnimation, 100);
    });

    onUnmounted(() => {
        cleanup();
    });

    return {
        initScrollAnimation,
        cleanup,
    };
}
