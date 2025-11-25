<template>
    <swiper
        :modules="[Autoplay]"
        :loop="true"
        :slides-per-view="5.3"
        :slides-per-group="1"
        :space-between="0"
        :centered-slides="false"
        :speed="3000"
        :autoplay="{ delay: 3000, disableOnInteraction: false, pauseOnMouseEnter: true }"
        :breakpoints="breakpoints"
        :observer="true"
        :observe-parents="true"
        :loop-additional-slides="2"
        v-if="providers"
        @swiper="onSwiperReady"
    >
        <swiper-slide
            v-for="(provider, index) in providers"
            :key="index"
            class="cursor-pointer providers__item"
            @click.native="navigateToSlots(provider.id)"
        >
            <img style="width: 190px !important;"
                 :src="provider.image ?? '/images/default-provider.png'" alt="provider"/>
        </swiper-slide>
    </swiper>
</template>

<script lang="ts">
import {Swiper, SwiperSlide} from "swiper/vue";
import "swiper/css";
import "swiper/css/navigation";
import "swiper/css/pagination";
import {Autoplay} from "swiper/modules";
import { router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import {mapActions, mapState} from "pinia";
import {useSlotsStore} from "@/Stores/slots.js";
import {nextTick} from "vue";

export default {
    components: {
        Swiper,
        SwiperSlide,
    },
    data() {
        return {
            Autoplay,
            breakpoints: {
                0: {slidesPerView: 3.3, spaceBetween: 12},
                640: {slidesPerView: 3.3, spaceBetween: 12},
                1024: {slidesPerView: 5.3, spaceBetween: 12},
                1280: {slidesPerView: 5.3, spaceBetween: 12}
            },
        };
    },
    async created() {
        await this.fetchProviders();

        this.$nextTick(() => {
            if (this.$refs.swiper && this.$refs.swiper.swiper) {
                this.$refs.swiper.swiper.update();
                this.$refs.swiper.swiper.slideTo(0, 0);
            }
        })
    },
    methods: {
        ...mapActions(useSlotsStore, ['fetchProviders']),
        onSwiperReady(swiperInstance) {
            this.swiper = swiperInstance;
            this.swiper.autoplay.start();
        },
        navigateToSlots(provider) {
            router.get(route('slots-provider', {provider}))
        },

    },
    computed: {
        ...mapState(useSlotsStore, ['providers']),
    },
    watch: {
        providers() {
            nextTick(() => {
                this.swiper?.update()
                this.swiper?.slideTo?.(0, 0)
            })
        },
    },
}
</script>

<style scoped lang="scss">
.providers-container {
    padding: 24px 0;
    background: rgba(31, 27, 41, 0.5215686275);
    gap: 24px;
    border-radius: 32px;

    .swiper {
        margin-left: 17px;
    }

    .providers-container__header {
        margin-bottom: 24px;
        padding-left: 15px;
    }

    .providers__list {
        position: relative;
        cursor: pointer;
        transition: all 0.3s ease;

        display: flex;

        &:deep(.swiper-wrapper) {
            padding: 10px 0;
        }

        .providers__item {
            min-width: 185px;
            width: auto;
            /* background-color: rgba(31, 27, 41, 0.5); */
            /* border-radius: 16px; */
            /* padding: 12px; */
            cursor: pointer;

            img {
                display: block;
                width: 100%;
                height: auto;
                max-height: 100%;
                border-radius: 16px;
                object-fit: cover;
                transition: all 0.3s ease;
                box-shadow: 0 0 12px rgba(255, 255, 255, 0);
            }

            &:hover {
                img {
                    box-shadow: 0 0 12px rgba(255, 255, 255, 0.2);
                }
            }
        }
    }

    @media (max-width: 1023px) {
        border-radius: 0;

        .providers__item {
            width: 136px;
            height: 72px;
        }
    }

    @media (min-width: 1024px) {
        .providers__item {
            width: 206px;
            height: 110px;
        }
    }
}
</style>
