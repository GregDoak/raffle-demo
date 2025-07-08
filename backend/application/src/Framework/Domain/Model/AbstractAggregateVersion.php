<?php

declare(strict_types=1);

namespace App\Framework\Domain\Model;

use DomainException;

abstract readonly class AbstractAggregateVersion implements AggregateVersionInterface
{
    final private function __construct(
        private int $version,
    ) {
        if ($this->version < 0) {
            throw new DomainException('Aggregate version must be larger than 0');
        }
    }

    public static function fromInt(int $version): static
    {
        return new static($version);
    }

    public static function fromNew(): static
    {
        return new static(0);
    }

    public function next(): static
    {
        return new static($this->version + 1);
    }

    public function toInt(): int
    {
        return $this->version;
    }
}
