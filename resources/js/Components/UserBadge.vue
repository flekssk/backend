<template>
  <el-dropdown trigger="click">
    <div class="flex items-center gap-3 cursor-pointer select-none">
        <img
            v-if="avatar"
            :src="avatar"
            alt=""
            class="w-9 h-9 rounded-full ring-2 ring-white/10 object-cover"
        />

        <div
            v-else
            class="w-9 h-9 rounded-full ring-2 ring-white/10 bg-gray-600 text-white flex items-center justify-center font-medium select-none"
            aria-hidden="true"
        >
            {{ initials }}
        </div>
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
            return this.user.player_profile?.avatar;
        },
        initials() {
            const n = String(this.user.name ?? '').trim();
            if (!n) return '?';
            const parts = n.split(/\s+/).filter(Boolean);
            if (parts.length >= 2) {
                return (parts[0][0] + parts[1][0]).toUpperCase();
            }
            const word = parts[0].replace(/[^A-Za-zА-Яа-яЁё0-9]/g, '');
            return word.slice(0, 2).toUpperCase();
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
