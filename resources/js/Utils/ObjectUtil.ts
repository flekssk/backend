type KeyValue = { key: string; value: unknown };

function isPlainObject(val: unknown): val is Record<string, unknown> {
    return typeof val === 'object' && val !== null && !Array.isArray(val);
}

export function flattenToKeyValue(input: unknown): KeyValue[] {
    const out: KeyValue[] = [];

    function walk(value: unknown, path: string) {
        if (Array.isArray(value)) {
            value.forEach((item, i) => {
                const next = path ? `${path}[${i}]` : `[${i}]`;
                walk(item, next);
            });
        } else if (isPlainObject(value)) {
            Object.entries(value).forEach(([k, v]) => {
                const next = path ? `${path}.${k}` : k;
                walk(v, next);
            });
        } else {
            // Примитив или null/undefined — конечное значение
            if (path) out.push({ key: path, value });
        }
    }

    walk(input, '');

    return out;
}
