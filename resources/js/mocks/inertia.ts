import { reactive, ref, watch, defineComponent, h, computed } from 'vue';

// Global reactive page state to mock Inertia page structure
export const pageState = reactive({
    url: window.location.pathname,
    props: {
        auth: {
            user: JSON.parse(localStorage.getItem('docdot_user') || 'null') as any,
        },
        flash: {
            success: null as string | null,
            error: null as string | null,
        }
    }
});

// Reactively update localStorage when user log state changes
watch(() => pageState.props.auth.user, (newUser) => {
    if (newUser) {
        localStorage.setItem('docdot_user', JSON.stringify(newUser));
    } else {
        localStorage.removeItem('docdot_user');
    }
}, { deep: true });

// Mock usePage hook
export function usePage<T = any>() {
    return pageState as any;
}

// Simple client-side router
export const currentPath = ref(window.location.pathname);

export const router = {
    visit(url: string, options?: any) {
        console.log(`[Mock Router VISIT]: ${url}`, options);
        
        const cleanUrl = url.split('?')[0];
        window.history.pushState({}, '', url);
        currentPath.value = cleanUrl;
        pageState.url = cleanUrl;
        window.scrollTo(0, 0);

        if (options?.onSuccess) {
            options.onSuccess();
        }
        if (options?.onFinish) {
            options.onFinish();
        }
    },
    get(url: string, data?: any, options?: any) {
        console.log(`[Mock Router GET]: ${url}`, data, options);
        // Map query params for static articles or catalogs if needed
        if (url.startsWith('/article')) {
            let redirectUrl = '/article';
            const params = [];
            if (data?.category) params.push(`category=${encodeURIComponent(data.category)}`);
            if (data?.search) params.push(`search=${encodeURIComponent(data.search)}`);
            if (params.length > 0) {
                redirectUrl += '?' + params.join('&');
            }
            this.visit(redirectUrl, options);
        } else if (url.startsWith('/drug-catalog')) {
            let redirectUrl = '/drug-catalog';
            const params = [];
            if (data?.category) params.push(`category=${encodeURIComponent(data.category)}`);
            if (data?.search) params.push(`search=${encodeURIComponent(data.search)}`);
            if (params.length > 0) {
                redirectUrl += '?' + params.join('&');
            }
            this.visit(redirectUrl, options);
        } else {
            this.visit(url, options);
        }
    },
    post(url: string, data?: any, options?: any) {
        console.log(`[Mock Router POST]: ${url}`, data, options);
        if (url === '/logout') {
            pageState.props.auth.user = null;
            pageState.props.flash.success = 'Anda telah berhasil logout.';
            this.visit('/');
        } else if (options?.onSuccess) {
            options.onSuccess();
        }
        if (options?.onFinish) {
            options.onFinish();
        }
    },
    put(url: string, data?: any, options?: any) {
        console.log(`[Mock Router PUT]: ${url}`, data, options);
        if (options?.onSuccess) options.onSuccess();
        if (options?.onFinish) options.onFinish();
    },
    delete(url: string, options?: any) {
        console.log(`[Mock Router DELETE]: ${url}`, options);
        if (options?.onSuccess) options.onSuccess();
        if (options?.onFinish) options.onFinish();
    },
    reload(options?: any) {
        console.log(`[Mock Router RELOAD]`);
        if (options?.onSuccess) options.onSuccess();
        if (options?.onFinish) options.onFinish();
    }
};

// Listen to popstate event for back and forward browser buttons
window.addEventListener('popstate', () => {
    const cleanUrl = window.location.pathname;
    currentPath.value = cleanUrl;
    pageState.url = cleanUrl;
});

// Mock Link component
export const Link = defineComponent({
    name: 'Link',
    props: {
        href: { type: String, required: true },
        method: { type: String, default: 'get' },
        as: { type: String, default: 'a' },
    },
    setup(props, { slots, attrs }) {
        const handleClick = (e: MouseEvent) => {
            // Prevent default only if it's an internal SPA link (starts with / or has no http/domain)
            if (!props.href.startsWith('http') && !props.href.startsWith('//') && !attrs.target) {
                e.preventDefault();
                if (props.method.toLowerCase() === 'post') {
                    router.post(props.href);
                } else {
                    router.visit(props.href);
                }
            }
        };

        return () => h(
            props.as,
            {
                ...attrs,
                href: props.href,
                onClick: handleClick
            },
            slots.default ? slots.default() : null
        );
    }
});

