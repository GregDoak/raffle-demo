<?php

declare(strict_types=1);

namespace App\Foundation\Clock;

use DateTimeImmutable;
use DateTimeInterface;

final readonly class NativeClock implements ClockInterface
{
    public static function now(): DateTimeInterface
    {
        return new DateTimeImmutable();
    }

    public static function fromString(string $value): DateTimeInterface
    {
        return new DateTimeImmutable($value);
    }

    public static function fromTimestamp(int $value): DateTimeInterface
    {
        return DateTimeImmutable::createFromTimestamp($value);
    }
}
