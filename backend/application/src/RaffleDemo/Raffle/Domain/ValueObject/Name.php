<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Domain\ValueObject;

use App\Framework\Domain\ValueObject\AbstractString;
use App\RaffleDemo\Raffle\Domain\Exception\InvalidNameException;

use function strlen;

final readonly class Name extends AbstractString
{
    protected function __construct(
        string $value,
    ) {
        if ($value === '') {
            throw InvalidNameException::fromEmptyName();
        }

        if (strlen($value) < 3) {
            throw InvalidNameException::fromTooShort();
        }

        if (strlen($value) > 200) {
            throw InvalidNameException::fromTooLong();
        }

        parent::__construct($value);
    }
}
