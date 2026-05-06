// import './bootstrap';
import '../css/app.css';

import { createApp, h, ref } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { useDateFormat } from '@vueuse/core';

const appName = import.meta.env.VITE_APP_NAME || 'TDMS';

const filters = {
    formatDate(date){
        const formattedDate = useDateFormat(date, 'DD/MM/YYYY hh:mm A', { locales: 'en-US' }).value
        return formattedDate
    },
    formatDateForDatePicker(date){
        const formattedDate = useDateFormat(date, 'DD-MM-YYYY hh:mm A', { locales: 'en-US' }).value
        return formattedDate
    }
}

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        // return createApp({ render: () => h(App, props) })
        //     .use(plugin)
        //     .use(ZiggyVue)
        //     .use(filters)
        //     .mount(el);
        const app  = createApp({ render: () => h(App, props),
            provide: {
                filters: filters,
            }
        })
        .use(plugin)
        .use(ZiggyVue)

        app.config.globalProperties.$filters = filters;
        app.mount(el)
        // .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});

