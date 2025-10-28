<template>
    <div class="flex items-center pl-2 pr-3 select-none">
        <div class="relative w-18 h-18 md:hidden">
            <Coin size="40"/>
        </div>
        <div class="relative w-18 h-18 hidden md:block">
            <Coin size="75" class="hidden md:block"/>
        </div>
        <span class="font-baloo text-xl md:text-5xl font-bold text-gold">Socia</span>
    </div>
</template>

<script setup>
import {ref, onMounted, onBeforeUnmount, computed} from 'vue'
import Coin from "./Coin.vue";

const angle = ref(0)
const speed = ref(0.3)
const direction = ref(1)

const rotationStyle = computed(() => ({
    transform: `rotateY(${angle.value}deg)`,
    transition: `transform ${speed.value}s ease-in-out`,
}))

let interval

onMounted(() => {
    interval = setInterval(() => {
        // случайная скорость и направление
        const newSpeed = Math.random() * 1.5 + 0.3
        const newDir = Math.random() > 0.1 ? 1 : -1

        speed.value = newSpeed
        direction.value = newDir
        angle.value += 180 * direction.value
    }, 800)
})

onBeforeUnmount(() => clearInterval(interval))
</script>

<style scoped>
/* добавляем легкий блик */
img {
    filter: drop-shadow(0 0 6px rgba(255, 215, 0, 0.35));
    transform-style: preserve-3d;
    backface-visibility: hidden;
}
</style>
