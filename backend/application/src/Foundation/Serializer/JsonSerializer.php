<?php

declare(strict_types=1);

namespace App\Foundation\Serializer;

use function json_decode;
use function json_encode;

final readonly class JsonSerializer
{
    public static function deserialize(string $json): mixed
    {
        return json_decode($json, true, flags: JSON_THROW_ON_ERROR);
    }

    public static function serialize(mixed $array): string
    {
        return json_encode($array, JSON_THROW_ON_ERROR);
    }
}
