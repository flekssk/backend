import {createApp, h} from 'vue';
import ElementPlus from 'element-plus';
import 'element-plus/dist/index.css';
import '../css/app.css';
import 'animate.css';
import {MotionPlugin} from '@vueuse/motion';
import '@fontsource/baloo-2/400.css';
import '@fontsource/baloo-2/700.css';
import {createInertiaApp} from "@inertiajs/vue3";
import mitt from "mitt";
import {createPinia} from "pinia";
import { useUserStore } from "@/Stores/user.ts";
import PrimeVue from 'primevue/config';
import ToastService from 'primevue/toastservice';
import Aura from '@primevue/themes/aura';
import {initEcho} from "./Services/Socket/Socket.js";
import VueFullscreen from "vue-fullscreen";
import {ZiggyVue} from "ziggy-js";
import {useSlotsStore} from "@/Stores/slots.js";
import { ClickOutside as vClickOutside } from 'element-plus'

createInertiaApp({
    resolve: (name) => import(`./Pages/${name}.vue`),
    setup({el, App, props, plugin}) {
        const app = createApp({render: () => h(App, props)})

        app.config.globalProperties.$emitter = mitt();

        initEcho()

        app.use(plugin)
            .use(ElementPlus)
            .directive('click-outside', vClickOutside)
            .use(PrimeVue, {theme: {preset: Aura}})
            .use(ToastService)
            .use(MotionPlugin)
            .use(VueFullscreen)
            .use(ZiggyVue)
            .use(createPinia())
            .mount(el);

        const userStore = useUserStore()
        userStore.init()
        const slotsStore = useSlotsStore()
        let socketInitialized = false
        do {
            setTimeout(100)

            if (window.Echo) {
                socketInitialized = true
                slotsStore.initSocket()
            }
        } while (!socketInitialized)
    },
}).then(r => {});
