<template>
    <el-dialog
        v-model="paymentsModalOpen"
        :before-close="onClose"
        :close-on-click-modal="false"
        :destroy-on-close="true"
        width="560px"
        center
        class="w-full lg:w-[80vw] max-w-screen-xl auth-modal pay-modal bg-black/90 backdrop-blur-sm py-10 z-[9999]"
    >
        <!-- HEADER -->
        <template #header>
            <div class="am-header">
                <div class="am-brand">
                    <div class="am-brand">
                        <Coin size="32"/>
                    </div>
                </div>
                <div class="am-tabsbar">
                    <button :class="['am-tab', paymentsTab==='payment' && 'is-active']"
                            @click="setPaymentTab('payment')">
                        Пополнение
                    </button>
                    <button :class="['am-tab', paymentsTab==='withdraw' && 'is-active']"
                            @click="setPaymentTab('withdraw')">Вывод
                    </button>
                </div>
            </div>
            <div class="am-divider-top"></div>
        </template>

        <!-- BODY -->
        <div class="am-body">
            <!-- DEPOSIT -->
            <div v-if="paymentsTab === 'payment'">
                <template v-if="activePayment?.status.status === 0">
                    <div class="space-y-3" v-if="activePayment.invoice instanceof SBPInvoiceData">
                        <!-- предупреждение о смене суммы -->
                        <el-alert
                            v-if="activePayment.amount !== activePayment.invoice.amount"
                            type="warning"
                            show-icon
                            class="!bg-amber-500/10 !border-amber-400/40 !text-amber-200"
                        >
                            Провайдер изменил сумму. Пожалуйста, оплатите <b>новую сумму</b>, иначе платёж не будет
                            зачислен.
                        </el-alert>

                        <!-- шапка инвойса -->
                        <div class="flex items-center justify-between">
                            <div class="text-sm">
                                <span class="text-white/60 mr-2">Истекает через</span>
                                <span v-if="!isExpired">{{ formatted }}</span>
                                <span v-else>00:00</span>
                            </div>
                        </div>

                        <!-- сумма -->
                        <div class="flex items-center gap-3">
                            <div class="text-white/70">Сумма к оплате:</div>
                            <div
                                :class="['text-2xl font-extrabold', activePayment.amount !== activePayment.invoice.amount && 'pulse text-amber-300']">
                                {{ activePayment.invoice.amount.toLocaleString('ru-RU') }} ₽
                            </div>
                        </div>

                        <!-- реквизиты -->
                        <div class="grid md:grid-cols-2 gap-3">
                            <FieldCard label="Банк" :value="activePayment.invoice.bank"/>
                            <FieldCard label="Получатель" :value="activePayment.invoice.name"/>
                            <FieldCard label="Телефон/СБП" :value="activePayment.invoice.account"/>
                        </div>

                        <!-- прогресс жизни инвойса -->
                        <el-progress :percentage="lifePercent" :stroke-width="10" status="success"/>

                        <!-- действия -->
                        <div class="flex flex-col sm:flex-row gap-2">
                            <el-button class="btn-gold flex-1" @click="markPayed(activePayment.id)">
                                Я оплатил
                            </el-button>
                            <el-button @click="cancelPayment(activePayment.id)" class="btn-cancel flex-1">
                                Отменить
                            </el-button>
                        </div>

                        <p class="am-hint">
                            После оплаты удерживайте приложение/страницу открытой — мы автоматически проверяем статус
                            платежа.
                        </p>
                    </div>
                    <div v-if="activePayment.invoice instanceof RedirectInvoiceData">
                        <div class="flex items-center gap-3">
                            <h1>Провайдер: {{ activePayment.payment_provider_method }}</h1>
                        </div>
                        <div class="flex items-center justify-between mb-2">
                            <div class="text-sm">
                                <span class="text-white/60 mr-2">Истекает через</span>
                                <span v-if="!isExpired">{{ formatted }}</span>
                                <span v-else>00:00</span>
                            </div>
                        </div>
                        <div>
                            <div class="grid md:grid-cols-1 gap-3">
                                <FieldCard label="Ссылка" :value="activePayment.invoice.url"/>
                                <p>
                                    Скопируйте ссылку или нажмите кнопку "Оплатить"
                                </p>
                            </div>
                        </div>
                        <div class="text-center">
                            <a :href="activePayment.invoice.url" class="mr-2" target="_blank">
                                <el-button class="btn-gold flex-1">
                                    Оплатить
                                </el-button>
                            </a>
                            <el-button @click="cancelPayment(activePayment.id)" class="btn-cancel flex-1">
                                Отменить
                            </el-button>
                        </div>
                    </div>
                </template>
                <template v-else-if="activePayment">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <div class="text-sm">
                                <span class="text-white/60 mr-2">Статус</span>
                                <span>{{ activePayment.status.name() }}</span>
                            </div>
                        </div>
                        <div class="text-center">
                            <el-button @click="fetchActivePayments()" class="btn-gold flex-1">
                                Обновить
                            </el-button>
                            <el-button @click="awaitPayment(activePayment.id)" class="btn-cancel flex-1">
                                Новый платеж
                            </el-button>
                        </div>
                    </div>
                </template>
                <template v-else>
                    <el-form :model="dep" ref="formDep" label-position="top" class="am-form">
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 mb-4">
                            <button
                                v-for="paymentProvider in availablePaymentMethods()"
                                :key="paymentProvider.provider"
                                type="button"
                                @click="selectedPaymentProvider = paymentProvider.provider; selectedPaymentMethod = paymentProvider.method;"
                                :class="[
                              'relative flex items-center gap-3 rounded-xl px-3 py-2 text-left transition',
                              'border bg-slate-800/60 border-slate-700/70 hover:bg-slate-800/80 hover:border-slate-600/70 shadow-sm',
                              selectedPaymentProvider === paymentProvider.provider
                                ? 'border-indigo-500 ring-2 ring-indigo-500/50 bg-indigo-500/10'
                                : ''
                            ]"
                            >
                                <img
                                    :src="paymentProvider.icon"
                                    alt=""
                                    class="w-10 h-10 object-contain"
                                />

                                <div class="flex flex-col">
                                    <div class="text-slate-100 font-medium leading-5">
                                        {{ paymentProvider.title }}
                                    </div>
                                    <div class="text-xs text-slate-400 mt-0.5">
                                        Мин. {{ formatMoney(paymentProvider.min) }}
                                        <span
                                            v-if="Number(paymentProvider.bonus_percent) > 0"
                                            class="ml-1 text-emerald-400 font-medium"
                                        >
                                        +{{ paymentProvider.bonus_percent }}%
                                    </span>
                                    </div>
                                </div>
                            </button>
                        </div>

                        <el-form-item label="Сумма" prop="amount" class="mb-3">
                            <el-input v-model.number="dep.amount" type="number" placeholder="Введите сумму"/>
                        </el-form-item>
                        <div class="preset-row mb-4">
                            <button v-for="p in presets" :key="'d'+p" class="preset" @click.prevent="dep.amount=p">
                                {{ p }}
                            </button>
                        </div>

                        <el-button class="btn-primary w-full" :disabled="loading" @click="submitDeposit">Перейти к
                            оплате
                        </el-button>
                    </el-form>
                </template>
            </div>

            <!-- WITHDRAW -->
            <div v-else>
                <el-form :model="wd" ref="formDep" label-position="top" class="am-form">
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 mb-4">
                        <button
                            v-for="withdrawProvider in availableWithdrawMethods()"
                            :key="`${withdrawProvider.provider}:${withdrawProvider.method}`"
                            type="button"
                            @click="selectedWithdraw = withdrawProvider"
                            :class="[
                              'relative flex items-center gap-3 rounded-xl px-3 py-2 text-left transition',
                              'border bg-slate-800/60 border-slate-700/70 hover:bg-slate-800/80 hover:border-slate-600/70 shadow-sm',
                              selectedWithdrawMethod.provider === withdrawProvider.provider && selectedWithdrawMethod.method === withdrawProvider.method
                                ? 'border-indigo-500 ring-2 ring-indigo-500/50 bg-indigo-500/10'
                                : ''
                            ]"
                        >
                            <img
                                :src="withdrawProvider.icon"
                                alt=""
                                class="w-10 h-10 object-contain"
                            />

                            <div class="flex flex-col">
                                <div class="text-slate-100 font-medium leading-5">
                                    {{ withdrawProvider.title }}
                                </div>
                                <div class="text-xs text-slate-400 mt-0.5">
                                    Мин. {{ formatMoney(withdrawProvider.min) }}
                                </div>
                            </div>
                        </button>
                    </div>

                    <el-form-item label="Сумма" prop="amount">
                        <el-input v-model.number="wd.amount" type="number" placeholder="Введите сумму"/>
                        <div class="preset-row mb-4">
                            <button v-for="p in presets" :key="'w'+p" class="preset" @click.prevent="wd.amount=p">{{
                                    p
                                }}
                            </button>
                        </div>
                    </el-form-item>

                    <el-form-item :label="selectedWithdrawMethod.wallet_input_title" prop="wallet">
                        <div class="w-full mb-4">
                            <el-input v-model.number="wd.wallet" type="text"
                                      :placeholder="selectedWithdrawMethod.wallet_input_placeholder"/>
                        </div>
                    </el-form-item>
                    <Select v-if="selectedWithdrawMethod.variants" v-model="wd.variant"
                            :options="selectedWithdrawMethod.variants"/>

                    <div class="flex items-center gap-3 mb-2">
                        <p class="hint">Минимум: {{ selectedWithdrawMethod.min }} ₽. Комиссия
                            <b>{{ selectedWithdrawMethod.commission_percents }}%</b>. К зачислению: <b>{{
                                    netAmount
                                }}</b></p>
                    </div>

                    <el-button class="btn-primary w-full" :disabled="loading" @click="submitWithdraw">Вывод</el-button>
                </el-form>
            </div>
        </div>
    </el-dialog>
