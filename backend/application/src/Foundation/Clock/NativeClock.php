<?php

declare(strict_types=1);

namespace App\Foundation\Clock;

use DateTimeImmutable;
use DateTimeInterface;

final readonly class NativeClock implements ClockInterface
{
    public function now(): DateTimeInterface
    {
        return new DateTimeImmutable();
    }

    public function fromString(string $value): DateTimeInterface
    {
        return new DateTimeImmutable($value);
    }
}
