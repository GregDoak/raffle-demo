<?php

declare(strict_types=1);

namespace App\RaffleDemo\Notification\Domain\ValueObject;

enum Status: string
{
    case SENT = 'sent';
}