</template>

<script lang="ts">
import {defineComponent, ref} from 'vue'
import {mapActions, mapState} from "pinia";
import {RedirectInvoiceData, SBPInvoiceData, usePaymentsStore} from "@/Stores/payments";
import {useUserStore} from "@/Stores/user";
import {formatMoney} from "@/Utils/StringUtils";
import FieldCard from "../Forms/FieldCard.vue";
import Select from "../Forms/Select.vue";
import Coin from '../Coin.vue'

export default defineComponent({
    name: 'PaymentModal',

    components: {FieldCard, Coin, Select},

    data() {
        return {
            loading: ref(false),
            presets: [500, 1500, 3000, 5000] as number[],

            dep: {method: '', amount: null as number | null},
            wd: {
                method: '',
                amount: null as number | null,
                wallet: null as string | null,
                variant: null as string | null
            },

            selectedPaymentProvider: ref<string | null>('cryptobot'),
            selectedPaymentMethod: ref<string | null>('cryptobot'),

            selectedWithdraw: ref<any>(null),
            now: Date.now() as number,
            timer: undefined as number | undefined,
            durationMs: 10 * 60
        }
    },

    computed: {
        RedirectInvoiceData() {
            return RedirectInvoiceData
        },
        SBPInvoiceData() {
            return SBPInvoiceData
        },
        ...mapState(usePaymentsStore, ['paymentsModalOpen', 'paymentsTab', 'activePayment']),
        ...mapState(useUserStore, ['user']),
        netAmount(): string {
            const a = this.wd.amount

            return a ? (a - (a / 100 * this.selectedWithdraw.commission_percents)).toLocaleString('ru-RU') + ' ₽' : '0 ₽'
        },
        lifePercent(): number {
            if (!this.activePayment.invoice) return 0

            const total = 10 * 60 * 1000
            const left = this.remainingSeconds

            return Math.round(100 - (left / total) * 100)
        },
        remainingSeconds(): number {
            return this.activePayment.remainingSecondsToPay
        },
        isExpired(): boolean {
            return this.remainingSeconds <= 0;
        },
        formatted(): string {
            const s = Math.floor(this.activePayment.remainingSecondsToPay);
            const m = Math.floor(s / 60);
            return `${String(m).padStart(2, '0')}:${String(s % 60).padStart(2, '0')}`;
        },
        selectedWithdrawMethod(): any {
            if (this.selectedWithdraw === null) {
                return this.availableWithdrawMethods()[0]
            }

            return this.selectedWithdraw
        }
    },

    methods: {
        formatMoney,
        ...mapActions(
            usePaymentsStore,
            [
                'openPaymentsModal',
                'closePaymentsModal',
                'setPaymentTab',
                'fetchPaymentMethods',
                'fetchActivePayments',
                'createPayment',
                'cancelPayment',
                'createWithdraw',
                'markPayed',
                'awaitPayment',
            ]),
        ...mapState(usePaymentsStore, ['availablePaymentMethods', 'availableWithdrawMethods']),
        onClose() {
            this.closePaymentsModal()
        },

        submitDeposit() {
            if (!this.dep.amount) return

            this.loading = true

            this.createPayment({
                amount: this.dep.amount,
                paymentProvider: this.selectedPaymentProvider,
                paymentProviderMethod: this.selectedPaymentMethod,
            }).finally(() => {
                this.loading = false
            })
        },

        async submitWithdraw() {
            if (!this.wd.amount) return
            if (!this.wd.wallet) return

            this.loading = true

            await this.createWithdraw({
                amount: this.wd.amount,
                wallet: this.wd.wallet,
                paymentProvider: this.selectedWithdrawMethod.provider,
                paymentProviderMethod: this.selectedWithdrawMethod.method,
                paymentProviderVariant: this.wd.variant
            }).finally(() => this.loading = false)
        },
        tick() {
            this.activePayment.remainingSecondsToPay -= 1

            if (this.isExpired) this.stopTimer();
        },
        startTimer() {
            if (this.activePayment.remainingSecondsToPay === null) return
            this.tick()
            this.timer = window.setInterval(() => this.tick(), 1000)
        },
        stopTimer() {
            if (this.timer) {
                this.fetchActivePayments()
                clearInterval(this.timer);
                this.timer = undefined;
            }
        },
    },
    watch: {
        activePayment: {
            handler(newVal, oldVal) {
                if (newVal.remainingSecondsToPay !== null) {
                    this.stopTimer()
                    this.startTimer()
                }
            }
        },
    },
})
</script>

