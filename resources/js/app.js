import {createApp, h} from 'vue';
import ElementPlus from 'element-plus';
import 'element-plus/dist/index.css';
import './index.css';
import 'animate.css';
import {MotionPlugin} from '@vueuse/motion';
import '@fontsource/baloo-2/400.css';
import '@fontsource/baloo-2/700.css';
import {createInertiaApp} from "@inertiajs/vue3";
import mitt from "mitt";
import {createPinia} from "pinia";
import { useUserStore } from "@/Stores/user.ts";

createInertiaApp({
    resolve: (name) => import(`./Pages/${name}.vue`),
    setup({el, App, props, plugin}) {
        const app = createApp({render: () => h(App, props)})

        app.config.globalProperties.$emitter = mitt();

        app.use(plugin)
            .use(ElementPlus)
            .use(MotionPlugin)
            .use(createPinia())
            .mount(el);

        const userStore = useUserStore()
        userStore.init()
    },
}).then(r => {});
