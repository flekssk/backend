import {
    ContainsCondition,
    FiltersCollection,
    SearchConditions,
    SortsCollection
} from "@/Components/Filters/Types/Filters";
import {Field, ListRequestConfig} from "@/Types/Request/Requests";
import {statusColors} from "@/Types/Request/Colors";

export const statusFilterVariants =
    [
        {
            name: 'pending',
            value: 0
        },
        {
            name: 'success',
            value: 1
        },
        {
            name: 'failed',
            value: 2
        },
    ]


export function useWithdrawsList(userId: number | null = null) {
    const userContains = [];
    if (userId) {
        userContains.push(userId);
    }

    return new ListRequestConfig(
        '/api/v1/stimule/withdraws',
        new SearchConditions(
            ['id', 'amount', 'status', 'status_name', 'system', 'reason', 'wallet', 'created_at'],
            new FiltersCollection([
                new ContainsCondition('user_id', userContains),
                new ContainsCondition('status', [], [], statusFilterVariants),
            ]),
            new SortsCollection({
                created_at: 'desc'
            })
        ),
        [
            new Field('id', 'number', true),
            new Field('amount', 'money', false,  'Amount'),
            new Field('status_name', 'string', false, 'Status', statusColors, 'status'),
            new Field('system', 'string'),
            new Field('wallet', 'string'),
            new Field('reason', 'string'),
            new Field('created_at', 'date'),
        ]
    );
}
