<template>
    <header class="sticky top-0 z-40 bg-[#0c1118]/90 backdrop-blur border-b border-white/10">
        <div class="w-full md:max-w-8xl mx-auto px-4 md:px-6 h-36 flex items-center gap-3">
            <!-- Бренд -->
            <router-link to="/" class="flex items-center gap-3">
                <div class="rounded-xl bg-gradient-to-br from-purple-600 to-fuchsia-600 btn-glossy">
                    <AnimatedLogo />
                </div>
            </router-link>

            <!-- Центр: навигация -->
            <nav class="hidden md:flex mx-auto items-center gap-2">
                <HeaderLink to="/">Главная</HeaderLink>
                <HeaderLink to="/slots">Слоты</HeaderLink>
            </nav>

            <!-- Право: профиль + CTA -->
            <div class="ml-auto md:flex items-center gap-3">
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

export default {
    components: {
        UserBadge,
        AnimatedLogo,
        HeaderLink: {
            props: {to: {type: String, required: true}},
            computed: {
                active() {
                    return this.$route.path === this.to
                }
            },
            template: `
                <router-link :to="to"
                             class="px-3 py-2 rounded-xl transition"
                             :class="active
            ? 'bg-white/10 text-white shadow'
            : 'text-white/80 hover:text-white hover:bg-white/5'">
                    <slot/>
                </router-link>`
        },
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
