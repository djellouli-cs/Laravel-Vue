import './bootstrap';
import { createApp, h } from 'vue';
import { createInertiaApp, Head, Link } from '@inertiajs/vue3';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import Layout from '@/Layouts/LayoutAnnuaire.vue'; // optional default layout

createInertiaApp({
    title: title => `DTN ${title}`,

    resolve: name => {
        const pages = import.meta.glob('./Pages/**/*.vue', { eager: true });
        const page = pages[`./Pages/${name}.vue`];

        if (!page) {
            throw new Error(`❌ Page not found: ./Pages/${name}.vue`);
        }

        // Set default layout if not defined in the page itself
        page.default.layout = page.default.layout || Layout;

        return page;
    },

    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .component('Head', Head)
            .component('Link', Link)
            .mount(el);
    },

    progress: {
        color: 'yellow',
        includeCSS: true,
        showSpinner: true,
    },
});
