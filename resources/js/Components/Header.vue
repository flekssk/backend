<template>
    <header class="sticky top-0 z-[1000] bg-[#070A12]/95 backdrop-blur border-b border-white/5 shadow-[0_2px_12px_rgba(0,0,0,0.4)]">
        <div class="w-full md:max-w-8xl px-2 md:px-6 h-36 flex items-center gap-3 mx-auto max-w-7xl">
            <!-- Бренд -->
            <Link :href="route('home')" class="flex items-center gap-3">
                <div class="rounded-xl bg-gradient-to-br from-purple-600 to-fuchsia-600 btn-glossy">
                    <AnimatedLogo />
                </div>
            </Link>

            <!-- Центр: навигация -->
            <nav class="hidden mx-auto md:flex items-center gap-6 text-sm font-medium">
                <Link :href="route('home')" class="relative text-white/80 hover:text-white transition">
                    Главная
                </Link>
                <Link :href="route('slots')"
                      class="relative text-white/80 hover:text-white transition"
                >
                    Слоты
                </Link>
            </nav>

            <!-- Право: профиль + CTA -->
            <div class="ml-auto flex items-center gap-3">
                <button
                    class="text-xs mr-2 md:mr-0 px-2 py-2 md:px-3 md:py-2 rounded-xl bg-gold text-black font-semibold hover:brightness-110 transition shadow"
                    @click="openPaymentsModal()"
                    v-if="user"
                >
                    Пополнить
                </button>
                <button
                    class="px-2 py-2 md:px-3 md:py-2 rounded-xl border border-white/15 text-white/90 hover:bg-white/5 transition text-left"
                    @click="openAuthModal()"
                    v-if="!user"
                >
                    Регистрация / Вход
                </button>
                <UserBadge v-if="user" />
            </div>
        </div>

        <!-- Мобильное меню -->
        <transition name="fade" v-if="user">
            <div v-if="open" class="md:hidden border-t border-white/10 bg-[#0c1118]/95">
                <div class="max-w-7xl mx-auto px-4 py-3 flex flex-col gap-2">
                    <div class="mt-2 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <img :src="user.avatar" class="w-9 h-9 rounded-full ring-2 ring-white/10 object-cover"/>
                            <div>
                                <div class="text-white font-semibold leading-tight">{{ user.name }}</div>
                                <div class="text-xs text-white/70">
                                    Баланс: <span class="text-gold font-semibold">{{ mobileBalance }}</span>
                                </div>
                            </div>
                        </div>
                        <router-link
                            to="/pay"
                            class="px-3 py-2 rounded-xl bg-gold text-black font-semibold hover:brightness-110 transition shadow"
                            @click="open=false"
                        >Пополнить
                        </router-link>
                    </div>
                </div>
            </div>
        </transition>
    </header>
</template>

<script lang="ts">
import {mapActions, mapState} from "pinia";
import AnimatedLogo from "@/Components/AnimatedLogo.vue";
import UserBadge from "@/Components/UserBadge.vue";
import {useUserStore} from "../Stores/user";
import {usePaymentsStore} from "../Stores/payments";
import {Link} from "@inertiajs/vue3";
import {route} from 'ziggy-js';

export default {
    components: {
        Link,
        UserBadge,
        AnimatedLogo,
        MobileLink: {
            props: {to: {type: String, required: true}},
            template: `
                <router-link :to="to"
                             class="px-3 py-2 rounded-xl text-white/90 hover:bg-white/5 transition">
                    <slot/>
                </router-link>`
        }
    },
    computed: {
        ...mapState(useUserStore, ['user'])
    },
    methods: {
        ...mapActions(useUserStore, ['openAuthModal']),
        ...mapActions(usePaymentsStore, ['openPaymentsModal']),
        route
    },
}
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
    transition: opacity .18s ease
}

.fade-enter-from, .fade-leave-to {
    opacity: 0
}
</style>
