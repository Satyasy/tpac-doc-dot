import '../css/app.css';
import { createApp, defineComponent, h, computed, shallowRef, watch } from 'vue';
import { currentPath, pageState, router } from './mocks/inertia';
import { getMockArticles, getMockDrugs, getInitialHealthDashboardProps } from './mocks/mock-data';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';

// 1. Define SPA Routes Map
const routes = [
    { path: '/', component: 'Welcome' },
    { path: '/about', component: 'About' },
    { path: '/contact', component: 'Contact' },
    { path: '/privacy-policy', component: 'PrivacyPolicy' },
    { path: '/terms-of-service', component: 'TermsOfService' },
    { path: '/consultation', component: 'Consultation' },
    { path: '/login', component: 'Auth/Login' },
    { path: '/register', component: 'Auth/Register' },
    { path: '/forgot-password', component: 'Auth/ForgotPassword' },
    { path: '/reset-password', component: 'Auth/ResetPassword' },
    { path: '/verify-otp', component: 'Auth/VerifyOtp' },
    { path: '/profile', component: 'Profile' },
    { path: '/health-dashboard', component: 'HealthDashboard' },
    { path: '/chat-history', component: 'ChatHistory' },
    { path: '/doctor/patients', component: 'Doctor/Patients' },
    
    // Wildcard Parameter Matching
    { path: '/article/:slug', component: 'ArticleDetail' },
    { path: '/article', component: 'Article' },
    { path: '/drug-catalog/:id', component: 'DrugDetail' },
    { path: '/drug-catalog', component: 'DrugCatalog' },
];

// Helper to match paths with optional route parameters (:id, :slug)
function matchRoute(path: string) {
    const exact = routes.find(r => r.path === path);
    if (exact) return { component: exact.component, params: {} as Record<string, string> };
    
    for (const route of routes) {
        if (route.path.includes('/:')) {
            const parts = route.path.split('/');
            const pathParts = path.split('/');
            if (parts.length === pathParts.length) {
                let match = true;
                const params: Record<string, string> = {};
                for (let i = 0; i < parts.length; i++) {
                    if (parts[i].startsWith(':')) {
                        const paramName = parts[i].substring(1);
                        params[paramName] = pathParts[i];
                    } else if (parts[i] !== pathParts[i]) {
                        match = false;
                        break;
                    }
                }
                if (match) {
                    return { component: route.component, params };
                }
            }
        }
    }
    
    return { component: 'Error404', params: {} };
}

