<template>
    <div class="support-widget">
        <!-- Плавающая кнопка -->
        <button
            class="support-button"
            @click="toggle"
            aria-label="Открыть поддержку"
        >
            💬
        </button>

        <!-- Затемнение фона (по желанию можно убрать) -->
        <div v-if="isOpen" class="support-backdrop" @click="close"></div>

        <!-- Всплывающее окно -->
        <transition name="support-fade">
            <div v-if="isOpen" class="support-popup">
                <div class="support-header">
                    <div class="support-title">Поддержка Socia</div>
                    <button class="support-close" @click="close">×</button>
                </div>

                <div class="support-body">
                    <p class="support-text">
                        Напишите нам в Telegram:
                    </p>

                    <div class="support-code-block" v-if="user">
                        <span class="support-code">{{ user.id }}</span>

                        <button
                            v-if="!loading && !error && code"
                            class="support-copy"
                            @click="copyCode"
                        >
                            Копировать
                        </button>
                    </div>
                    <p class="support-text" v-if="user">Укажите этот код, чтобы мы могли идентифицировать ваш аккаунт</p>

                    <a
                        class="support-tg-button"
                        :href="telegramLink"
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        Открыть Telegram
                    </a>

                    <p class="support-hint">
                        Код нужен только для сотрудников поддержки. Никому больше его не
                        сообщайте.
                    </p>
                </div>
            </div>
        </transition>
    </div>
</template>

<script>
import {mapState} from "pinia";
import {useUserStore} from "@/Stores/user.js";

export default {
    name: 'Help',
    props: {
        /**
         * Ник вашего аккаунта поддержки без @, например: 'socia_support'
         */
        telegramUsername: {
            type: String,
            required: true
        }
    },
    data() {
        return {
            isOpen: false,
            code: '',
            loading: false,
            error: false
        }
    },
    computed: {
        ...mapState(useUserStore, ['user']),
        telegramLink() {
            return `https://t.me/${this.telegramUsername}`
        }
    },
    watch: {
        isOpen(open) {}
    },
    methods: {
        toggle() {
            this.isOpen = !this.isOpen
        },
        close() {
            this.isOpen = false
        },
        async copyCode() {
            if (!this.code) return
            try {
                await navigator.clipboard.writeText(this.code)
            } catch {
                // молча проглатываем, чтобы не рушить UX
            }
        },
    }
}
</script>

<style scoped>
.support-widget {
    position: fixed;
    right: 24px;
    bottom: 24px;
    z-index: 2000;
}

/* Плавающая кнопка */
.support-button {
    width: 56px;
    height: 56px;
    border-radius: 999px;
    border: none;
    cursor: pointer;
    background: #ffd54d;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.45);
    font-size: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.15s ease, box-shadow 0.15s ease;
}
.support-button:hover {
    transform: translateY(-1px);
    box-shadow: 0 14px 32px rgba(0, 0, 0, 0.6);
}

/* затемнение */
.support-backdrop {
    position: fixed;
    inset: 0;
    background: transparent;
}

/* всплывашка */
.support-popup {
    position: absolute;
    right: 0;
    bottom: 72px;
    width: 320px;
    background: #10131a;
    border-radius: 16px;
    box-shadow: 0 18px 40px rgba(0, 0, 0, 0.7);
    border: 1px solid rgba(255, 255, 255, 0.06);
    color: #fff;
    font-family: system-ui, -apple-system, BlinkMacSystemFont, 'SF Pro Text',
    'Segoe UI', sans-serif;
}

/* анимация */
.support-fade-enter-active,
.support-fade-leave-active {
    transition: opacity 0.18s ease, transform 0.18s ease;
}
.support-fade-enter-from,
.support-fade-leave-to {
    opacity: 0;
    transform: translateY(8px);
}

.support-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 16px 8px;
}
.support-title {
    font-weight: 600;
    font-size: 15px;
}
.support-close {
    border: none;
    background: transparent;
    color: #888;
    cursor: pointer;
    font-size: 20px;
    line-height: 1;
}
.support-close:hover {
    color: #fff;
}

.support-body {
    padding: 4px 16px 14px;
    font-size: 14px;
}
.support-text {
    margin: 0 0 10px;
    color: #c5c5c5;
    line-height: 1.4;
}

.support-code-block {
    background: #151925;
    border-radius: 12px;
    padding: 10px 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 10px;
}
.support-code {
    font-family: 'SF Mono', ui-monospace, Menlo, Monaco, Consolas, 'Liberation Mono',
    'Courier New', monospace;
    font-weight: 600;
    color: #ffd54d;
}
.support-code-error {
    color: #ff6b6b;
}
.support-copy {
    margin-left: 8px;
    font-size: 12px;
    border-radius: 999px;
    border: none;
    padding: 4px 10px;
    background: #272b3a;
    color: #f0f0f0;
    cursor: pointer;
}
.support-copy:hover {
    background: #343a4d;
}

.support-tg-button {
    display: block;
    text-align: center;
    margin-top: 4px;
    padding: 10px 12px;
    border-radius: 999px;
    background: #2aabee;
    color: #fff;
    font-weight: 600;
    font-size: 14px;
    text-decoration: none;
    transition: background 0.15s ease, transform 0.1s ease;
}
.support-tg-button:hover {
    background: #1b9ad9;
    transform: translateY(-1px);
}

.support-hint {
    margin-top: 8px;
    font-size: 11px;
    color: #8a8a8a;
    line-height: 1.4;
}
</style>
