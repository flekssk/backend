export interface Condition {
    field: string
    label: string | undefined
    value(): object | string | boolean | null
    setValue(value: object | string | boolean | null): void
}

export class FiltersCollection {
    public conditions: Condition[]

    constructor(conditions: Condition[] = []) {
        this.conditions = conditions
    }

    public addCondition(condition: Condition): this {
        this.conditions.push(condition)

        return this
    }

    public getFilter(field: string): Condition | ContainsCondition | null {
        return this.conditions.find(condition => condition.field === field) || null
    }

    clone() {
        let filters: Condition[] = []

        this.conditions.forEach(condition => {
            if (condition instanceof ContainsCondition) {
                condition = condition.clone()
            }

            filters.push(condition)
        })

        return new FiltersCollection(filters)
    }

    static make(conditions: Condition[] = []): FiltersCollection {
        return new FiltersCollection(conditions)
    }
}

export type SortDirection = 'asc' | 'desc';
export type SortsRecord = Record<string, SortDirection>;

export class SortsCollection {
    public sorts: SortsRecord;

    constructor(sorts: SortsRecord = {}) {
        this.sorts = {...sorts};
    }

    addSort(field: string, direction: SortDirection): this {
        this.sorts[field] = direction;
        return this;
    }
}


export class ContainsCondition implements Condition {
    public field: string
    public contains: number[] | string[]
    public notContains: number[] | string[]
    public availableVariants: { name: string | number }[]
    public label: string | undefined;

    constructor(
        field: string,
        contains: number[] | string[] = [],
        notContains: number[] | string[] = [],
        availableVariants: { name: string | number }[] = [],
        label: string | undefined = undefined,
    ) {
        this.field = field
        this.contains = contains
        this.notContains = notContains
        this.availableVariants = availableVariants
        this.label = label
    }

    setContains(value: string[] | number[]): this {
        this.contains = value

        return this
    }

    setNotContains(value: []): this {
        this.notContains = value

        return this
    }

    value(): object | string | null {
        if (this.contains.length > 0) {
            return {
                contains: this.contains
            }
        }

        if (this.notContains.length > 0) {
            return {
                notContains: this.notContains
            }
        }

        return null
    }

    clone(): ContainsCondition {
        return new ContainsCondition(
            this.field,
            this.contains,
            this.notContains,
            this.availableVariants,
            this.label,
        )
    }

    setValue(value: { contains: string [] | number[], notContains: string [] | number[]  }): void {
        this.contains = value.contains
        this.notContains = value.notContains
    }
}

export class SearchCondition implements Condition {
    public field: string
    public search: string | null
    public label: string | undefined;

    constructor(field: string, value: string | null = null, label: string | undefined = undefined) {
        this.field = field
        this.search = value
        this.label = label
    }

    value() {
        return this.search
    }

    setValue(value: string): void {
        this.search = value
    }
}

export class BooleanCondition implements Condition {
    public field: string
    public bool: boolean | null
    public label: string | undefined

    constructor(field: string, value: boolean | null = null, label: string | undefined = undefined) {
        this.field = field
        this.bool = value
        this.label = label
    }

    value(): object | string | boolean | null {
        return this.bool
    }

    setValue(value: boolean): void {
        this.bool = value
    }
}

export class NumericCondition implements Condition {
    public field: string
    public equals: number | null
    public notEquals: number | null
    public lowerOrEquals: number | null
    public lowerThat: number | null
    public greaterThat: number | null
    public greaterOrEquals: number | null
    public label: string | undefined

    constructor(
        field: string,
        equals: number | null = null,
        notEquals: number | null = null,
        lowerOrEquals: number | null = null,
        lowerThat: number | null = null,
        greaterThat: number | null = null,
        greaterOrEquals: number | null = null,
        label: string | undefined = undefined,
    ) {
        this.field = field
        this.equals = equals
        this.notEquals = notEquals
        this.lowerOrEquals = lowerOrEquals
        this.lowerThat = lowerThat
        this.greaterThat = greaterThat
        this.greaterOrEquals = greaterOrEquals
        this.label = label
    }

    value(): object | string | boolean | null {
        return {
            eq: this.equals,
            ne: this.notEquals,
            le: this.lowerOrEquals,
            lt: this.lowerThat,
            gt: this.greaterThat,
            ge: this.greaterOrEquals,
        }
    }

    setValue(value: {eq: number | null}): void {
        this.equals = value?.eq
    }
}

export type SearchConditionsObject = {
    available_fields: string[]
    filter: Record<string, object | string | boolean | null>,
    sort: Record<string, object | string | boolean | null>,
    page: number,
    per_page: number,
}

export class SearchConditions {
    public availableFields: string[]
    public filters: FiltersCollection
    public sorts: SortsCollection
    public page: number
    public perPage: number

    constructor(
        availableFields: string[],
        filters: FiltersCollection,
        sorts: SortsCollection,
        page: number = 1,
        perPage: number = 20,
    ) {
        this.availableFields = availableFields
        this.filters = filters
        this.sorts = sorts
        this.page = page
        this.perPage = perPage
    }

    toObject(): SearchConditionsObject {
        return {
            available_fields: this.availableFields,
            filter: Object.fromEntries(
                this.filters
                    .conditions
                    .filter(condition => {
                        if (condition instanceof ContainsCondition) {
                            return condition.contains.length > 0 || condition.notContains.length > 0
                        }

                        return condition.value !== null
                    })
                    .map(condition => [condition.field, condition.value()] as const),
            ),
            sort: this.sorts.sorts,
            page: this.page,
            per_page: this.perPage,
        }
    }

    nextPage(): SearchConditions {
        this.page++

        return this
    }

    static fromObject(object: SearchConditionsObject): SearchConditions {
        return SearchConditions.make(
            object.available_fields,
        )
    }

    static make(
        availableFields: string[],
        filters: FiltersCollection | null = null,
        sort: SortsCollection | null = null,
        page: number = 1,
        perPage: number = 20,
    ): SearchConditions {
        return new SearchConditions(
            availableFields,
            filters ?? new FiltersCollection(),
            sort ?? new SortsCollection(),
            page,
            perPage
        )
    }
}

export function cloneConditions(sc: SearchConditions): SearchConditions {
    return SearchConditions.make(
        sc.availableFields,
        sc.filters.clone(),
        sc.sorts,
        sc.page,
        sc.perPage,
    )
}
