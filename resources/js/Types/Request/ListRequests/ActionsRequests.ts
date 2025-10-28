import {
    ContainsCondition,
    FiltersCollection, SearchCondition,
    SearchConditions,
    SortsCollection
} from "@/Components/Filters/Types/Filters";
import {Field, ListRequestConfig} from "@/Types/Request/Requests";


export function useActionsList(userId: number | null = null): ListRequestConfig {
    const userContains = [];
    if (userId) {
        userContains.push(userId);
    }

    return new ListRequestConfig(
        '/api/v1/stimule/actions',
        new SearchConditions(
            ['id', 'action', 'balanceBefore', 'balanceAfter', 'created_at'],
            new FiltersCollection([
                new ContainsCondition('user_id', userContains),
                new SearchCondition('action'),
            ]),
            new SortsCollection({
                created_at: 'desc'
            })
        ),
        [
            new Field('id', 'number', true),
            new Field('action', 'string'),
            new Field('balanceBefore', 'money'),
            new Field('balanceAfter', 'money'),
            new Field('created_at', 'date'),
        ]
    );
}
