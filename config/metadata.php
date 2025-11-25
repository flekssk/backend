<?php

declare(strict_types=1);

use App\Payments\Models\Payment;
use App\Payments\Models\PaymentMetadata;

return [
    'entities' => [
        Payment::class => [
            'table' => 'payment_metadata',
            'primary_key' => 'id',
            'entity_table' => 'payments',
            'entity_primary_key' => 'payment_id',
            'metadata_key_field_name' => 'metadata_key',
            'metadata_value_field_name' => 'metadata_value',
            'base_model' => Payment::class,
            'model' => PaymentMetadata::class,
            'mutators' => [],
            'only_metadata_keys' => true,
        ],
    ]
];
