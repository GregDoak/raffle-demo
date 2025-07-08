<?php

declare(strict_types=1);

namespace Alleava\Foundation\Clock;

use DateTimeImmutable;
use DateTimeInterface;

final readonly class MockClock implements ClockInterface
{
    private DateTimeImmutable $now;

    public function __construct(string $now = 'now')
    {
        $this->now = new DateTimeImmutable($now);
    }

    public function now(): DateTimeInterface
    {
        return $this->now;
    }

    public function fromString(string $value): DateTimeInterface
    {
        return new DateTimeImmutable($value);
    }
}
