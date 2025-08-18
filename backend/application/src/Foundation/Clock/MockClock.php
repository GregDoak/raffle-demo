<?php

declare(strict_types=1);

namespace App\Foundation\Clock;

use DateTimeImmutable;
use DateTimeInterface;

final class MockClock implements ClockInterface
{
    private static DateTimeImmutable $now;

    public function __construct(string $now = 'now')
    {
        self::$now = new DateTimeImmutable($now);
    }

    public static function now(): DateTimeInterface
    {
        return self::$now;
    }

    public static function fromString(string $value): DateTimeInterface
    {
        return new DateTimeImmutable($value);
    }

    public static function fromNullableString(?string $value): ?DateTimeInterface
    {
        if ($value === null) {
            return null;
        }

        return new DateTimeImmutable($value);
    }

    public static function fromTimestamp(int $value): DateTimeInterface
    {
        return DateTimeImmutable::createFromTimestamp($value);
    }
}
