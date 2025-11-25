<template>
    <AppLayout>
        <div>
            <HeroBanner class="mb-4"/>

            <section class="max-w-6xl mx-auto px-4 py-8">
                <CategoryTitle>Провайдеры</CategoryTitle>
                <ProvidersList class="mb-4"/>

                <CategoryTitle v-if="user">Мои игры</CategoryTitle>
                <SlotsList type="latest" class="mb-4" v-if="user" />

                <CategoryTitle route-link="slots" >Популярные игры</CategoryTitle>
                <SlotsList has class="mb-4"/>

                <div class="my-10">
                    <DonationWidget/>
                </div>
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
import CategoryTitle from "@/Components/CategoryTitle.vue";
import {mapState} from "pinia";
import {useUserStore} from "@/Stores/user";

export default {
    components: {CategoryTitle, AppLayout, SlotsList, ProvidersList, HeroBanner, GameCard, DonationWidget},
    computed: {
        ...mapState(useUserStore, ['user'])
    }
};
</script>
