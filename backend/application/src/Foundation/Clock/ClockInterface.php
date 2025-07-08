<?php

declare(strict_types=1);

namespace Alleava\Foundation\Clock;

use DateTimeInterface;

interface ClockInterface
{
    public function now(): DateTimeInterface;

    public function fromString(string $value): DateTimeInterface;
}
