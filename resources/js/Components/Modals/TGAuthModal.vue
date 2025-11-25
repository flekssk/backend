<script setup lang="ts">
import { onMounted, onBeforeUnmount } from 'vue';
import axios from 'axios';
import { router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import {ApiClient} from "@/Services/ApiClient/ApiClient";
import {useUserStore} from "@/Stores/user";
import {ElMessage} from "element-plus";

const BOT_USERNAME = import.meta.env.VITE_TELEGRAM_BOT_USERNAME;

function onTelegramAuth(user: Record<string, any>) {
    // user содержит id, first_name, last_name, username, photo_url, auth_date, hash
    ApiClient.post('/api/v1/auth/telegram', user)
        .then(response => {
            const store = useUserStore()

            store.setToken(response.data.token)

            ElMessage.info('Вы успешно вошли. Приятной игры.')

            window.location.reload();
        })
        .catch((e) => {
            console.error('Telegram auth failed', e);
            // покажите уведомление об ошибке
        });
}

onMounted(() => {
    window.onTelegramAuth = onTelegramAuth;

    const s = document.createElement('script');
    s.async = true;
    s.src = 'https://telegram.org/js/telegram-widget.js?22';
    s.setAttribute('data-telegram-login', BOT_USERNAME);
    s.setAttribute('data-size', 'large');
    s.setAttribute('data-onauth', 'onTelegramAuth(user)');
    s.setAttribute('data-request-access', 'write');
    document.getElementById('tg-login-container')?.appendChild(s);
});

onBeforeUnmount(() => {
    // @ts-ignore
    delete window.onTelegramAuth;
});
</script>

<template>
    <div id="tg-login-container"></div>
</template>
