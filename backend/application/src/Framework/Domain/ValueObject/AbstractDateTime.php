<?php

declare(strict_types=1);

namespace App\Framework\Domain\ValueObject;

use App\Foundation\Clock\Clock;
use App\Framework\Domain\Exception\InvalidDateTimeException;
use DateTimeInterface;
use Throwable;

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
        if ($value === '') {
            throw InvalidDateTimeException::fromEmptyDateTime();
        }

        try {
            return new static(Clock::fromString($value));
        } catch (Throwable) {
            throw InvalidDateTimeException::fromInvalidDateTime();
        }
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
