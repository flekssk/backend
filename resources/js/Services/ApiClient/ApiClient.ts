import {AxiosInstance, AxiosResponse} from "axios";
import {SearchConditions} from "../../Components/Filters/Types/Filters";

type WithBody = "post" | "put" | "patch"
type NoBody  = "get"  | "delete" | "head" | "options"

import axios from "axios"
import {authToken} from "../../Stores/user";

export const http = axios.create({
    headers: {
        Accept: "application/json",
    },
})

http.interceptors.request.use((config: any) => {
    try {
        const token = authToken();
        console.log(token)
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

    static post(route: string, data: any): Promise<AxiosResponse<any, any>> {
        return this.sendRequest('post' , route, data)
    }

    static get(route: string, data: any): Promise<AxiosResponse<any, any>> {
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
