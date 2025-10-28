import {statusFilterVariants} from "./PaymentsRequests";
import {ListRequestConfig} from "../Requests";
import {SearchConditions} from "../../../Components/filters/Types/Filters";

export function useSlotsList(userId: number | null = null, perPage = 100, types: slotsType[] = []) {
    const userContains = [];

    if (userId) {
        userContains.push(userId);
    }

    return new ListRequestConfig(
        '/api/v1/slots/list',
        new SearchConditions(
            ['id', 'title', 'provider'],
            new FiltersCollection([
                new ContainsCondition('user_id', userContains),
                new ContainsCondition('status', [], [], statusFilterVariants),
            ]),
            new SortsCollection({
                created_at: 'desc'
            }),
            1,
            perPage
        ),
        []
    );
}
