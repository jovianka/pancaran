import '../css/app.css';
import 'vue-sonner/style.css';
import { createInertiaApp, router } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import { toast } from 'vue-sonner';
import { ZiggyVue } from 'ziggy-js';
import { initializeTheme } from './composables/useAppearance';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./pages/${name}.vue`, import.meta.glob<DefineComponent>('./pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});

// Surface authorization failures (403) as a friendly toast instead of Inertia's
// default error modal. Requires a <Toaster> mounted on the current page.
// Backend policies remain the source of truth.
router.on('httpException', (event) => {
    const status = (event as CustomEvent).detail?.response?.status;
    if (status === 403) {
        event.preventDefault();
        toast.error('Anda tidak memiliki izin untuk melakukan tindakan ini.');
    }
});

// This will set light / dark mode on page load...
initializeTheme();
