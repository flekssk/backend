import {defineStore} from "pinia"
import {ApiClient} from "../Services/ApiClient/ApiClient";
import { router } from '@inertiajs/vue3';
import {ElMessage} from "element-plus";

// Примеры интерфейсов — подстройте под реальные поля API
export interface Wallet {
    id: number
    type: string
    currency_code: string
    balance: string
}

export interface PlayerProfile {
    avatar?: string | null
    current_currency?: string | null
    vk_id?: number | null
    tg_id?: string | null
    welcome_bonus_use?: boolean
}

export interface User {
    id: number
    name: string
    email: string
    player_profile?: PlayerProfile | null
    wallets?: Wallet[] | null
}

type AuthTab = "login" | "register" | "forgot"

interface State {
    user: User | null
    token: string | null
    loading: boolean
    error: unknown | null
    authModalOpen: boolean
    authModalTab: AuthTab
    _interceptorInstalled: boolean
}

type MeResponse = User | { user: User }

export const TOKEN_STORAGE_KEY = "auth_token"

export function authToken(): string | null {
    return localStorage.getItem(TOKEN_STORAGE_KEY)
}

export const useUserStore = defineStore("user", {
    state: (): State => ({
        user: null,
        token: null,
        loading: false,
        error: null,
        authModalOpen: false,
        authModalTab: "login",
        _interceptorInstalled: false,
    }),

    getters: {
        isAuthenticated: (state) => !!state.user || !!state.token,
        currentWallet: (state) => state.user.wallets?.find(w => w.currency_code === state.user.player_profile.current_currency) ?? null,
    },

    actions: {
        init() {
            if (authToken()) this.setToken(authToken())

            this.fetchUser()
        },

        setToken(token: string | null) {
            this.token = token
            if (token) {
                localStorage.setItem(TOKEN_STORAGE_KEY, token)
            } else {
                localStorage.removeItem(TOKEN_STORAGE_KEY)
            }
        },

        async fetchUser(): Promise<User | null> {
            this.loading = true
            this.error = null
            try {
                const {data} = await ApiClient.get("/api/v1/user", {})
                const user: User = "user" in data ? data.user : data
                this.user = user ?? null
                return this.user
            } catch (e) {
                this.user = null
                this.error = e
                throw e
            } finally {
                this.loading = false
            }
        },

        async authenticate(email: string, password: string): Promise<void> {
            this.loading = true
            this.error = null
            try {
                const {data} = await ApiClient.post("/api/v1/auth/authenticate", {email, password})

                this.setToken(data.token)
                await this.fetchUser()

                ElMessage.info('Вы успешно вошли. Приятной игры.')
            } catch (e) {
                this.error = e
                throw e
            } finally {
                this.loading = false
            }
        },

        async register(name: string, email: string, password: string, passwordConfirmation: string) {
            const {data} = await ApiClient.post("/api/v1/auth/register", {
                name, email, password, password_confirmation: passwordConfirmation,
            })

            this.setToken(data.token)
            await this.fetchUser()

            ElMessage.info('Регистрация прошла успешно. Приятной игры.')
        },

        async logout(): Promise<void> {
            try {
                await ApiClient.post('/api/v1/auth/logout', {})
            } catch (e) {
                if (e.status === 401) {
                    this.setToken(null)
                    this.user = null
                }
            } finally {
                window.location.reload()
            }
        },

        openAuthModal() {
            this.authModalOpen = true
        },

        closeAuthModal() {
            this.authModalOpen = false
        },
    },
})
