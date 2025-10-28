<template>
    <el-dialog
        v-model="authModalOpen"
        :before-close="onClose"
        :close-on-click-modal="false"
        :destroy-on-close="true"
        center
        class="w-full auth-modal bg-black/90 backdrop-blur-sm py-10"
    >
        <!-- HEADER -->
        <template #header>
            <div class="am-header">
                <div class="am-brand">
                    <Coin size="32"/>
                </div>
                <div class="am-tabsbar">
                    <button :class="['am-tab', tab==='login' && 'is-active']" @click="tab='login'">Вход</button>
                    <button :class="['am-tab', tab==='register' && 'is-active']" @click="tab='register'">Регистрация
                    </button>
                </div>
            </div>
            <div class="am-divider-top"></div>
        </template>

        <!-- BODY -->
        <div class="am-body">
            <!-- LOGIN -->
            <div v-if="tab==='login'">
                <el-form :model="login" :rules="rulesLogin" ref="formLogin" label-position="top" class="am-form">
                    <el-form-item label="Email" prop="email">
                        <el-input v-model="login.email" placeholder="you@email.com"/>
                    </el-form-item>
                    <el-form-item label="Пароль" prop="password">
                        <el-input v-model="login.password" show-password placeholder="••••••••"/>
                    </el-form-item>

                    <div class="am-row">
                        <el-checkbox v-model="login.remember">Запомнить меня</el-checkbox>
                        <el-link type="primary" :underline="false">Забыли пароль?</el-link>
                    </div>

                    <el-button class="btn-primary w-full" @click="submitLogin">Войти</el-button>

                    <div class="am-or"><span>или</span></div>

                    <div class="am-social">
                        <el-button class="btn-ghost w-full">Google</el-button>
                        <el-button class="btn-ghost w-full">VK</el-button>
                    </div>
                </el-form>
            </div>

            <!-- REGISTER -->
            <div v-else>
                <el-form :model="reg" :rules="rulesReg" ref="formReg" label-position="top" class="am-form">
                    <el-form-item label="Ваше имя" prop="name">
                        <el-input v-model="reg.name" placeholder="Ваше имя"/>
                    </el-form-item>
                    <el-form-item label="Email" prop="email">
                        <el-input v-model="reg.email" placeholder="you@email.com"/>
                    </el-form-item>
                    <el-form-item label="Пароль" prop="password">
                        <el-input v-model="reg.password" show-password placeholder="Минимум 6 символов"/>
                    </el-form-item>
                    <el-form-item label="Повторите пароль" prop="confirm">
                        <el-input v-model="reg.confirm" show-password placeholder="Повторите пароль"/>
                    </el-form-item>

                    <el-checkbox v-model="reg.agree" class="mb-3">
                        Я согласен с
                        <el-link type="primary" :underline="false">правилами</el-link>
                    </el-checkbox>

                    <el-button class="btn-gold w-full" @click="submitReg">Создать аккаунт</el-button>
                    <p class="am-hint">Регистрируясь, вы подтверждаете возраст 18+ и соглашаетесь с условиями
                        сервиса.</p>
                </el-form>
            </div>
        </div>
    </el-dialog>
</template>

<script lang="ts">
import Coin from '../Coin.vue'
import {mapActions, mapState} from "pinia";
import {useUserStore} from "@/Stores/user";

export default {
    name: 'AuthModal',
    components: {Coin},

    computed: {
        ...mapState(useUserStore, ['authModalOpen'])
    },
    data() {
        return {
            visible: true,
            tab: 'login',

            // LOGIN
            login: {email: '', password: '', remember: true},
            rulesLogin: {
                email: [
                    {required: true, message: 'Введите email', trigger: 'blur'},
                    {type: 'email', message: 'Неверный формат email', trigger: ['blur', 'change']}
                ],
                password: [{required: true, message: 'Введите пароль', trigger: 'blur'}]
            },

            // REGISTER
            reg: {name: '', email: '', password: '', confirm: '', agree: true},
            rulesReg: null
        }
    },

    created() {
        this.rulesReg = {
            name: [{required: true, message: 'Введите имя', trigger: 'blur'}],
            email: [
                {required: true, message: 'Введите email', trigger: 'blur'},
                {type: 'email', message: 'Неверный email', trigger: ['blur', 'change']}
            ],
            password: [
                {required: true, message: 'Введите пароль', trigger: 'blur'},
                {min: 6, message: 'Минимум 6 символов', trigger: 'blur'}
            ],
            confirm: [{validator: this.validateConfirm, trigger: 'blur'}]
        }
    },

    methods: {
        ...mapActions(useUserStore, ['closeAuthModal', 'openAuthModal', 'authenticate', 'register']),
        onClose() {
            this.closeAuthModal()
        },

        validateConfirm(_rule, value, callback) {
            if (value !== this.reg.password) {
                callback(new Error('Пароли не совпадают'))
            } else {
                callback()
            }
        },

        async submitLogin() {
            const form = this.$refs.formLogin
            if (!form) return
            await form.validate(valid => {
                if (!valid) return
                this.authenticate(
                    this.login.email,
                    this.login.password,
                    this.login.remember,
                )
                this.closeAuthModal()
            })
        },

        async submitReg() {
            if (!this.reg.agree) {
                alert('Подтвердите согласие с правилами')
                return
            }
            const form = this.$refs.formReg
            if (!form) return
            await form.validate(valid => {
                if (!valid) return
                this.register(
                    this.reg.name,
                    this.reg.email,
                    this.reg.password,
                    this.reg.confirm,
                ).then((response) => {
                    if (response.status === 201) {
                        this.closeAuthModal()

                        this.authenticate(
                            this.reg.email,
                            this.reg.password,
                            true,
                        )
                    } else {
                        alert(
                            'Произошла ошибка при регистрации. Попробуйте ещё раз или обратитесь к администратору.'
                        )
                    }
                })
            })
        }
    }
}
</script>


