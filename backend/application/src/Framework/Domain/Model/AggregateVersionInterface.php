<?php

declare(strict_types=1);

namespace App\Framework\Domain\Model;

interface AggregateVersionInterface
{
    public static function fromInt(int $version): static;

    public static function fromNew(): static;

    public function next(): static;

    public function toInt(): int;
}
