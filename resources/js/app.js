require('./bootstrap');

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/inertia-vue3';
import { InertiaProgress } from '@inertiajs/progress';
import { createI18n } from 'vue-i18n';
import zh_CN from '../js/locales/zh_CN';

//i18n
const i18n = createI18n({
    locale: 'zh_CN',
    fallbackLocale: 'en',
    messages: {
        zh_CN
    }
});


//vxe
import { useTable } from './plugins/vxe-table';
import 'vxe-table/lib/style.css';
import 'xe-utils';


const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => require(`./Pages/${name}.vue`),
    setup({ el, app, props, plugin }) {
        return createApp({ render: () => h(app, props) })
            .use(plugin)
            .use(useTable)
            .use(i18n)
            .mixin({ methods: { route } })
            .mount(el);
    },
});

InertiaProgress.init({ color: '#4B5563' });
