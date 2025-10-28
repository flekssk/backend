import {SearchConditions} from "@/Components/Filters/Types/Filters";
import {formatDate, formatMoney} from "@/Utils/StringUtils";
import {Colored} from "@/Utils/Colored";

export type FieldType = 'string' | 'number' | 'date' | 'datetime' | 'money' | 'bool'

export class Field {
    name: string
    title: string
    type: FieldType
    copiable: boolean
    colors: Colored[] | undefined = undefined
    valueField: string | undefined = undefined

    constructor(
        name: string,
        type: FieldType,
        copiable: boolean = false,
        title: string = name,
        colors: Colored[] | undefined = undefined,
        valueField: string | undefined = undefined,
    ) {
        this.name = name
        this.type = type
        this.title = title
        this.copiable = copiable
        this.colors = colors
        this.valueField = valueField
    }
}

export class ListRequestConfig {
    public route: string
    public filters: SearchConditions
    public fields: Field[] = []

    constructor(
        route: string,
        filters: SearchConditions,
        fields: Field[] = []
    ) {
        this.route = route;
        this.filters = filters;
        this.fields = fields;
    }
}

export function formatField(column: Field, val: string | number | null | undefined): any {
    if (val === null || val === undefined) {
        return '-'
    }

    if (column.type === 'date') {
        return formatDate(val.toString())
    }
    if (column.type === 'money') {
        return formatMoney(Number(val))
    }
    if (column.type === 'bool') {
        return val ? 'Yes' : 'No'
    }
    return val
}
