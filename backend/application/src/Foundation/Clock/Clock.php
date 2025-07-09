<?php

declare(strict_types=1);

namespace App\Foundation\Clock;

use DateTimeInterface;

final readonly class Clock
{
    public static function fromString(string $value): DateTimeInterface
    {
        return ClockProvider::get()->fromString($value);
    }

    public static function now(): DateTimeInterface
    {
        return ClockProvider::get()->now();
    }
}
