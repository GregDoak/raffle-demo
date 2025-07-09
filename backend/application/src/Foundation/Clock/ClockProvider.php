<?php

declare(strict_types=1);

namespace App\Foundation\Clock;

final class ClockProvider
{
    private static ClockInterface $clock;

    public static function get(): ClockInterface
    {
        return self::$clock ?? new NativeClock();
    }

    public function set(ClockInterface $clock): void
    {
        self::$clock = $clock;
    }
}
