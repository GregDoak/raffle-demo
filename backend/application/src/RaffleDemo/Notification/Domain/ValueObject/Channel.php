<?php

declare(strict_types=1);

namespace App\RaffleDemo\Notification\Domain\ValueObject;

enum Channel: string
{
    case EMAIL = 'email';
}
