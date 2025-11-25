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

    public static function make(string|UuidInterface|null|Id $id = null): Id
    {
        if ($id instanceof Id) {
            return $id;
        }

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

    public function uuid(): string
    {
        return is_string($this->id) ? $this->id : $this->id->toString();
    }
}
