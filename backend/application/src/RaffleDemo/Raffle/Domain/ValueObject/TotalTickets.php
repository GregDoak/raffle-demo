<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Domain\ValueObject;

use App\Framework\Domain\ValueObject\AbstractInt;
use App\RaffleDemo\Raffle\Domain\Exception\InvalidTotalTicketsException;

final readonly class TotalTickets extends AbstractInt
{
    protected function __construct(
        int $value,
    ) {
        if ($value < 1) {
            throw InvalidTotalTicketsException::fromLessThan1();
        }

        parent::__construct($value);
    }
}
