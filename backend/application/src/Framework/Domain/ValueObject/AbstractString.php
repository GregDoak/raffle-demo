<?php

declare(strict_types=1);

namespace App\Framework\Domain\ValueObject;

/** @phpstan-consistent-constructor */
abstract readonly class AbstractString
{
    protected function __construct(
        private string $value,
    ) {
    }

    public static function fromString(string $value): static
    {
        return new static($value);
    }

    public function toString(): string
    {
        return $this->value;
    }
}
