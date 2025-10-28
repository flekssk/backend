import {defineStore} from "pinia";

export const useSlotsStore = defineStore("user", {
    state: (): State => ({
        paymentsModalOpen: false,
        paymentsTab: 'payment',
        paymentsProviders: [],
        withdrawsProviders: [],
    }),
    actions: {
        openPaymentsModal() {
            this.paymentsModalOpen = true
        },
        closePaymentsModal() {
            this.paymentsModalOpen = false
        },
    },
})
