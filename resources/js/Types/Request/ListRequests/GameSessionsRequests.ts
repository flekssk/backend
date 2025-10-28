import {
    ContainsCondition,
    FiltersCollection,
    SearchConditions,
    SortsCollection
} from "@/Components/Filters/Types/Filters";
import {Field, ListRequestConfig} from "@/Types/Request/Requests";


export function useGameSessionsList(userId: number | null = null) {
    const userContains = [];
    if (userId) {
        userContains.push(userId);
    }

    return new ListRequestConfig(
        '/api/v1/sessions/list',
        new SearchConditions(
            ['id', 'status', 'status_name', 'user_id', 'user', 'metadata', 'created_at'],
            new FiltersCollection([
                new ContainsCondition('user_id', userContains),
            ]),
            new SortsCollection({
                created_at: 'desc'
            })
        ),
        [
            new Field('id', 'number', true),
            new Field('status_name', 'string'),
            new Field('user_id', 'number', true),
            new Field('created_at', 'date'),
        ]
    );
}