<style scoped>
/* ——— фон оверлея и диалога ——— */
:deep(.el-overlay) {
    backdrop-filter: blur(3px);
}

:deep(.el-dialog.auth-modal) {
    --bg: #0f141b;
    --stroke: rgba(255, 255, 255, .08);
    background: radial-gradient(120% 80% at 15% 0%, rgba(255, 215, 0, .06), transparent 40%),
    radial-gradient(110% 70% at 100% 0%, rgba(138, 43, 226, .06), transparent 35%),
    var(--bg);
    border: 1px solid var(--stroke);
    border-radius: 16px;
    box-shadow: 0 24px 70px rgba(0, 0, 0, .55), inset 0 1px 0 rgba(255, 255, 255, .05);
    padding-bottom: 4px;
}

/* ——— header ——— */
.am-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 2px 2px 2px;
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
    font-weight: 600;
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

/* ——— body ——— */
.am-body {
    padding: 14px 12px 8px
}

.am-form :deep(.el-form-item) {
    margin-bottom: 12px
}

:deep(.el-form-item__label) {
    color: rgba(255, 255, 255, .82);
    font-weight: 600
}

/* required звёздочки — золотые */
:deep(.el-form-item__label .el-form-item__label-wrap > .is-required:not(.is-no-asterisk))::before,
:deep(.el-form-item.is-required .el-form-item__label)::before {
    color: #FFD700 !important;
}

/* поля ввода */
:deep(.el-input__wrapper) {
    background: rgba(255, 255, 255, .05);
    border: 1px solid rgba(255, 255, 255, .09);
    border-radius: 12px;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, .05);
}

:deep(.el-input__inner) {
    color: #e9edf3;
    background-color: #0e1113;
    :active {
        border: none;
    }
}

:deep(.el-input__inner::placeholder) {
    color: rgba(233, 237, 243, .55)
}

/* чекбокс/ссылки */
:deep(.el-checkbox__label), :deep(.el-link) {
    color: #e9edf3
}

/* кнопки */
.btn-primary {
    background: #6d28d9;
    color: #fff;
    border: none;
    border-radius: 12px;
}

.btn-primary:hover {
    background: #5b21b6
}

.btn-gold {
    background: #FFD700;
    color: #101114;
    border: none;
    border-radius: 12px;
    box-shadow: 0 10px 24px rgba(255, 215, 0, .22);
}

.btn-gold:hover {
    filter: brightness(1.05)
}

.btn-ghost {
    background: rgba(255, 255, 255, .04);
    border: 1px solid rgba(255, 255, 255, .08);
    color: #e9edf3;
    border-radius: 12px;
}

.btn-ghost:hover {
    background: rgba(255, 255, 255, .07)
}

/* доп. элементы */
.am-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 8px
}

.am-social {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px
}

.am-or {
    position: relative;
    text-align: center;
    margin: 12px 0;
    color: rgba(255, 255, 255, .6)
}

.am-or::before {
    content: "";
    position: absolute;
    left: 0;
    right: 0;
    top: 50%;
    height: 1px;
    transform: translateY(-50%);
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, .15), transparent)
}

.am-or span {
    position: relative;
    padding: 0 8px;
    background: var(--bg)
}

.am-hint {
    margin-top: 8px;
    color: rgba(255, 255, 255, .55);
    font-size: .75rem
}
</style>
