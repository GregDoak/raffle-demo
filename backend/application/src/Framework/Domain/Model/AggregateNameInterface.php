<?php

declare(strict_types=1);

namespace App\Framework\Domain\Model;

interface AggregateNameInterface
{
    public static function fromString(string $name): static;

    public function toString(): string;
}
