<template>
    <button
        type="button"
        class="auth-social auth-social--vk"
        @click="authVk()"
    >
      <span class="auth-social__inner">
        <Icon icon="simple-icons:vk" class="auth-social__icon"/>
        <span>Войти через VK</span>
      </span>
    </button>
</template>

<script lang="ts">

import {defineComponent} from "vue";
import {Icon} from '@iconify/vue'
import {route} from "ziggy-js";
import {router} from "@inertiajs/vue3";

export default defineComponent({
    components: {Icon},
    methods: {
        authVk() {
            location.href = route('auth.vk.redirect')
        }
    }
})

</script>

<style scoped>
.auth-social {
    width: 200px;
    border: none;
    background: transparent;
    padding: 0;
    border-radius: 999px;
    cursor: pointer;
}

.auth-social__inner {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    height: 40px;
    border-radius: inherit;
    font-size: 15px;
    font-weight: 600;
}

/* VK-кнопка в фирменном стиле, но всё ещё в духе Socia */
.auth-social--vk .auth-social__inner {
    background: linear-gradient(135deg, #0077ff, #4a8dff);
    color: #ffffff;
    box-shadow: 0 0 0 1px rgba(255, 255, 255, 0.12),
    0 14px 30px rgba(0, 0, 0, 0.7);
}

.auth-social--vk:hover .auth-social__inner {
    filter: brightness(1.06);
    box-shadow: 0 0 0 1px rgba(255, 255, 255, 0.18),
    0 16px 36px rgba(0, 0, 0, 0.8);
}

.auth-social--vk:active .auth-social__inner {
    transform: translateY(1px);
}

.auth-social--tg .auth-social__inner {
    background: linear-gradient(135deg, #28a9e0, #44c3ff);
    color: #ffffff;
    box-shadow: 0 0 0 1px rgba(255, 255, 255, 0.12),
    0 14px 30px rgba(0, 0, 0, 0.7);
}

.auth-social--tg:hover .auth-social__inner {
    filter: brightness(1.07);
    box-shadow: 0 0 0 1px rgba(255, 255, 255, 0.18),
    0 16px 36px rgba(0, 0, 0, 0.8);
}

.auth-social--tg:active .auth-social__inner {
    transform: translateY(1px);
}

.auth-social__icon {
    width: 22px;
    height: 22px;
    flex-shrink: 0;
    margin-right: 10px;
    color: #ffffff;
}
#tg-login-container {
    width: 234px;
}
</style>
