<template>
  <div class="bg-gray-800 rounded-xl overflow-hidden shadow border border-white/10
              transform hover:scale-[1.02] hover:shadow-xl transition duration-300">
    <img :src="imageSrc" :alt="game.name" class="w-full aspect-video object-cover" />
    <div class="p-3 flex items-center justify-between">
      <div>
        <h3 class="text-white text-sm font-semibold leading-tight">{{ game.name }}</h3>
        <p class="text-gray-400 text-xs">{{ game.provider }}</p>
      </div>
      <button class="px-3 py-1.5 rounded-lg bg-purple-600 hover:bg-purple-700 text-white text-xs btn-glossy">
        Play
      </button>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
const props = defineProps({ game: { type: Object, required: true } });
const imageSrc = computed(() => props.game.image || `https://picsum.photos/seed/${encodeURIComponent(props.game.name)}/480/270`);
</script>
