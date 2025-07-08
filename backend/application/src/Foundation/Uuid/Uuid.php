<?php

declare(strict_types=1);

namespace App\Foundation\Uuid;

final readonly class Uuid implements UuidInterface
{
    public static function isValid(string $uuid): bool
    {
        return UuidProvider::get()::isValid($uuid);
    }

    public static function v4(): string
    {
        return UuidProvider::get()::v4();
    }

    public static function v7(): string
    {
        return UuidProvider::get()::v7();
    }
}
