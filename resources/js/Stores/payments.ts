import {defineStore} from "pinia";
import {ApiClient} from "../Services/ApiClient/ApiClient";
import {useUserStore} from "./user";
import {ElMessage} from "element-plus";

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
    activePayment: Payment | null;
};

interface PaymentMetadata {
    metadata_value: string
    metadata_key: string
}

class PaymentStatus {
    public readonly status: 0 | 1 | 2 | 3 | 4 | 5 | 6;

    constructor(status: 0 | 1 | 2 | 3 | 4 | 5 | 6) {
        this.status = status;
    }

    name(): string {
        const statuses = {
            0: 'Ожидание',
            1: 'Зачислено на счет',
            2: 'Неудача',
            3: 'Просрочено',
            4: 'Оплачен',
            5: 'Отменен',
            6: 'Ожидает подтверждения',
        };

        return statuses[this.status]
    }
}

type InvoiceType = "show_sbp" | "redirect"

export class SBPInvoiceData {
    public readonly amount: number
    public readonly bank: string
    public readonly name: string
    public readonly account: string

    constructor(
        amount: number,
        bank: string,
        name: string,
        account: string,
    ) {
        this.amount = amount;
        this.bank = bank;
        this.name = name;
        this.account = account;
    }
}

export class RedirectInvoiceData {
    public readonly url: string

    constructor(
        url: string,
    ) {
        this.url = url;
    }
}

export class Payment {
    public readonly id: string
    public readonly payment_provider: string
    public readonly payment_provider_method: string
    public readonly amount: number
    public readonly status: PaymentStatus
    public readonly metadata: PaymentMetadata[]
    public readonly invoice: SBPInvoiceData | RedirectInvoiceData | null = null
    public readonly createdAt: Date | null
    public readonly remainingSecondsToPay: number | null

    constructor(
        id: string,
        payment_provider: string,
        payment_provider_method: string,
        amount: number,
        status: 0 | 1 | 2 | 3,
        metadata: PaymentMetadata[],
        createdAt: Date = null,
        remainingSecondsToPay: number = null,
    ) {
        this.id = id
        this.payment_provider = payment_provider
        this.payment_provider_method = payment_provider_method
        this.status = new PaymentStatus(status)
        this.amount = amount
        this.metadata = metadata
        this.invoice = this.buildInvoice()
        this.createdAt = createdAt
        this.remainingSecondsToPay = remainingSecondsToPay
    }

    buildInvoice(): SBPInvoiceData | RedirectInvoiceData | null {
        if (this.payment_provider_method === 'sbp') {
            const amount = this.metadata.find(m => m.metadata_key === 'amount')?.metadata_value
            const bank = this.metadata.find(m => m.metadata_key === 'bank')?.metadata_value
            const name = this.metadata.find(m => m.metadata_key === 'name')?.metadata_value
            const account = this.metadata.find(m => m.metadata_key === 'phone')?.metadata_value

            if (
                !Number.isInteger(amount)
                || bank === undefined
                || name === undefined
                || account === undefined
            ) {
                throw new Error('Invalid SBP invoice data')
            }

            return new SBPInvoiceData(Number.parseInt(amount), bank, name, account)
        } else {
            const url = this.metadata?.find(m => m.metadata_key === 'url')?.metadata_value

            if (url === undefined) {
                return null;
            }

            return new RedirectInvoiceData(url)
        }
    }
}

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

export function buildPayment(data: any): Payment {
    return new Payment(
        data.id,
        data.payment_provider,
        data.payment_provider_method,
        data.amount,
        data.status,
        data.metadata,
        new Date(data.created_at),
        data.remaining_seconds_to_pay
    )
}

export const usePaymentsStore = defineStore("payments", {
    state: (): State => ({
        paymentsModalOpen: false,
        paymentsTab: "payment",
        paymentsProviders: [],
        withdrawsProviders: [],
        providers: [],
        methods: {},
        activePayment: null
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
            this.fetchActivePayments()
        },
        closePaymentsModal() {
            this.paymentsModalOpen = false;
        },
        setPaymentTab(tab: "payment" | "withdraw") {
            this.paymentsTab = tab;
        },
        setProviders(providers: ProviderDto[]): void {
            this.providers = providers ?? [];
        },
        setMethods(methods: Record<string, MethodMeta>): void {
            this.methods = methods ?? [];
        },
        async createPayment({amount, paymentProvider, paymentProviderMethod}: {
            amount: number;
            paymentProvider: string;
            paymentProviderMethod: string;
        }): Promise<any> {
            const payment = await ApiClient.post('/api/v1/payments', {
                amount,
                payment_provider: paymentProvider,
                payment_method: paymentProviderMethod,
            })

            if (Array.isArray(payment?.data.errors)) {
                payment?.data.errors.forEach((error: string) => {
                    ElMessage.error(error)
                })

                return
            }

            this.activePayment = buildPayment(payment.data)
        },
        async createWithdraw({amount, wallet, paymentProvider, paymentProviderMethod, paymentProviderVariant}: {
            amount: number;
            wallet: string;
            paymentProvider: string;
            paymentProviderMethod: string;
            paymentProviderVariant: string | null;
        }): Promise<any> {
            console.log(
                amount
            )
            const payment = await ApiClient.post('/api/v1/withdraw', {
                amount,
                wallet,
                payment_provider: paymentProvider,
                payment_method: paymentProviderMethod,
                payment_variant: paymentProviderVariant,
            })
                .then(response => {
                    if (response.status === 200) {
                        ElMessage.success('Выплата прошла успешно')
                        useUserStore().fetchUser()
                    }
                })
        },
        async fetchActivePayments(s: State): Promise<Payment> {
            try {
                let response = await ApiClient.post('/api/v1/payments/active', {})

                this.activePayment = buildPayment(response.data)

                return this.activePayment
            } catch (e) {
                this.activePayment = null

                return null
            }
        },
        fetchPaymentMethods(
            opts: { force?: boolean; } = {}
        ): Promise<void> {
            const {force = false} = opts;
            if (this.providers.length && !force) return;

            try {
                ApiClient.get(
                    `/api/v1/user/payment-providers`,
                    {}
                ).then((response: any) => {
                    const data = response?.data ?? {};

                    this.setMethods(data.methods);
                    this.setProviders(Object.values(data.providers ?? {}));
                })
            } catch {
                // Опционально: обработка ошибок/логирование
            }
        },

        async markPayed(id: string) {
            const response = await ApiClient.post(`/api/v1/user/payments/${id}/payed`)

            if (response.status === 200) {
                ElMessage.success('Платеж в обработке')
            }
        },

        async cancelPayment(id: string) {
            const response = await ApiClient.post(`/api/v1/user/payments/${id}/cancel`)

            if (response.status === 200) {
                ElMessage.success('Платеж отменен')

                this.activePayment = null
            }
        },

        async awaitPayment(id: string) {
            const response = await ApiClient.post(`/api/v1/user/payments/${id}/await`)

            if (response.status === 200) {
                ElMessage.success('Платеж в ожидание')

                this.activePayment = null
            }
        }
    },
});
