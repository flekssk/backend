<template>
    <!-- full-bleed секция -->
    <section class="relative w-full">
        <!-- фон по ширине экрана -->
        <div class="absolute inset-0 bg-[#0b0e13]"></div>

        <!-- кастомный градиентный слой с лёгким шумом -->
        <div
            class="pointer-events-none absolute inset-0 bg-soft-shimmer"
        ></div>

        <el-carousel
            :height="bannerHeight"
            indicator-position="outside"
            trigger="click"
            arrow="always"
            class="relative"
            interval="10000"
        >
            <el-carousel-item>
                <div class="relative h-full">
                    <div class="grid grid-cols-[30%_1fr] z-10 h-full max-w-7xl mx-auto items-center px-4 md:px-6">
                        <div>
                            <div class="flex items-center gap-3 mb-3">
                                <img :src="foxImage" class="w-90" alt="Socia"/>
                            </div>
                        </div>
                        <div>
                            <h1 class="text-3xl md:text-5xl font-black leading-tight">
                                Бонус на любой депозит 25%
                            </h1>
                            <p class="text-white/80 mt-3 text-sm md:text-lg">
                                Бонус 25% при любом депозите до конца года в честь старта проекта
                            </p>
                            <div class="mt-6 flex flex-wrap gap-3">
                                <Link :href="route('slots')">
                                    <router-link
                                        to="/slots"
                                        class="px-5 py-3 rounded-xl bg-gold text-black font-semibold hover:brightness-110 btn-glossy"
                                    >Играть сейчас
                                    </router-link>
                                </Link>
                            </div>
                            <div class="mt-6 flex flex-wrap gap-2 text-xs text-white/70">
                                <span class="px-3 py-1 rounded-full bg-white/5 border border-white/10">SSL Secure</span>
                                <span
                                    class="px-3 py-1 rounded-full bg-white/5 border border-white/10">Быстрые выплаты</span>
                                <span class="px-3 py-1 rounded-full bg-white/5 border border-white/10">18+ Ответственная игра</span>
                            </div>
                        </div>
                    </div>
                </div>
            </el-carousel-item>
            <el-carousel-item>
                <div class="relative h-full">
                    <div class="grid grid-cols-[30%_1fr] z-10 h-full max-w-7xl mx-auto items-center px-4 md:px-6">
                        <div>
                            <div class="flex items-center gap-3 mb-3">
                                <img :src="coinmanImage" class="w-90" alt="Socia"/>
                            </div>
                        </div>
                        <div>
                            <h1 class="text-3xl md:text-5xl font-black leading-tight">
                                Добро пожаловать в <span class="text-gold">Socia</span>
                            </h1>
                            <p class="text-white/80 mt-3 text-sm md:text-lg">
                                Играйте и выигрывайте — часть прибыли мы направляем на благотворительность.
                            </p>
                            <div class="mt-6 flex flex-wrap gap-3">
                                <Link :href="route('slots')">
                                    <router-link
                                        to="/slots"
                                        class="px-5 py-3 rounded-xl bg-gold text-black font-semibold hover:brightness-110 btn-glossy"
                                    >Играть сейчас
                                    </router-link>
                                </Link>
                            </div>
                            <div class="mt-6 flex flex-wrap gap-2 text-xs text-white/70">
                                <span class="px-3 py-1 rounded-full bg-white/5 border border-white/10">SSL Secure</span>
                                <span
                                    class="px-3 py-1 rounded-full bg-white/5 border border-white/10">Быстрые выплаты</span>
                                <span class="px-3 py-1 rounded-full bg-white/5 border border-white/10">18+ Ответственная игра</span>
                            </div>
                        </div>
                    </div>
                </div>
            </el-carousel-item>
        </el-carousel>
    </section>
</template>

<script setup>
import coinmanImage from '@/Assets/images/heroes/coinman.png'
import foxImage from '@/Assets/images/heroes/fox.png'
import {ref, onMounted, onBeforeUnmount} from 'vue'
import {Link} from "@inertiajs/vue3";

// responsive высота карусели (чуть шире/выше на десктопе)
const bannerHeight = ref('420px')
const calcHeight = () => {
    const w = window.innerWidth
    bannerHeight.value = w < 640 ? '360px' : w < 1024 ? '460px' : '540px'
}
onMounted(() => {
    calcHeight();
    window.addEventListener('resize', calcHeight)
})
onBeforeUnmount(() => window.removeEventListener('resize', calcHeight))

// демо-выигрыши для бегущей строки
const wins = [
    {name: 'Алексей', game: 'Royal Spin', amount: 2480},
    {name: 'Мария', game: 'Fruit Rush', amount: 15420},
    {name: 'Игорь', game: 'Poker Pro', amount: 930},
    {name: 'Ольга', game: 'Gold Mine', amount: 4210},
    {name: 'Павел', game: 'Mega Slots', amount: 7100},
]
const winsRow = [...wins, ...wins]
</script>

<style>
@keyframes marquee {
    0% {
        transform: translateX(0)
    }
    100% {
        transform: translateX(-50%)
    }
}
</style>
