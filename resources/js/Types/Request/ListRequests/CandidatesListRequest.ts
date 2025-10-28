import {Field, ListRequestConfig} from "@/Types/Request/Requests";
import {
    BooleanCondition,
    ContainsCondition,
    FiltersCollection, NumericCondition, SearchCondition,
    SearchConditions,
    SortsCollection
} from "@/Components/Filters/Types/Filters";

export function useCandidatesList() {
    const paymentSystems = [
        {
            name: 'fk',
            value: 'fk'
        },
        {
            name: 'cryptobot',
            value: 'cryptobot'
        },
        {
            name: 'onepayment',
            value: 'onepayment'
        },
    ]

    return new ListRequestConfig(
        '/api/v1/candidates/list',
        new SearchConditions(
            [
                "id",
                "balance",
                "username",
                "wager",
                "slots_wager",
                "wager_status",
                "slots",
                "last_payment_at",
                "withdraws_count",
                "auto_withdraw",
                "last_session_date",
                "only_internal",
                "ban",
            ],
            new FiltersCollection([
                new ContainsCondition('withdraws_with_provider', [], [], paymentSystems),
                new SearchCondition('username'),
                new BooleanCondition('is_auto_withdraw_available', false),
                new NumericCondition('balance'),
            ]),
            new SortsCollection({
                slots: 'asc'
            })
        ),
        [
            new Field('id', 'number', true),
            new Field('username', 'string', false, 'Name'),
            new Field('balance', 'money', false, 'Balance'),
            new Field('slots', 'money', false, 'Slots'),
            new Field('wager', 'money', false, 'Wager'),
            new Field('only_internal', 'bool', false, 'locked'),
            new Field('last_session_date', 'date',false, 'last session'),
            new Field('auto_withdraw', 'bool', false, 'AW block'),
            new Field('ban', 'bool', false, 'Is Banned'),
        ]
    );
}
