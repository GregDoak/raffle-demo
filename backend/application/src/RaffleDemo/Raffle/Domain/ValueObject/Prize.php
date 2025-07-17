<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Domain\ValueObject;

use App\Framework\Domain\ValueObject\AbstractString;
use App\RaffleDemo\Raffle\Domain\Exception\InvalidPrizeException;

use function strlen;

final readonly class Prize extends AbstractString
{
    protected function __construct(string $value)
    {
        if ($value === '') {
            throw InvalidPrizeException::fromEmptyPrize();
        }

        if (strlen($value) < 3) {
            throw InvalidPrizeException::fromTooShort();
        }

        if (strlen($value) > 200) {
            throw InvalidPrizeException::fromTooLong();
        }

        parent::__construct($value);
    }
}
