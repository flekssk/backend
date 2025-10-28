<template>
  <section class="max-w-6xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-4">Slots</h1>
    <div class="mb-4 flex items-center gap-3 flex-wrap">
      <label class="text-gray-300">Filter by provider:</label>
      <el-select v-model="selectedProvider" placeholder="All Providers" clearable style="min-width: 220px;">
        <el-option label="All Providers" :value="''" />
        <el-option v-for="prov in providers" :key="prov" :label="prov" :value="prov" />
      </el-select>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
      <GameCard v-for="g in filteredGames" :key="g.name" :game="g" />
    </div>
  </section>
</template>

<script>
import { ref, computed } from 'vue';
import GameCard from '../Components/GameCard.vue';
export default {
  components: { GameCard },
  setup(){
    const selectedProvider = ref('');
    const providers = computed(() => Array.from(new Set(games.value.map(g => g.provider))));
    const filteredGames = computed(() => !selectedProvider.value ? games.value : games.value.filter(g => g.provider === selectedProvider.value));
    return { selectedProvider, providers, games, filteredGames };
  }
}
</script>
