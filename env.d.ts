declare module '*.svg' {
    const src: string;
    export default src;
}
declare module '*.png' {
    const src: string;
    export default src;
}
declare module '*.jpg' {
    const src: string;
    export default src;
}
interface ImportMetaEnv {
    readonly VITE_API_BASE_URL: string;
}
interface ImportMeta {
    readonly env: ImportMetaEnv;
}