// Mock Head component
export const Head = defineComponent({
    name: 'Head',
    props: {
        title: { type: String },
    },
    setup(props, { slots }) {
        watch(() => props.title, (newTitle) => {
            if (newTitle) {
                document.title = newTitle;
            }
        }, { immediate: true });

        // Try to read nested slot text for title
        if (slots.default) {
            const vnodes = slots.default();
            const titleNode = vnodes.find(v => v.type === 'title');
            if (titleNode && typeof titleNode.children === 'string') {
                document.title = titleNode.children;
            }
        }

        return () => null;
    }
});

// Mock useForm hook
export function useForm(initialValues: any) {
    const dataObj = typeof initialValues === 'object' ? { ...initialValues } : {};
    
    const formState = reactive({
        ...dataObj,
        errors: {} as Record<string, string>,
        processing: false,
        wasSuccessful: false,
        post(url: string, options?: any) {
            this.processing = true;
            this.errors = {};
            setTimeout(() => {
                this.processing = false;
                
                // Simulate authentication flows
                if (url === '/login') {
                    if (!this.email) {
                        this.errors.email = 'Alamat email wajib diisi.';
                        if (options?.onError) options.onError(this.errors);
                        return;
                    }
                    if (!this.password) {
                        this.errors.password = 'Password wajib diisi.';
                        if (options?.onError) options.onError(this.errors);
                        return;
                    }
                    
                    // Success mock login
                    pageState.props.auth.user = {
                        id: 123,
                        name: this.email.split('@')[0].toUpperCase(),
                        email: this.email,
                        email_verified_at: new Date().toISOString(),
                        photo_profile: null,
                        roles: [{ name: 'patient' }]
                    };
                    pageState.props.flash.success = 'Login berhasil! Selamat datang kembali.';
                    
                    if (options?.onSuccess) options.onSuccess();
                    router.visit('/health-dashboard');
                    
                } else if (url === '/register') {
                    if (!this.name) {
                        this.errors.name = 'Nama lengkap wajib diisi.';
                    }
                    if (!this.email) {
                        this.errors.email = 'Alamat email wajib diisi.';
                    }
                    if (!this.password) {
                        this.errors.password = 'Password wajib diisi.';
                    }
                    
                    if (Object.keys(this.errors).length > 0) {
                        if (options?.onError) options.onError(this.errors);
                        return;
                    }
                    
                    // Success mock register
                    pageState.props.auth.user = {
                        id: 124,
                        name: this.name,
                        email: this.email,
                        email_verified_at: new Date().toISOString(),
                        photo_profile: null,
                        roles: [{ name: 'patient' }]
                    };
                    pageState.props.flash.success = 'Pendaftaran berhasil! Akun Anda siap digunakan.';
                    
                    if (options?.onSuccess) options.onSuccess();
                    router.visit('/health-dashboard');
                    
                } else if (url.includes('/forgot-password')) {
                    if (!this.email) {
                        this.errors.email = 'Alamat email wajib diisi.';
                        if (options?.onError) options.onError(this.errors);
                        return;
                    }
                    pageState.props.flash.success = 'Link reset password telah dikirim ke email Anda.';
                    if (options?.onSuccess) options.onSuccess();
                    router.visit('/login');
                } else {
                    // Other general form posts
                    this.wasSuccessful = true;
                    if (options?.onSuccess) options.onSuccess();
                }
                
                if (options?.onFinish) options.onFinish();
            }, 800);
        },
        put(url: string, options?: any) {
            this.processing = true;
            setTimeout(() => {
                this.processing = false;
                this.wasSuccessful = true;
                
                if (url === '/profile') {
                    // Update mock user details
                    if (pageState.props.auth.user) {
                        pageState.props.auth.user.name = this.name || pageState.props.auth.user.name;
                        pageState.props.auth.user.email = this.email || pageState.props.auth.user.email;
                        pageState.props.flash.success = 'Profil Anda berhasil diperbarui!';
                    }
                }
                
                if (options?.onSuccess) options.onSuccess();
                if (options?.onFinish) options.onFinish();
            }, 800);
        },
        delete(url: string, options?: any) {
            this.processing = true;
            setTimeout(() => {
                this.processing = false;
                this.wasSuccessful = true;
                if (options?.onSuccess) options.onSuccess();
                if (options?.onFinish) options.onFinish();
            }, 800);
        },
        setData(key: string | Record<string, any>, value?: any) {
            if (typeof key === 'object') {
                Object.assign(this, key);
            } else {
                (this as any)[key] = value;
            }
        },
        reset(...fields: string[]) {
            if (fields.length === 0) {
                Object.assign(this, dataObj);
            } else {
                fields.forEach(field => {
                    (this as any)[field] = dataObj[field];
                });
            }
        }
    });

    return formState as any;
}
