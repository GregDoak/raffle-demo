<?php

declare(strict_types=1);

namespace App\Foundation\Clock;

use DateTimeInterface;

interface ClockInterface
{
    public static function now(): DateTimeInterface;

    public static function fromString(string $value): DateTimeInterface;

    public static function fromNullableString(?string $value): ?DateTimeInterface;

    public static function fromTimestamp(int $value): DateTimeInterface;
}
