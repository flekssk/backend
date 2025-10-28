export enum Provider {
    FK = 'fk',
    Cryptobot = 'cryptobot',
    Onepayment = 'onepayment',
}

export const ProvidersOptions = (Object.values(Provider) as Provider[]).map((p) => ({
    name: p,
    value: p,
})) as readonly { name: string; value: Provider }[];

