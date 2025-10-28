<template>
  <AppLayout>
      <div>
          <HeroBanner class="mb-4" />

          <section class="max-w-6xl mx-auto px-4 py-8">
              <ProvidersList class="mb-4" />

              <SlotsList class="mb-4" />

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

export default {
  components: {AppLayout, SlotsList, ProvidersList, HeroBanner, GameCard, DonationWidget},
  data() {
    return {
      topGames: [
        {name: 'Mega Slots', provider: 'Pragmatic'},
        {name: 'Royal Spin', provider: 'NetEnt'},
        {name: 'Poker Pro', provider: 'BGaming'},
        {name: 'Lucky Wheel', provider: 'Red Tiger'},
        {name: 'Fruit Rush', provider: 'Hacksaw'},
        {name: 'Gold Mine', provider: 'Amatic'},
      ],
    };
  },
};
</script>
