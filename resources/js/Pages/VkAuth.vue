<template>

</template>

<script lang="ts">

import {defineComponent} from "vue";
import {route} from "ziggy-js";
import {router} from "@inertiajs/vue3";
import {ApiClient} from "@/Services/ApiClient/ApiClient";
import {ElMessage} from "element-plus";

function loadScript(src) {
  return new Promise((resolve, reject) => {
    const s = document.createElement('script')
    s.src = src
    s.async = true
    s.onload = resolve
    s.onerror = reject
    document.head.appendChild(s)
  })
}

export default defineComponent({
  async mounted() {
    const params = Object.fromEntries(new URLSearchParams(window.location.search))

    await loadScript('https://unpkg.com/@vkid/sdk@<3.0.0/dist-sdk/umd/index.js')

    if (!('VKIDSDK' in window)) return

    const VKID = window.VKIDSDK

    VKID.Config.init({
      app: 54343120, // замените на ваш ID приложения
      redirectUrl: `${window.location.origin}/auth/vk/callback`,
      responseMode: VKID.ConfigResponseMode.Callback,
      source: VKID.ConfigSource.LOWCODE,
      scope: '' // при необходимости
    })

    VKID.Auth.exchangeCode(params.code, params.device_id)
        .then((data) => {
          ApiClient.post('/api/v1/auth/vk', data)
        })
        .catch(() => {
          ElMessage.error('Авторизоваться через VK не удалось, попробуйте позже')
          router.get(route('home'))
        })
  }

})

</script>

<style scoped>

</style>
