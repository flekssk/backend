<template>
  <div
      class="relative inline-block"
      ref="root"
      tabindex="0"
      @keydown.esc="open = false"
  >
    <!-- КНОПКА -->
    <button
        type="button"
        class="provider-toggle-btn"
        @click="toggle"
    >
  <span class="truncate max-w-[180px]">
    {{ selectedProvider?.name ?? 'Выбрать провайдера' }}
  </span>
      <svg class="chevron">
        <path d="M2 4l4 4 4-4" stroke-width="2" stroke="currentColor" fill="none"/>
      </svg>
    </button>

    <!-- ДРОПДАУН -->
    <transition name="fade-scale">
      <div
          v-if="open"
          class="dropdown-panel"
      >
        <div class="py-1.5">
          <button
              v-for="item in providers"
              :key="item.id"
              type="button"
              class="w-full px-2.5 py-1.5"
              @click="onSelect(item)"
          >
            <div class="provider-row">
              <div class="provider-left">
                <img
                    v-if="item.icon"
                    :src="item.icon"
                    class="provider-icon"
                    alt="logo"
                >
                <span class="provider-name">
                  {{ item.title }}
                </span>
              </div>

              <span class="provider-badge">
                {{ item.count ?? 0 }}
              </span>
            </div>
          </button>
        </div>
      </div>
    </transition>
  </div>
</template>

<script lang="ts">
import {defineComponent} from 'vue'
import {mapActions, mapState} from 'pinia'
import {useSlotsStore} from '@/Stores/slots'

export default defineComponent({
  name: 'ProvidersSelect',
  computed: {
    ...mapState(useSlotsStore, ['providers', 'selectedProvider']),
  },
  data() {
    return {
      open: false as boolean,
    }
  },
  methods: {
    ...mapActions(useSlotsStore, ['fetchProviders', 'selectProvider']),
    toggle() {
      this.open = !this.open
    },
    onSelect(item: any) {
      this.selectProvider(item)
      this.open = false
    },
    handleClickOutside(e: MouseEvent) {
      const root = this.$refs.root as HTMLElement | null
      if (!root) return
      if (!root.contains(e.target as Node)) {
        this.open = false
      }
    },
  },
  mounted() {
    this.fetchProviders()
    document.addEventListener('click', this.handleClickOutside)
  },
  beforeUnmount() {
    document.removeEventListener('click', this.handleClickOutside)
  },
})
</script>

<style scoped>
/* ПАНЕЛЬ ДРОПДАУНА */
.dropdown-panel {
  position: absolute;
  right: 0;
  margin-top: 12px;
  width: 270px;
  max-height: 320px;
  overflow-y: auto;
  z-index: 40;
  border-radius: 18px;
  background: radial-gradient(120% 120% at 0% 0%, rgba(111, 164, 255, .15), transparent 50%),
  #050811;
  border: 1px solid rgba(255, 255, 255, 0.1);
  box-shadow: 0 20px 50px rgba(0, 0, 0, 0.85);
  padding: 4px 2px;
}

/* ОДНА СТРОКА */
.provider-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 10px;
  padding: 7px 10px;
  border-radius: 14px;
  background: transparent;
  transition: background 0.15s ease, transform 0.1s ease;
}

.provider-left {
  display: flex;
  align-items: center;
  gap: 10px;
  min-width: 0;
}

/* ЛОГО */
.provider-icon {
  width: 30px;
  height: 30px;
  object-fit: contain;
}

/* НАЗВАНИЕ — ДЕЛАЕМ ЯРКИМ И ЧИТАЕМЫМ */
.provider-name {
  font-size: 14px;
  font-weight: 500;
  color: #f7f8ff !important;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 170px;
  text-shadow: 0 1px 2px rgba(0, 0, 0, 0.6);
}

/* БЕЙДЖ ЧИСЛА */
.provider-badge {
  min-width: 32px;
  height: 24px;
  padding: 0 12px;
  border-radius: 999px;
  font-size: 12px;
  font-weight: 700;
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #b05aff, #df74ff);
}

/* ХОВЕР */
.provider-row:hover {
  background: rgba(255, 255, 255, 0.06);
  transform: translateY(-1px);
}

/* АНИМАЦИЯ */
.fade-scale-enter-active,
.fade-scale-leave-active {
  transition: opacity 0.16s ease, transform 0.16s ease;
  transform-origin: top right;
}

.fade-scale-enter-from,
.fade-scale-leave-to {
  opacity: 0;
  transform: scale(0.96);
}
.provider-toggle-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 9px 18px;
  font-size: 15px;
  font-weight: 600;
  color: #ffffff;
  border-radius: 999px;
  cursor: pointer;
  white-space: nowrap;
  transition: 0.2s ease;
  user-select: none;
  box-shadow: 0 4px 18px rgba(0,0,0,0.4);
  background: linear-gradient(135deg, #b874ff 0%, #c98fff 35%, #e6b54f 100%);
  border: 1px solid rgba(255,255,255,0.15);
}

.provider-toggle-btn:hover {
  filter: brightness(1.08);
  box-shadow: 0 4px 20px rgba(218,170,255,0.3), 0 0 12px rgba(255,215,0,0.4);
}

.provider-toggle-btn:active {
  transform: translateY(1px);
}

.provider-toggle-btn .chevron {
  width: 14px;
  height: 14px;
  opacity: 0.9;
  flex-shrink: 0;
  transition: transform 0.15s ease;
}

.provider-toggle-btn.open .chevron {
  transform: rotate(180deg);
}
</style>
