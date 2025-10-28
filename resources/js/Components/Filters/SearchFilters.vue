<template style="font-size: 12px!important;">
    <template v-for="filter in localConditions.filters.conditions">
        <Select v-if="isSelectFilter(filter)"
                class="rounded-lg"
                :options="filter.availableVariants"
                optionLabel="name"
                optionValue="value"
                :placeholder="filter.field"
                size="small"
                showClear
                @update:model-value="(val: string) => setFilterValue(filter.field, val)"
        />
        <FloatLabel size="small" v-if="isSearchFilter(filter)" class="w[200px]">
            <InputText
                id="username"
                size="small"
                class="w-full rounded-lg"
                :model-value="getValue(filter.field)"
                @update:model-value="(val: string) => setFilterValue(filter.field, val)"
            />
            <label for="username">{{ filter.field }}</label>
        </FloatLabel>
        <div class="items-center gap-2" v-if="isBoolFilter(filter)">
            <Checkbox
                class="rounded-sm w-1"
                :model-value="getValue(filter.field)"
                :inputId="filter.field"
                name="AutoWithdrawAvailable"
                size="small"
                :pt="{ box: { class: 'rounded-md' } }"
                @update:model-value="(val: string) => setFilterValue(filter.field, val)"
                binary
            />
            <label class="text-sm" :for="filter.field">
                {{ filter.label ?? filter.field }}
            </label>
        </div>
    </template>
    <div>
        <Button
            size="small"
            class="rounded-sm"
            icon="pi pi-refresh"
            :outlined="true"
            @click="refresh"
            severity="secondary"
        />
    </div>
</template>

<script lang="ts">
import {
    BooleanCondition, cloneConditions,
    Condition,
    ContainsCondition,
    SearchCondition,
    SearchConditions
} from "@/Components/Filters/Types/Filters";
import Select from 'primevue/select'
import FloatLabel from 'primevue/floatlabel';
import InputText from 'primevue/inputtext';
import Checkbox from 'primevue/checkbox';
import Button from 'primevue/button';
import IftaLabel from 'primevue/iftalabel';
import {defineComponent, PropType } from "vue";

export default defineComponent({
        name: "SearchFilters",
        components: {
            Select,
            FloatLabel,
            InputText,
            Checkbox,
            IftaLabel,
            Button
        },
        props: {
            conditions: {
                type: Object as PropType<SearchConditions>,
                required: true,
            }
        },
        methods: {
            isSelectFilter(filter: Condition) {
                return filter instanceof ContainsCondition
                    && filter.availableVariants.length > 0;
            },
            isSearchFilter(filter: Condition) {
                return filter instanceof SearchCondition
            },
            isBoolFilter(filter: Condition) {
                return filter instanceof BooleanCondition
            },
            getValue(field: string) {
                return this.conditions.filters.getFilter(field)?.value()
            },
            setFilterValue(field: string, val: any) {
                if (this._debounceTimer) clearTimeout(this._debounceTimer)

                const filter = this.localConditions.filters.getFilter(field)
                if (!filter) return

                if (filter instanceof ContainsCondition) {
                    filter.setValue({ contains: val !== null ? [val] : [], notContains: [] })
                } else {
                    filter.setValue(val)
                }

                this._debounceTimer = setTimeout(() => {
                    this.$emit('update:conditions', cloneConditions(this.localConditions))
                }, 700)
            },
            refresh() {
                if (this._debounceTimer) {
                    clearTimeout(this._debounceTimer)
                }

                this.$emit('update:conditions', cloneConditions(this.localConditions))
            },
        },
        emits: ['update:conditions', 'refresh'],
        watch: {
            conditions: {
                deep: true,
                handler(val: SearchConditions) {
                    this.localConditions = cloneConditions(val);
                }
            }
        },
        data(): any {
            return {
                localConditions: cloneConditions(this.conditions),
                _debounceTimer: null as ReturnType<typeof setTimeout> | null,
            }
        }
    }
)
</script>

<style scoped>
:deep(.p-inputtext),
:deep(.p-select),
:deep(.p-select-trigger),
:deep(.p-multiselect),
:deep(.p-calendar),
:deep(.p-inputnumber),
:deep(.p-password) {
    font-size: 13px !important;
    border-radius: 0.5rem !important;
}
:deep(.p-select-dropdown),
:deep(.p-floatlabel label) {
    font-size: 13px !important;
}

/* Чтобы скругление Select было визуально заметно */
:deep(.p-select) {
    overflow: hidden; /* важно для «срезания» углов у внутренних элементов */
}

:deep(.p-checkbox .p-checkbox-box) {
    border-radius: 0.375rem;
}

:deep(.p-button),
:deep(.p-button.p-button-icon-only) {
    border-radius: 0.5rem;
}

</style>
