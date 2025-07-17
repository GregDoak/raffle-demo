<?php

declare(strict_types=1);

namespace App\Framework\Domain\ValueObject;

/** @phpstan-consistent-constructor */
abstract readonly class AbstractInt
{
    protected function __construct(
        private int $value,
    ) {
    }

    public static function fromInt(int $value): static
    {
        return new static($value);
    }

    public function toInt(): int
    {
        return $this->value;
    }
}
