<?php

declare(strict_types=1);

namespace App\Framework\Domain\ValueObject;

use App\Foundation\Clock\Clock;
use DateTimeInterface;

/** @phpstan-consistent-constructor */
abstract readonly class AbstractDateTime
{
    protected function __construct(
        private DateTimeInterface $value,
    ) {
    }

    public static function fromNow(): static
    {
        return new static(Clock::now());
    }

    public static function fromString(string $value): static
    {
        return new static(Clock::fromString($value));
    }

    public function toDateTime(): DateTimeInterface
    {
        return $this->value;
    }

    public function toString(?string $format = null): string
    {
        return $this->value->format($format ?? DATE_ATOM);
    }
}
