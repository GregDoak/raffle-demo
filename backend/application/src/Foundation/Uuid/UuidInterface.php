<?php

declare(strict_types=1);

namespace App\Foundation\Uuid;

interface UuidInterface
{
    public static function isValid(string $uuid): bool;

    public static function v4(): string;

    public static function v7(): string;
}
