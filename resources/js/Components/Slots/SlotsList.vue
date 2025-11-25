<template>
    <div class="category-list">
        <SlotItem
            v-for="(slot, index) in slots"
            :key="slot.id ?? index"
            :item="slot"
        />
    </div>
    <div v-if="isLoading" class="flex-1 flex items-center justify-center min-h-[80px]">
          <span
              class="inline-block h-6 w-6 animate-spin rounded-full border-2 border-current border-r-transparent"
              role="status"
              aria-label="Загрузка"
          />
    </div>
    <div
        v-show="hasMore"
        ref="sentinel"
        style="height: 1px; width: 100%;"
    />
</template>

<script lang="ts">
import "swiper/css";
import "swiper/css/navigation";
import "swiper/css/pagination";
import SlotItem from "@/Components/Slots/SlotItem.vue";
import {ApiClient} from "@/Services/ApiClient/ApiClient";
import {ref} from "vue";
import Coin from '../Coin.vue'

export type slotsType = ['latest', 'popular', 'by_provider']

export default {
    components: {SlotItem, Coin},
    props: {
        length: {
            type: Number,
            default: 24
        },
        type: {
            type: String as () => slotsType,
            default: () => 'popular'
        },
        provider: {
            type: Number,
            default: null
        },
        paginated: {
            type: Boolean,
            default: false
        }
    },
    data() {
        return {
            slots: [] as any[],
            page: 1,
            hasMore: true,
            observer: null as IntersectionObserver | null,
            isLoading: ref(false)
        }
    },
    created() {
        this.fetchSlots(true)
    },
    mounted() {
        this.setupObserver()
    },
    beforeUnmount() {
        if (this.observer) {
            this.observer.disconnect();
            this.observer = null;
        }
    },
    watch: {
        type() {
            this.fetchSlots(true)
        },
        provider() {
            this.fetchSlots(true)
        },
        length() {
            this.fetchSlots(true)
        },
    },
    methods: {
        setupObserver() {
            const sentinel = this.$refs.sentinel as HTMLElement | undefined;
            if (!sentinel) return;

            if (this.observer) {
                this.observer.disconnect();
                this.observer = null;
            }

            this.observer = new IntersectionObserver((entries) => {
                const entry = entries[0];
                if (entry.isIntersecting) {
                    this.fetchSlots(false);
                }
            }, {
                root: null,
                rootMargin: '200px 0px',
                threshold: 0,
            });

            this.observer.observe(sentinel);
        },

        async fetchSlots(reset = false) {
            if (this.isLoading) return;

            if (this.type === 'latest') {
                if (reset) {
                    this.isLoading = true;
                    this.hasMore = false; // отключаем бесконечную прокрутку
                    this.page = 1;
                    try {
                        const resp = await ApiClient.post(`/api/v1/user/slots/latest?per_page=${this.length}`, {});
                        this.slots = Array.isArray(resp.data) ? resp.data : [];
                    } catch (e) {
                        console.error(e);
                    } finally {
                        this.isLoading = false;
                    }
                }
                return;
            }

            if (reset) {
                this.page = 1;
                this.hasMore = true;
                this.slots = [];
            }

            if (!this.hasMore || (!this.paginated && this.page > 1)) return;

            this.isLoading = true;

            let sort: Record<string, string> = {};
            let filter: Record<string, unknown> = {};

            if (this.type !== 'latest') sort = {priority: 'asc'};
            if (this.provider) filter = {slot_provider_id: {contains: [this.provider]}};

            try {
                const url = `/api/v1/slots/list?per_page=${this.length}&page=${this.page}`;
                const resp = await ApiClient.post(url, {sort, filter});

                const items = Array.isArray(resp.data) ? resp.data : [];
                if (items.length > 0) {
                    if (reset) {
                        this.slots = items;
                    } else {
                        this.slots = [...this.slots, ...items];
                    }
                    this.page += 1;
                }

                if (items.length < this.length) {
                    this.hasMore = false;
                }
            } catch (e) {
                console.error(e);
            } finally {
                this.isLoading = false;
            }
        },
    },
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
