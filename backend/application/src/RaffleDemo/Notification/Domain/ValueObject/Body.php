<?php

declare(strict_types=1);

namespace App\RaffleDemo\Notification\Domain\ValueObject;

use App\Framework\Domain\ValueObject\AbstractString;
use App\RaffleDemo\Notification\Domain\Exception\InvalidBodyException;

final readonly class Body extends AbstractString
{
    protected function __construct(string $value)
    {
        if ($value === '') {
            throw InvalidBodyException::fromEmptyBody();
        }

        parent::__construct($value);
    }
}
