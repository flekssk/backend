<template>
    <AppLayout>
        <div class="flex flex-col w-full gap-4 p-4 z-10" v-if="slot">
            <div class="header grid grid-cols-3 place-items-center w-full">
                <div></div>
                <div class="header__game h-[40px] flex items-center gap-2">
                    <span class="text-[var(--color-text)] text-[18px] font-baloo">{{ slot.title }}</span>
                </div>
                <el-button variant="default" @click.native="toggle"
                           :isDisabled="false"
                           class="header__toggle justify-self-end bg-[var(--color-primary)] h-[40px] aspect-square rounded-[8px] text-white text-[16px] p-2 flex items-center font-baloo">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1"
                         id="Capa_1" x="0px" y="0px" viewBox="0 0 330 330" style="enable-background:new 0 0 330 330;"
                         xml:space="preserve" width="32px" height="32px" class=""><g><g>
                        <path
                            d="M315,210c-8.284,0-15,6.716-15,15v53.785l-94.392-94.392c-5.857-5.858-15.355-5.858-21.213,0   c-5.858,5.858-5.858,15.355,0,21.213l94.39,94.39L224.999,300c-8.284,0-15,6.717-14.999,15.001   c0.001,8.284,6.717,14.999,15.001,14.999l90-0.006c8.284,0,14.999-6.716,14.999-15V225C330,216.716,323.284,210,315,210z"
                            data-original="#000000" class="active-path" data-old_color="#000000" fill="#FFFFFF"/>
                        <path
                            d="M15,120c8.284,0,15-6.716,15-15V51.215l94.392,94.392c2.929,2.929,6.768,4.394,10.606,4.394   c3.839,0,7.678-1.464,10.607-4.394c5.858-5.858,5.858-15.355,0-21.213l-94.39-94.39L105.001,30c8.284,0,15-6.717,14.999-15.001   S113.283,0,104.999,0l-90,0.006C6.715,0.006,0,6.722,0,15.006V105C0,113.284,6.716,120,15,120z"
                            data-original="#000000" class="active-path" data-old_color="#000000" fill="#FFFFFF"/>
                        <path
                            d="M124.394,184.395l-94.39,94.39L30,224.999c0-8.284-6.717-14.999-15.001-14.999S0,216.717,0,225.001l0.006,90   c0,8.284,6.716,14.999,15,14.999H105c8.284,0,15-6.716,15-15s-6.716-15-15-15H51.215l94.392-94.392   c5.858-5.858,5.858-15.355,0-21.213C139.749,178.537,130.251,178.537,124.394,184.395z"
                            data-original="#000000" class="active-path" data-old_color="#000000" fill="#FFFFFF"/>
                        <path
                            d="M195,149.997c3.839,0,7.678-1.464,10.606-4.394l94.39-94.39L300,105.001c0.001,8.284,6.717,15,15.001,14.999   c8.284-0.001,15-6.717,14.999-15.001l-0.006-90C329.993,6.715,323.278,0,314.994,0H225c-8.284,0-15,6.716-15,15s6.716,15,15,15   h53.784l-94.391,94.391c-5.858,5.858-5.858,15.355,0,21.213C187.322,148.533,191.161,149.997,195,149.997z"
                            data-original="#000000" class="active-path" data-old_color="#000000" fill="#FFFFFF"/>
                    </g></g> </svg>
                </el-button>
            </div>

            <div class="game-container w-full rounded-[16px]">
                <fullscreen class="z-20" v-if="slot.url" v-model="isFullscreen" style="height: 70%">
                    <iframe id="iframe_slot"
                            scrolling="no"
                            frameborder="0"
                            webkitallowfullscreen="true"
                            allowfullscreen
                            allow="autoplay; fullscreen"
                            mozallowfullscreen="true"
                            allowtransparency="true"
                            style="position: relative; z-index: 1"
                            :src="slot.url + '&wmode=transparent'"
                    ></iframe>
                </fullscreen>
            </div>
        </div>
    </AppLayout>
</template>

<script>
import {ref} from "vue";
import AppLayout from "../Layouts/AppLayout.vue";
import {Link} from "@inertiajs/vue3";
import {mapActions, mapState} from "pinia";
import {useSlotsStore} from "../Stores/slots.js";
import {useUserStore} from "@/Stores/user.js";

export default {
    components: {
        Link,
        AppLayout,
    },
    data() {
        return {
            isFullscreen: ref(false),
            slot: ref(null),
        };
    },

    methods: {
        ...mapActions(useSlotsStore, ['loadSlot']),

        toggle() {
            this.isFullscreen = !this.isFullscreen;
        },
    },
    computed: {
        ...mapState(useUserStore, ['user']),
        slotId() {
            return this.$page.props.slot?.id ?? this.$page.props.slotId
        },
    },
    watch: {
        user: {
            immediate: true,
            handler(newUser) {
                if (newUser) {
                     this.loadSlot(this.slotId).then(slot => {
                         this.slot = slot
                    })
                }
                else this.slot = null
            },
        },
    },
};
</script>

<style lang="scss" scoped>
.game-container {
    position: relative;
    width: 100%;
    height: 100%;
    aspect-ratio: 16/9;
    overflow: hidden;
    border-radius: 16px;
    z-index: 9999 !important;

}

::v-deep(.fullscreen) {
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    width: 100vw !important;
    height: 100vh !important;
    z-index: 9999 !important;
    background: rgba(0, 0, 0, 1); // Optional: darken background
}

@supports (height: 100dvh) {
    ::v-deep(.fullscreen) {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        width: 100vw !important;
        height: 100dvh !important;
        z-index: 9999 !important;
        background: rgba(0, 0, 0, 1); // Optional: darken background
    }
}

@supports (-webkit-touch-callout: none) {
    .game-container {
        position: relative;
        width: 100%;
        height: 100%;
        aspect-ratio: 16/9;
        overflow: hidden;
        border-radius: 16px;
    }
}

iframe {
    width: 100%;
    height: 100%;
    border-radius: inherit;
}

.header {
    &__back {
        svg {
            width: 40px;
            height: 40px;
            stroke: white;
            display: none;
        }
    }
}

@media (max-width: 568px) {
    .header {
        &__game {
            padding: 0 5px;

            span {
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            img {
                display: none;
            }
        }

        &__back {
            padding: 0;
            width: 100%;
            max-width: 100px;
            justify-content: center;
            width: 40px;

            svg {
                display: block;
            }

            span {
                display: none;
            }
        }
    }
}
</style>
