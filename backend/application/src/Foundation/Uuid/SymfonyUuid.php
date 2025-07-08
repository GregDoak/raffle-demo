<?php

declare(strict_types=1);

namespace App\Foundation\Uuid;

use Symfony\Component\Uid\Uuid;

final readonly class SymfonyUuid implements UuidInterface
{
    public static function isValid(string $uuid): bool
    {
        return Uuid::isValid($uuid);
    }

    public static function v4(): string
    {
        return Uuid::v4()->toString();
    }

    public static function v7(): string
    {
        return Uuid::v7()->toString();
    }
}
