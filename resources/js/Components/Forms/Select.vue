<template>
    <div class="ui-select" @click="toggle">
    <span class="ui-select__value">
      {{ modelValue ? getLabel(modelValue) : placeholder }}
    </span>

        <svg class="ui-select__arrow" :class="{ open: isOpen }" width="18" height="18" viewBox="0 0 24 24">
            <path fill="currentColor" d="M7 10l5 5 5-5z"/>
        </svg>

        <transition name="fade">
            <ul v-if="isOpen" class="ui-select__dropdown">
                <li
                    v-for="item in items"
                    :key="item.value"
                    :class="{ active: item.value === modelValue }"
                    @click.stop="select(item.value)"
                >
                    {{ item.label }}
                </li>
            </ul>
        </transition>
    </div>
</template>

<script setup>
import { ref } from 'vue'

const props = defineProps({
    modelValue: [String, Number, Object],
    items: { type: Array, required: true },
    placeholder: { type: String, default: 'Выберите' }
})

const emit = defineEmits(['update:modelValue'])

const isOpen = ref(false)

function toggle() {
    isOpen.value = !isOpen.value
}

function select(v) {
    emit('update:modelValue', v)
    isOpen.value = false
}

function getLabel(v) {
    const found = props.items.find(i => i.value === v)
    return found ? found.label : ''
}

document.addEventListener('click', () => (isOpen.value = false))
</script>

<style scoped>
.ui-select {
    position: relative;
    background: #0e1014;
    border: 1px solid #1d2229;
    border-radius: 12px;
    height: 52px;
    padding: 0 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    cursor: pointer;
    user-select: none;
    transition: border-color .25s, box-shadow .25s;
}
.ui-select:hover {
    border-color: #6f41ff;
}
.ui-select__value {
    color: #fff;
    font-size: 15px;
}
.ui-select__value:empty {
    color: #707070;
}
.ui-select__arrow {
    color: #9b9b9b;
    transition: transform .25s;
}
.ui-select__arrow.open {
    transform: rotate(180deg);
}

/* Dropdown */
.ui-select__dropdown {
    position: absolute;
    top: calc(100% + 8px);
    left: 0;
    width: 100%;
    background: #0e1014;
    border: 1px solid #1d2229;
    border-radius: 12px;
    padding: 6px 0;
    list-style: none;
    z-index: 999;
}
.ui-select__dropdown li {
    padding: 10px 14px;
    color: #d8d8d8;
    font-size: 14px;
    cursor: pointer;
    border-radius: 8px;
    margin: 2px 6px;
}
.ui-select__dropdown li:hover,
.ui-select__dropdown li.active {
    background: rgba(124, 58, 237, 0.25);
    color: #fff;
}

/* Fade animation */
.fade-enter-active, .fade-leave-active { transition: opacity .2s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
