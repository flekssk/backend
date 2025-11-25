<template>
    <div class="category-item" @click="handleClick">
      <template v-if="!!item.component">
        <component :is="item.component" />
      </template>
      <template v-else>
        <img loading="lazy" class="w-full h-full object-cover" :src="item.image" alt="img" />
      </template>
    </div>
  </template>

<script>
import {router} from "@inertiajs/vue3";
import {mapActions, mapGetters, mapState} from "pinia";
import {useUserStore} from "@/Stores/user.js";

export default {
    props: {
        item: {
            type: Object,
            required: true,
        },
    },
    data() {
        return {
            hasErrored: false,
        };
    },
    computed: {
        ...mapState(useUserStore, ['user']),
    },
    methods: {
        ...mapActions(useUserStore, ['openAuthModal']),
        handleClick() {
            if (this.user) {
                router.get(route('play', this.item.id))
            } else {
                this.openAuthModal()
            }
        },
    },
};
</script>

<style lang="scss" scoped>
.category-item {
    width: 100%;
    aspect-ratio: 166/222;
    overflow: hidden;
    border-radius: 16px;
    z-index: 2;
    transition: transform 0.4s, opacity 0.4s;

    &:hover {
        opacity: 0.75;
        transform: translateY(-10px);
    }

    img {
        width: 100%;
        height: auto;
    }
}
</style>
