<template>
    <div class="category-list">
        <SlotItem v-for="slot in slots" :item="slot" />
    </div>
</template>

<script lang="ts">
import "swiper/css";
import "swiper/css/navigation";
import "swiper/css/pagination";
import {ref} from "vue";
import axios from "axios";
import SlotItem from "@/Components/Slots/SlotItem.vue";

export type slotsType = ['latest', 'popular', 'by_provider']

export default {
    components: {SlotItem},
    props: {
        length: {
            type: Number,
            default: 16
        },
        types: {
            type: Array,
            default: () => []
        }
    },
    created() {
        this.fetchSlots()
    },
    methods: {
        fetchSlots() {
            axios.post(`/api/v1/slots/list`)
                .then(response => {
                    this.slots = response.data;
                })
                .catch(error => {
                    console.error(error);
                });
        },
        handleSlotClick(slot) {
            if (!this.isAuthenticated) {
                this.$emitter.emit("noty", {
                    title: "Необходима авторизация",
                    text: "Пожалуйста, войдите в аккаунт для открытия слота",
                    type: "error",
                });
            } else {
                localStorage.setItem(`slotSource_mobule_${slot.id}`, provider);
                this.$router.push({
                    path: `/slots/${slot.id}`
                });
            }
        },
    },
    data() {
        return {
            slots: ref([]),
        }
    }
};
</script>

<style lang="scss" scoped>
.category-list {
    display: grid;
    /* grid-template-columns: repeat(auto-fit, minmax(136px, 1fr)); */
    /* grid-template-columns: repeat(6, minmax(136px, 1fr)); */
    grid-template-columns: repeat(6, 1fr);
    gap: 8px;
    position: relative;
}

@media (max-width: 1024px) {
    .category-list {
        /* grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); */
    }
}

@media (max-width: 768px) {
    .category-list {
        /* grid-template-columns: repeat(auto-fit, minmax(100px, 1fr)); */
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 480px) {
    .category-list {
        /* grid-template-columns: repeat(auto-fit, minmax(90px, 1fr)); */
    }
}

@media (max-width: 320px) {
    .category-list {
        /* grid-template-columns: repeat(2, minmax(70px, 1fr)); */
    }
}
</style>