// 2. Resolve props dynamically for mocked components
function resolveProps(componentName: string, params: Record<string, string>) {
    const query = new URLSearchParams(window.location.search);
    const categoryQuery = query.get('category') || null;
    const searchQuery = query.get('search') || null;

    if (componentName === 'HealthDashboard') {
        return getInitialHealthDashboardProps();
    }
    
    if (componentName === 'Article') {
        const allArticles = getMockArticles();
        let filtered = [...allArticles];
        
        if (categoryQuery) {
            filtered = filtered.filter(a => a.category === categoryQuery);
        }
        if (searchQuery) {
            const lowSearch = searchQuery.toLowerCase();
            filtered = filtered.filter(a => 
                a.title.toLowerCase().includes(lowSearch) || 
                a.content.toLowerCase().includes(lowSearch)
            );
        }
        
        return {
            articles: {
                data: filtered,
                current_page: 1,
                last_page: 1,
                per_page: 10,
                total: filtered.length,
                links: [
                    { url: null, label: '&laquo; Previous', active: false },
                    { url: '/article', label: '1', active: true },
                    { url: null, label: 'Next &raquo;', active: false }
                ]
            },
            featuredArticle: filtered[0] || null,
            popularArticles: allArticles.slice(0, 3),
            categories: {
                'Berita & Update Kesehatan': allArticles.filter(a => a.category === 'Berita & Update Kesehatan').length,
                'Edukasi Kesehatan': allArticles.filter(a => a.category === 'Edukasi Kesehatan').length,
                'Tips & Gaya Hidup Sehat': allArticles.filter(a => a.category === 'Tips & Gaya Hidup Sehat').length,
                'Pencegahan & Perawatan': allArticles.filter(a => a.category === 'Pencegahan & Perawatan').length,
            },
            currentCategory: categoryQuery,
            searchQuery: searchQuery
        };
    }
    
    if (componentName === 'ArticleDetail') {
        const allArticles = getMockArticles();
        const article = allArticles.find(a => a.slug === params.slug) || allArticles[0];
        const relatedArticles = allArticles.filter(a => a.id !== article.id);
        
        return {
            article,
            relatedArticles
        };
    }
    
    if (componentName === 'DrugCatalog') {
        const allDrugs = getMockDrugs();
        const pregnancySafeQuery = query.get('pregnancy_safe') || '';
        let filtered = [...allDrugs];
        
        if (categoryQuery) {
            filtered = filtered.filter(d => d.category === categoryQuery);
        }
        if (pregnancySafeQuery) {
            const isSafe = pregnancySafeQuery === '1';
            filtered = filtered.filter(d => d.pregnancy_safe === isSafe);
        }
        if (searchQuery) {
            const lowSearch = searchQuery.toLowerCase();
            filtered = filtered.filter(d => 
                d.name.toLowerCase().includes(lowSearch) || 
                d.description.toLowerCase().includes(lowSearch)
            );
        }
        
        return {
            drugs: {
                data: filtered,
                current_page: 1,
                last_page: 1,
                per_page: 10,
                total: filtered.length,
                links: [
                    { url: null, label: '&laquo; Previous', active: false },
                    { url: '/drug-catalog', label: '1', active: true },
                    { url: null, label: 'Next &raquo;', active: false }
                ]
            },
            categories: ['Analgesik', 'Antibiotik', 'Antihistamin', 'Antasida', 'Vitamin', 'Obat Batuk', 'Obat Flu', 'Antiseptik'],
            filters: {
                search: searchQuery || '',
                category: categoryQuery || '',
                pregnancy_safe: pregnancySafeQuery
            }
        };
    }
    
    if (componentName === 'DrugDetail') {
        const allDrugs = getMockDrugs();
        const idNum = Number(params.id);
        const drug = allDrugs.find(d => d.id === idNum) || allDrugs[0];
        const relatedDrugs = allDrugs.filter(d => d.id !== drug.id);
        
        return {
            drug,
            relatedDrugs
        };
    }
    
    if (componentName === 'Consultation') {
        const storedSessions = localStorage.getItem('docdot_sessions');
        const sessions = storedSessions ? JSON.parse(storedSessions) : [
            { id: 'session-demo', title: 'Demo Konsultasi AI', created_at: new Date().toISOString() }
        ];
        
        if (!storedSessions) {
            localStorage.setItem('docdot_sessions', JSON.stringify(sessions));
        }

        return {
            sessions,
            userRole: pageState.props.auth.user?.roles?.some((r: any) => r.name === 'doctor') ? 'doctor' : 'patient'
        };
    }

    if (componentName === 'ChatHistory') {
        const storedSessions = localStorage.getItem('docdot_sessions') || '[]';
        return {
            sessions: JSON.parse(storedSessions)
        };
    }

    if (componentName === 'Doctor/Patients') {
        return {
            sessions: []
        };
    }
    
    if (componentName === 'Profile') {
        const storedProfile = localStorage.getItem('docdot_user_profile');
        const profile = storedProfile ? JSON.parse(storedProfile) : {
            height: 170,
            weight: 68.0,
            bmi: 23.5,
            bmi_category: 'Normal'
        };
        return {
            profile
        };
    }

    return {};
}

// 3. Vue Page Glob Import (matches resources/js/app.ts glob resolution)
const pagesGlob = import.meta.glob<DefineComponent>('./pages/**/*.vue');

// 4. Main SPA Router Component
const RouterView = defineComponent({
    name: 'RouterView',
    setup() {
        const activeComponent = shallowRef<any>(null);
        const activeProps = shallowRef<any>({});
        
        const routeData = computed(() => {
            return matchRoute(currentPath.value);
        });
        
        watch(() => routeData.value, async (newRoute) => {
            const { component, params } = newRoute;
            try {
                const module = await resolvePageComponent(`./pages/${component}.vue`, pagesGlob);
                activeComponent.value = module.default;
                activeProps.value = resolveProps(component, params);
            } catch (err) {
                console.error(`Gagal memuat komponen halaman: ${component}`, err);
                const errModule = await resolvePageComponent('./pages/Error404.vue', pagesGlob);
                activeComponent.value = errModule.default;
                activeProps.value = {};
            }
        }, { immediate: true });
        
        // Listen to activeProps update triggers when pageState updates (e.g. profile update or log save)
        watch(() => pageState.props.auth.user, () => {
            const { component, params } = routeData.value;
            activeProps.value = resolveProps(component, params);
        }, { deep: true });

        // Update activeProps when path changes (to reflect fresh query strings)
        watch(() => currentPath.value, () => {
            const { component, params } = routeData.value;
            activeProps.value = resolveProps(component, params);
        });
        
        return () => activeComponent.value 
            ? h(activeComponent.value, activeProps.value)
            : h('div', { class: 'flex h-screen items-center justify-center bg-gray-50' }, [
                h('div', { class: 'text-center' }, [
                    h('div', { class: 'h-10 w-10 animate-spin rounded-full border-4 border-t-purple-600 border-gray-200 mx-auto' }),
                    h('p', { class: 'mt-3 text-[14px] font-medium text-gray-600' }, 'Memuat Halaman...')
                ])
              ]);
    }
});

// 5. Create Vue instance and map Global Properties for templates
const app = createApp(RouterView);

app.config.globalProperties.$page = pageState;
app.config.globalProperties.$inertia = {
    visit: router.visit.bind(router),
    get: router.get.bind(router),
    post: router.post.bind(router),
    put: router.put.bind(router),
    delete: router.delete.bind(router),
    reload: router.reload.bind(router)
};

app.mount('#app');
