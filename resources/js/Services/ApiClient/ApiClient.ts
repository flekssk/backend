import {AxiosInstance, AxiosResponse} from "axios";
import {SearchConditions} from "../../Components/Filters/Types/Filters";

type WithBody = "post" | "put" | "patch"
type NoBody  = "get"  | "delete" | "head" | "options"

import axios from "axios"
import {authToken} from "../../Stores/user";
import {ElMessage} from "element-plus";
export const http = axios.create({
    headers: {
        Accept: "application/json",
    },
    baseURL: 'https://socia.win'
})


http.interceptors.request.use((config: any) => {
    try {
        const token = authToken();

        if (token) {
            config.headers = config.headers ?? {};
            (config.headers as any).Authorization = `Bearer ${token}`;
        }
    } catch {
        // Если Pinia ещё не инициализирован — пропускаем без ошибки
    }
    return config;
});


export class ApiClient {
    static list(route: string, condition: SearchConditions): Promise<AxiosResponse<any, any>> {
        route = route + '?page=' + condition.page + '&per_page=' + condition.perPage;

        return this.post(route, condition.toObject())
    }

    static post(route: string, data: any = {}): Promise<AxiosResponse<any, any>> {
        return this.sendRequest('post' , route, data)
            .catch(e => {
                if (e.response?.status === 422) {
                    Object.entries(e.response.data.errors).forEach(([field, msgs]) => {
                        if (!Array.isArray(msgs)) {
                            return
                        }

                        msgs.forEach((msg: string) => {
                            ElMessage.error(msg)
                        })
                    })
                }
                if (e.response?.status === 500) {
                    ElMessage.error(e.response.data.message)
                }
            })
    }

    static get(route: string, data: any = {}): Promise<AxiosResponse<any, any>> {
        return this.sendRequest('get' , route, data)
    }

    static sendRequest(
        method: string,
        route: string,
        data: any,
        config?: Parameters<AxiosInstance["get"]>[1]
    ): Promise<AxiosResponse<any, any>> {
        if ((["get", "delete", "head", "options"] as const).includes(method as NoBody)) {
            // get/delete: (url, config)
            return http[method](route, { ...(config || {}), params: data })
        } else {
            // post/put/patch: (url, data, config)
            return http[method as WithBody](route, data, config)
        }

    }
}
