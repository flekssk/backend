<template>
    <div class="header mb-4 mt-3">
        <h2 class="text-[18px] lg:text-[24px] font-baloo uppercase text-[var(--color-text)] font-bold">
            <slot></slot>
        </h2>
        <Link v-if="routeLink" :href="route(routeLink)">
            <el-button type="warning" class="animate__animated animate__heartBeat animate__repeat-2">
                Все игры
            </el-button>
        </Link>
    </div>
</template>

<script>

import {Link} from "@inertiajs/vue3";

export default {
    components: {Link},
    props: {
        title: String,
        routeLink: {
            type: String,
            default: null,
        }
    },
};
</script>

<style lang="scss" scoped>
.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    .text_button {
        justify-content: center;
        font-family: "Rubick Medium", sans-serif;
        font-weight: 500;
        font-size: 16px;
        width: 144px;
        text-transform: uppercase;
        justify-content: space-between;
    }

    svg {
        flex: none;
        transition: all 0.4s ease;
    }
    .active {
        svg {
            transform: rotate(180deg);
        }
    }
    @media (max-width: 1024px) {
        .text_button {
            width: calc(120px + 44 * ((100vw - 390px) / 1024));
        }
    }
    @media (max-width: 768px) {
        .text_button {
            display: none;
        }
    }
}
</style>
