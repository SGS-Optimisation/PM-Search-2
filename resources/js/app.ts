import './bootstrap';
import '../css/app.css';
import "primevue/resources/primevue.min.css";
import "primeicons/primeicons.css";
import 'primevue/resources/themes/mira/theme.css';
import 'vue-inner-image-zoom/lib/vue-inner-image-zoom.css';
import 'primeflex/primeflex.css';


import {createApp, h} from 'vue';
import {createInertiaApp} from '@inertiajs/vue3';
import {resolvePageComponent} from 'laravel-vite-plugin/inertia-helpers';
import {ZiggyVue} from '../../vendor/tightenco/ziggy/dist/vue.m';
import type {DefineComponent} from "vue";
import PrimeVue from 'primevue/config';
import DialogService from 'primevue/dialogservice';
import Tooltip from 'primevue/tooltip';

import VueImageZoomer from 'vue-image-zoomer'
import {createPinia} from 'pinia'
import piniaPluginPersistedstate from 'pinia-plugin-persistedstate'

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';
const pinia = createPinia()
pinia.use(piniaPluginPersistedstate)


// @ts-ignore
createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob<DefineComponent>('./Pages/**/*.vue')),
    setup({el, App, props, plugin}) {
        createApp({render: () => h(App, props)})
            .directive('tooltip', Tooltip)
            .use(plugin)
            .use(PrimeVue)
            .use(DialogService)
            .use(ZiggyVue, (window as any).Ziggy)
            .use(pinia)
            .use(VueImageZoomer)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
