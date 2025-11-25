import {defineStore} from "pinia";
import {ApiClient} from "../Services/ApiClient/ApiClient";
import axios from "axios";
import {router} from "@inertiajs/vue3";
import {route} from "ziggy-js";
import { usePage } from '@inertiajs/vue3';

export class Slot {
    public readonly id: number
    public readonly title: string
    public readonly provider: string
    public readonly image: string
    public readonly alias: string
    public readonly url: string

    constructor(
        id: number,
        title: string,
        provider: string,
        image: string,
        alias: string,
        url: string,
    ) {
        this.id = id
        this.title = title
        this.provider = provider
        this.image = image
        this.alias = alias
        this.url = url
    }
}

class GameWin {
    public readonly gameTitle: string
    public readonly gameImage: string
    public readonly userName: string
    public readonly profit: number

    constructor(
        game_title: string,
        game_image: string,
        user_name: string,
        profit: number,
    ) {
        this.gameTitle = game_title
        this.gameImage = game_image
        this.userName = user_name
        this.profit = profit
    }
}

class Provider {
    public readonly id: number
    public readonly title: string
    public readonly image: string
    public readonly icon: string
    public readonly count: number

    constructor(
        id: number,
        title: string,
        image: string,
        icon: string,
        count: number,
    ) {
        this.id = id
        this.title = title
        this.image = image
        this.icon = icon
        this.count = count
    }
}

interface State {
    lastWins: GameWin[]
    providers: Provider[]
    selectedProvider: Provider | null
}

const page = usePage<{
    provider?: string | null;
}>();

export const useSlotsStore = defineStore("slots", {
    state: (): State => ({
        lastWins: [],
        providers: [],
        selectedProvider: null,
    }),
    actions: {
        async loadSlot(slotId: number): Promise<Slot> {
            const mobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)
            let response = await ApiClient.get(`/api/v1/slots/${slotId}`, {mobile});

            if (response.status === 200) {
                return new Slot(
                    response.data.slot.id,
                    response.data.slot.title,
                    response.data.slot.provider,
                    response.data.slot.image,
                    response.data.slot.alias,
                    response.data.start_url,
                )
            }
            throw new Error('Cant load slot: ' + response.status + ' ' + response.statusText + ' ' + response.data.message + ' ' + response.data.errors.join(''))
        },
        async fetchProviders() {
            if (this.providers.length) return;

            try {
                const response = await axios.post(`/api/v1/slots/providers/list`);

                if (response.data && Array.isArray(response.data)) {
                    this.providers = await Promise.all(
                        response.data.map(async (provider) => {
                            return new Provider(
                                provider.id,
                                provider.name,
                                provider.image,
                                provider.icon,
                                0
                            );
                        })
                    )

                    if (page.props.provider !== undefined) {
                        this.selectedProvider = this.providers.find((provider: Provider) => provider.id === Number(page.props.provider))
                    }
                } else {
                    console.error("Неверный формат данных провайдеров");
                }
            } catch (error) {
                console.error("Ошибка при получении данных провайдеров:", error);
            }
        },
        async lastWinners(): Promise<Slot[]> {
            let response = await ApiClient.get(`/api/v1/slots/last-win-logs`)

            return response.data.map((slot: any) => new Slot(
                slot.id,
                slot.title,
                slot.provider,
                slot.image,
                slot.alias,
                slot.start_url
            ))
        },
        initSocket(): void {
            if (this.echoInited) return;
            if (window.Echo) {
                console.warn('Echo не инициализирован. Убедитесь, что он создаётся в bootstrap.');
                return;
            }

            this.echoInited = true;
            console.log(this.echoInited)

            window
                .Echo
                .listen('slots', 'App\\Services\\Games\\Events\\SlotWinEvent', (e: any) => {
                    const win = new GameWin(
                        e.game_title ?? e.game?.title ?? e.title ?? '',
                        e.game_image ?? e.game?.image ?? e.image ?? '',
                        e.user_name ?? e.user?.name ?? '',
                        Number(e.profit ?? e.amount ?? 0),
                    );

                    // Добавляем сверху и ограничиваем длину
                    this.lastWins.unshift(win);
                    if (this.lastWins.length > 50) {
                        this.lastWins.length = 50;
                    }
                });
        },

        selectProvider(provider: Provider = null) {
            if (this.selectProvider?.id === provider.id) return

            this.selectedProvider = this.providers.find((existed: Provider) => existed.id === provider.id) ?? null;

            router.get(
                this.selectedProvider
                    ? route('slots-provider', {provider: this.selectedProvider.id})
                    : route('slots')
            )
        },

        disposeSocket(): void {
            if (!this.echoInited || !(window as any).Echo) return;
            try {
                (window as any).Echo.leave('slots');
            } catch (_) {
                // ignore
            }
            this.echoInited = false;
        }
    },
})
