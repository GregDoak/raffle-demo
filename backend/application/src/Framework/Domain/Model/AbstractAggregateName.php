<?php

declare(strict_types=1);

namespace App\Framework\Domain\Model;

use App\Framework\Domain\Exception\InvalidDomainNameException;

use function trim;

abstract readonly class AbstractAggregateName implements AggregateNameInterface
{
    final private function __construct(
        private string $name,
    ) {
        if (trim($name) === '') {
            throw InvalidDomainNameException::fromEmptyName(static::class);
        }
    }

    public static function fromString(string $name): static
    {
        return new static($name);
    }

    public function toString(): string
    {
        return $this->name;
    }

    public function equals(AggregateNameInterface $that): bool
    {
        return $this->name === $that->toString();
    }
}
