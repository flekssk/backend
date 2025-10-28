<template>
  <el-dropdown trigger="click">
    <div class="flex items-center gap-3 cursor-pointer select-none">
      <img :src="avatar" alt="" class="w-9 h-9 rounded-full ring-2 ring-white/10 object-cover" />
      <div class="leading-tight">
        <div class="font-semibold text-white">{{ user.name }}</div>
        <div class="text-xs text-white/70">
          Баланс: <span class="text-gold font-semibold">{{ formattedBalance }}</span>
        </div>
      </div>
      <el-icon class="text-white/70"><ArrowDown /></el-icon>
    </div>
    <template #dropdown>
      <el-dropdown-menu>
        <el-dropdown-item divided>Профиль</el-dropdown-item>
        <el-dropdown-item>Настройки</el-dropdown-item>
        <el-dropdown-item divided class="!text-red-500" @click="logout">Выйти</el-dropdown-item>
      </el-dropdown-menu>
    </template>
  </el-dropdown>
</template>

<script lang="ts">
import { ArrowDown } from '@element-plus/icons-vue'
import {mapActions, mapState} from "pinia";
import {useUserStore} from "@/Stores/user.ts";

export default {
    name: 'PlayerCard',
    components: {
        ArrowDown,
    },
    computed: {
        ...mapState(useUserStore, ['user', "currentWallet"]),
        avatar() {
            return this.user.playerProfile?.avatar ?? 'https://i.ibb.co/800000/avatar.png';
        },
        formattedBalance() {
            return this.currentWallet.balance.toLocaleString('ru-RU', {
                style: 'currency',
                currency: 'RUB',
                maximumFractionDigits: 0,
            })
        },
    },
    methods: {
        ...mapActions(useUserStore, ['logout']),
    },
}
</script>
