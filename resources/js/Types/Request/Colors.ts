import {PaymentStatus} from "@/Types/Enums/Payments";
import {Colored} from "@/Utils/Colored";

export const statusColors = <Colored[]> [
    {
        value: PaymentStatus.PENDING.valueOf(),
        color: 'pending',
    },
    {
        value: PaymentStatus.SUCCESS.valueOf(),
        color: 'success',
    },
    {
        value: PaymentStatus.FAILED.valueOf(),
        color: 'failed',
    },
]
