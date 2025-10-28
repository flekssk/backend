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
    methods: {
        handleClick() {
            console.log(this.item);
            localStorage.setItem(`slotSource_mobule_${this.item.id}`, 'mobule');

            this.$router.push({ path: `/slots/${this.item.id}` });
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
