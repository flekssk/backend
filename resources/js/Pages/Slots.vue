<template>
    <AppLayout>
        <div>
            <section class="max-w-6xl mx-auto px-4 py-8">
                <div class="flex items-center justify-between gap-4 mb-4">
                    <CategoryTitle>
                            <span v-if="selectedProvider" class="inline-flex gap-2">
                                <img :src="selectedProvider.icon" />
                                <span>{{ selectedProvider?.title }}</span>
                            </span>
                        <span v-else>Все игры</span>
                    </CategoryTitle>
                    <ProvidersSelect class="shrink-0 mb-4"/>
                </div>
                <SlotsList :paginated="true" :provider="provider" class="mb-4"/>
            </section>
        </div>
    </AppLayout>
</template>

<script lang="ts">
import HeroBanner from '@/Components/HeroBanner.vue';
import GameCard from '@/Components/GameCard.vue';
import DonationWidget from '@/Components/DonationWidget.vue';
import ProvidersList from "@/Components/Slots/ProvidersList.vue";
import SlotsList from "@/Components/Slots/SlotsList.vue";
import AppLayout from "../Layouts/AppLayout.vue";
import ProvidersSelect from "@/Components/Slots/ProvidersSelect.vue";
import CategoryTitle from "@/Components/CategoryTitle.vue";
import {useSlotsStore} from "@/Stores/slots";
import {mapState} from "pinia";

export default {
    components: {
        CategoryTitle,
        ProvidersSelect, AppLayout, SlotsList, ProvidersList, HeroBanner, GameCard, DonationWidget
    },
    computed: {
        ...mapState(useSlotsStore, ['selectedProvider']),
        provider() {
            return this.$page.props.provider ?? null
        },
    }
};
</script>
