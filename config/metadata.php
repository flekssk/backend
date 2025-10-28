<?php

declare(strict_types=1);

return [
    'entities' => [
        \App\Services\GameSessions\Models\GameSession::class => [
            'table' => 'game_session_metadata',
            'primary_key' => 'id',
            'entity_table' => 'game_sessions',
            'entity_primary_key' => 'game_session_id',
            'metadata_key_field_name' => 'metadata_key',
            'metadata_value_field_name' => 'metadata_value',
            'base_model' => \App\Services\GameSessions\Models\GameSession::class,
            'model' => \App\Services\GameSessions\Models\GameSessionMetadata::class,
            'mutators' => [],
            'only_metadata_keys' => true,
        ],
    ]
];
