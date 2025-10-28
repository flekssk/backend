<?php

declare(strict_types=1);

namespace App\Services\Slots\Api\Mobule\Entities;

class Slot
{
    public function __construct(
        public int $id,
        public string $name,
        public string $provider,
        public string $title,
        public string $alias,
        public bool $isEnabled,
        public bool $isFreeroundsEnabled,
        public bool $desktopEnabled,
        public bool $mobileEnabled,
    ) {
    }
}
