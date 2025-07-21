<?php

declare(strict_types=1);

namespace App\Framework\Domain\Model;

interface AggregateIdInterface
{
    public static function fromNew(): static;

    public static function fromString(string $id): static;

    public function toString(): string;

    public function equals(self $that): bool;
}
