<?php

declare(strict_types=1);

namespace App\Foundation\Uuid;

final class UuidProvider
{
    private static UuidInterface $uuid;

    public static function get(): UuidInterface
    {
        return self::$uuid ?? new SymfonyUuid();
    }

    public function set(UuidInterface $uuid): void
    {
        self::$uuid = $uuid;
    }
}