<style scoped>
:deep(.el-dialog.pay-modal) {
    --bg: #0f141b;
    background: radial-gradient(120% 80% at 15% 0%, rgba(255, 215, 0, .06), transparent 40%),
    radial-gradient(110% 70% at 100% 0%, rgba(138, 43, 226, .06), transparent 35%),
    var(--bg);
    border: 1px solid rgba(255, 255, 255, .08);
    border-radius: 16px;
    box-shadow: 0 24px 70px rgba(0, 0, 0, .55), inset 0 1px 0 rgba(255, 255, 255, .05);
}

/* хедер/табы */
.am-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 2px 2px
}

.am-brand {
    display: flex;
    align-items: center;
    gap: .5rem
}

.am-tabsbar {
    display: flex;
    gap: .25rem;
    background: rgba(255, 255, 255, .04);
    padding: 4px;
    border-radius: 12px;
    border: 1px solid rgba(255, 255, 255, .06)
}

.am-tab {
    color: rgba(255, 255, 255, .7);
    padding: 7px 12px;
    border-radius: 10px;
    font-weight: 600
}

.am-tab.is-active {
    color: #fff;
    background: rgba(255, 255, 255, .08)
}

.am-divider-top {
    height: 2px;
    background: linear-gradient(90deg, #b084ff, #FFD700);
    border-radius: 1px
}

/* тело */
.am-body {
    padding: 14px 12px 10px
}

.am-form :deep(.el-form-item) {
    margin-bottom: 12px
}

:deep(.el-form-item__label) {
    color: rgba(255, 255, 255, .85);
    font-weight: 600
}

:deep(.el-input__wrapper) {
    background: rgba(255, 255, 255, .05);
    border: 1px solid rgba(255, 255, 255, .09);
    border-radius: 12px;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, .05)
}

:deep(.el-input__inner) {
    color: #e9edf3;
    background-color: #0e1113;
}

:deep(.el-input__inner::placeholder) {
    color: rgba(233, 237, 243, .55)
}

.preset-row {
    display: flex;
    gap: .5rem;
    flex-wrap: wrap;
    margin-top: .5rem
}

.preset {
    padding: .35rem .6rem;
    border-radius: 10px;
    background: rgba(255, 255, 255, .05);
    border: 1px solid rgba(255, 255, 255, .08);
    color: #e9edf3
}

.preset:hover {
    background: rgba(255, 255, 255, .08)
}

.hint {
    margin-top: .35rem;
    color: rgba(255, 255, 255, .6);
    font-size: .8rem
}

.am-hint {
    margin-top: 8px;
    color: rgba(255, 255, 255, .55);
    font-size: .75rem
}
</style>
