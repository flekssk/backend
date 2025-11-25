<?php

declare(strict_types=1);

namespace App\Services\Games\Contracts;

interface GameInterface
{
    public function getName(): string;
    public function getImage(): string;
}
