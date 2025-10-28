import {defineStore} from "pinia";
import {ApiClient} from "../Services/ApiClient/ApiClient";
import { useUserStore } from "./user";

type MethodMeta = {
    title: string;
    icon: string;
    wallet_input_title?: string;
    wallet_input_placeholder?: string;
};

type ProviderPaymentItem = {
    method: string;
    min: number;
    max: number;
    hot?: boolean;
    bonus_percent?: number;
};

type ProviderWithdrawItem = {
    method: string;
    min: number;
    max: number;
    hot?: boolean;
    commission_percents?: number;
    variants?: unknown[];
};

type ProviderDto = {
    provider: string;
    payment?: ProviderPaymentItem[];
    withdraw?: ProviderWithdrawItem[];
};

type PaymentMethodView = {
    title: string;
    icon: string;
    min: number;
    max: number;
    hot?: boolean;
    bonus_percent?: number;
    method: string;
    provider: string;
};

type WithdrawMethodView = {
    title: string;
    icon: string;
    min: number;
    max: number;
    hot?: boolean;
    method: string;
    provider: string;
    commission_percents?: number;
    wallet_input_title?: string;
    wallet_input_placeholder?: string;
    variants?: unknown[];
};

type State = {
    paymentsModalOpen: boolean;
    paymentsTab: "payment" | "withdraw";
    paymentsProviders: unknown[];
    withdrawsProviders: unknown[];
    providers: ProviderDto[];
    methods: Record<string, MethodMeta>;
};

function buildPaymentMethodItem(
    meta: MethodMeta | undefined,
    cfg: ProviderPaymentItem,
    providerId: string
): PaymentMethodView | null {
    if (!meta) return null;
    return {
        title: meta.title,
        icon: meta.icon,
        min: cfg.min,
        max: cfg.max,
        hot: cfg.hot,
        bonus_percent: cfg.bonus_percent,
        method: cfg.method,
        provider: providerId,
    };
}

function buildWithdrawMethodItem(
    meta: MethodMeta | undefined,
    cfg: ProviderWithdrawItem,
    providerId: string
): WithdrawMethodView | null {
    if (!meta) return null;
    const item: WithdrawMethodView = {
        title: meta.title,
        icon: meta.icon,
        min: cfg.min,
        max: cfg.max,
        hot: cfg.hot,
        method: cfg.method,
        provider: providerId,
        commission_percents: cfg.commission_percents,
        wallet_input_title: meta.wallet_input_title,
        wallet_input_placeholder: meta.wallet_input_placeholder,
    };
    if (cfg.variants && cfg.variants.length) {
        item.variants = cfg.variants;
    }
    return item;
}

export const usePaymentsStore = defineStore("payments", {
    state: (): State => ({
        paymentsModalOpen: false,
        paymentsTab: "payment",
        paymentsProviders: [],
        withdrawsProviders: [],
        providers: [],
        methods: {},
    }),
    getters: {
        availablePaymentMethods: (s: State): PaymentMethodView[] => {
            const result: PaymentMethodView[] = [];
            s.providers.forEach((provider) => {
                provider.payment?.forEach((payment) => {
                    const item = buildPaymentMethodItem(
                        s.methods[payment.method],
                        payment,
                        provider.provider
                    );
                    if (item) result.push(item);
                });
            });
            return result;
        },
        availableWithdrawMethods: (s: State): WithdrawMethodView[] => {
            const result: WithdrawMethodView[] = [];
            s.providers.forEach((provider) => {
                provider.withdraw?.forEach((withdraw) => {
                    const item = buildWithdrawMethodItem(
                        s.methods[withdraw.method],
                        withdraw,
                        provider.provider
                    );
                    if (item) result.push(item);
                });
            });
            return result;
        },
    },
    actions: {
        openPaymentsModal() {
            this.paymentsModalOpen = true
            const userStore = useUserStore()
            const userId = userStore.user?.id ?? null;

            this.fetchPaymentMethods({userId})
        },
        closePaymentsModal() {
            this.paymentsModalOpen = false;
        },
        setPaymentTab(tab: "payment" | "withdraw") {
            this.paymentsTab = tab;
        },
        setProviders(providers: ProviderDto[]): void {
            this.providers = Array.isArray(providers) ? providers : [];
        },
        setMethods(methods: Record<string, MethodMeta>): void {
            this.methods = methods ?? {};
        },
        async fetchPaymentMethods(
            opts: { force?: boolean; userId?: string | null } = {}
        ): Promise<void> {
            const { force = false, userId = null } = opts;
            if (this.providers.length && !force) return;
            if (!userId) return;

            try {
                const response = await ApiClient.get(
                    `/api/v1/user/payment-providers`,
                    {}
                );
                const data = response?.data ?? {};

                const methods: Record<string, MethodMeta> =
                    data && typeof data.methods === "object" ? data.methods : {};
                const providers: ProviderDto[] = Array.isArray(data.providers)
                    ? data.providers
                    : [];

                this.setMethods(methods);
                this.setProviders(providers);
            } catch {
                // Опционально: обработка ошибок/логирование
            }
        },
    },
});
