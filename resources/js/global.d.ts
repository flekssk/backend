import Pusher from 'pusher-js';
// resources/js/types/global.d.ts
import Echo from 'laravel-echo';


declare global {
    interface Window {
        Pusher: typeof Pusher;
        Echo: Echo<"reverb">;
        onTelegramAuth: (user: Record<string, any>) => void;
    }
}

interface ImportMetaEnv {
    readonly VITE_REVERB_APP_KEY: string;
    readonly VITE_REVERB_HOST?: string;
    readonly VITE_REVERB_PORT?: string;
    readonly VITE_REVERB_SCHEME?: 'http' | 'https';
    readonly VITE_TELEGRAM_BOT_USERNAME?: string;
}

interface ImportMeta {
    readonly env: ImportMetaEnv;
}


export {};
