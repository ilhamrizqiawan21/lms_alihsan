import 'bootstrap/dist/js/bootstrap.bundle.min.js';

import { createInertiaApp } from '@inertiajs/vue3';
import { createApp, h } from 'vue';

// Resolve the page map once at startup instead of recreating the glob on every navigation.
const pages = import.meta.glob('./Pages/**/*.vue');

createInertiaApp({
    title: (title) => title ? `${title} - LMS Sekolah` : 'LMS Sekolah',
    resolve: (name) => pages[`./Pages/${name}.vue`]?.(),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el);
    },
    progress: {
        color: '#198754',
    },
});
