<template>
  <div class="p-3 rounded-xl border border-white/10 bg-white/[0.04]">
    <div class="text-xs text-white/60 mb-1">{{ label }}</div>
    <div class="flex items-center justify-between gap-2">
      <div class="text-white/90">{{ value }}</div>
      <button class="px-2 py-1 text-xs rounded-lg bg-white/10 hover:bg-white/15" @click="$emit('copy', value)"
              @click.stop="copy(value)">Копировать
      </button>
    </div>
  </div>
</template>

<script lang="ts">
import {defineComponent} from "vue";
import {ElMessage} from "element-plus";

export default defineComponent({
  name: 'FieldCard',
  props: {label: String, value: String},
  methods: {
    async copy(val: any) {
      try { await navigator.clipboard.writeText(val); ElMessage.success('Скопировано') }
      catch { ElMessage.error('Не удалось скопировать') }
    }
  }
})
</script>


<style scoped>

</style>
