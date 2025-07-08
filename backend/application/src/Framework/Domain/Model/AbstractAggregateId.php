<?php

declare(strict_types=1);

namespace App\Framework\Domain\Model;

use App\Foundation\Uuid\Uuid;
use DomainException;

use function sprintf;

abstract readonly class AbstractAggregateId implements AggregateIdInterface
{
    final private function __construct(
        private string $id,
    ) {
        if (Uuid::isValid($id) === false) {
            throw new DomainException(sprintf("%s '%s' is not valid", static::class, $id));
        }
    }

    public static function fromNew(): static
    {
        return new static(Uuid::v7());
    }

    public static function fromString(string $id): static
    {
        return new static($id);
    }

    public function toString(): string
    {
        return $this->id;
    }
}
