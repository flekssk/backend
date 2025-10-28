<template>
    <el-dialog
        v-model="paymentsModalOpen"
        :before-close="onClose"
        :close-on-click-modal="false"
        :destroy-on-close="true"
        width="560px"
        center
        class="w-full auth-modal pay-modal bg-black/90 backdrop-blur-sm py-10"
    >
        <!-- HEADER -->
        <template #header>
            <div class="am-header">
                <div class="am-brand">
                    <div class="font-semibold text-white">Socia</div>
                </div>
                <div class="am-tabsbar">
                    <button :class="['am-tab', paymentsTab==='payment' && 'is-active']" @click="setPaymentTab('payment')">
                        Пополнение
                    </button>
                    <button :class="['am-tab', paymentsTab==='withdraw' && 'is-active']" @click="setPaymentTab('withdraw')">Вывод</button>
                </div>
            </div>
            <div class="am-divider-top"></div>
        </template>

        <!-- BODY -->
        <div class="am-body">
            <!-- DEPOSIT -->
            <div v-if="paymentsTab==='payment'">
                <el-form :model="dep" :rules="rulesDep" ref="formDep" label-position="top" class="am-form">
                    <el-form-item label="Метод пополнения" prop="method">
                        <el-select v-model="dep.method" placeholder="Выберите метод" class="w-full">
                            <el-option label="VISA / MasterCard" value="card"/>
                            <el-option label="СБП" value="sbp"/>
                            <el-option label="Crypto (USDT TRC20)" value="crypto"/>
                            <el-option label="FK Wallet" value="fk"/>
                        </el-select>
                    </el-form-item>

                    <el-form-item label="Сумма" prop="amount">
                        <el-input v-model.number="dep.amount" type="number" placeholder="Введите сумму"/>
                        <div class="preset-row">
                            <button v-for="p in presets" :key="'d'+p" class="preset" @click.prevent="dep.amount=p">{{
                                    p
                                }}
                            </button>
                        </div>
                        <p class="hint">Лимит: 100 ₽ — 300 000 ₽. Комиссия 0%.</p>
                    </el-form-item>

                    <el-button class="btn-primary w-full" @click="submitDeposit">Перейти к оплате</el-button>
                </el-form>
            </div>

            <!-- WITHDRAW -->
            <div v-else>
                <el-form :model="wd" :rules="rulesWd" ref="formWd" label-position="top" class="am-form">
                    <el-form-item label="Метод вывода" prop="method">
                        <el-select v-model="wd.method" placeholder="Выберите метод" class="w-full">
                            <el-option label="Банковская карта" value="bank"/>
                            <el-option label="СБП" value="sbp"/>
                            <el-option label="Crypto (USDT TRC20)" value="crypto"/>
                        </el-select>
                    </el-form-item>

                    <el-form-item label="Сумма" prop="amount">
                        <el-input v-model.number="wd.amount" type="number" placeholder="Введите сумму"/>
                        <div class="preset-row">
                            <button v-for="p in presets" :key="'w'+p" class="preset" @click.prevent="wd.amount=p">{{
                                    p
                                }}
                            </button>
                        </div>
                        <p class="hint">Минимум: 300 ₽. Комиссия <b>5%</b>. К зачислению: <b>{{ netAmount }}</b></p>
                    </el-form-item>

                    <el-button class="btn-gold w-full" @click="submitWithdraw">Создать выплату</el-button>
                    <p class="am-hint">Выплаты занимают до 24 часов, в редких случаях — до 72 часов.</p>
                </el-form>
            </div>
        </div>
    </el-dialog>
</template>

<script lang="ts">
import {defineComponent} from 'vue'
import {mapActions, mapState} from "pinia";
import {usePaymentsStore} from "../../Stores/payments";
import {useUserStore} from "../../Stores/user";

export default defineComponent({
    name: 'PaymentModal',

    data() {
        return {
            presets: [200, 500, 1500, 3000, 5000] as number[],

            formDep: null as any,
            dep: {method: '', amount: null as number | null},
            rulesDep: {
                method: [{required: true, message: 'Выберите метод', trigger: 'change'}],
                amount: [
                    {required: true, message: 'Введите сумму', trigger: 'blur'},
                    {
                        validator: (_r: any, v: number, cb: (e?: Error) => void) =>
                            v < 100 || v > 300000 ? cb(new Error('Допустимо 100–300000 ₽')) : cb(),
                        trigger: 'blur',
                    },
                ],
            },

            /* Withdraw */
            formWd: null as any,
            wd: {method: '', amount: null as number | null},
            rulesWd: {
                method: [{required: true, message: 'Выберите метод', trigger: 'change'}],
                amount: [
                    {required: true, message: 'Введите сумму', trigger: 'blur'},
                    {
                        validator: (_r: any, v: number, cb: (e?: Error) => void) =>
                            v < 300 ? cb(new Error('Минимум 300 ₽')) : cb(),
                        trigger: 'blur',
                    },
                ],
            },
        }
    },

    computed: {
        ...mapState(usePaymentsStore, ['paymentsModalOpen', 'paymentsTab']),
        ...mapState(useUserStore, ['user']),
        netAmount(): string {
            const a = this.wd.amount
            return a ? (a * 0.95).toLocaleString('ru-RU') + ' ₽' : '0 ₽'
        },
    },

    methods: {
        ...mapActions(usePaymentsStore, ['openPaymentsModal', 'closePaymentsModal', 'setPaymentTab', 'fetchPaymentMethods']),
        onClose() {
            this.closePaymentsModal()
        },

        async submitDeposit() {
            if (!this.formDep) return
            await this.formDep.validate((valid: boolean) => {
                if (!valid) return
                alert(`Пополнение: ${this.dep.amount} ₽ через ${this.dep.method}`)
                this.closePaymentsModal()
            })
        },

        async submitWithdraw() {
            if (!this.formWd) return
            await this.formWd.validate((valid: boolean) => {
                if (!valid) return
                const receive = this.wd.amount ? (this.wd.amount * 0.95).toFixed(2) : '0'
                alert(`Вывод: ${this.wd.amount} ₽ через ${this.wd.method} (к получению ~ ${receive} ₽)`)
                this.closePaymentsModal()
            })
        },
    }
})
</script>

<style scoped>
/* тот же стиль, что у модалки авторизации */
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
    color: #e9edf3
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

.btn-primary {
    background: #6d28d9;
    color: #fff;
    border: none;
    border-radius: 12px
}

.btn-primary:hover {
    background: #5b21b6
}

.btn-gold {
    background: #FFD700;
    color: #101114;
    border: none;
    border-radius: 12px;
    box-shadow: 0 10px 24px rgba(255, 215, 0, .22)
}

.btn-gold:hover {
    filter: brightness(1.05)
}

.am-hint {
    margin-top: 8px;
    color: rgba(255, 255, 255, .55);
    font-size: .75rem
}
</style>
