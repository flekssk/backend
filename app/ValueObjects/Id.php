<?php

declare(strict_types=1);

namespace App\ValueObjects;

use Illuminate\Support\Str;
use Ramsey\Uuid\UuidInterface;

readonly class Id implements \Stringable
{
    public function __construct(private string|UuidInterface $id)
    {
    }

    public static function make(string|UuidInterface|null $id = null): Id
    {
        if ($id === null) {
            $id = Str::uuid();
        }

        return new self($id);
    }

    public static function makeNullable(string|UuidInterface|null $id = null): ?Id
    {
        return $id === null ? null : new self($id);
    }

    public function __toString()
    {
        return (string) $this->id;
    }
}
