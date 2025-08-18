<?php

declare(strict_types=1);

namespace App\Foundation\Clock;

use DateTimeInterface;

final readonly class Clock implements ClockInterface
{
    public static function now(): DateTimeInterface
    {
        return ClockProvider::get()::now();
    }

    public static function fromString(string $value): DateTimeInterface
    {
        return ClockProvider::get()::fromString($value);
    }

    public static function fromNullableString(?string $value): ?DateTimeInterface
    {
        if ($value === null) {
            return null;
        }

        return ClockProvider::get()::fromString($value);
    }

    public static function fromTimestamp(int $value): DateTimeInterface
    {
        return ClockProvider::get()::fromTimestamp($value);
    }
}
