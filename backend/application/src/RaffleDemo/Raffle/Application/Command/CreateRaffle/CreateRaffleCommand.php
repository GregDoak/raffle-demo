<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Application\Command\CreateRaffle;

use App\Foundation\Clock\Clock;
use App\Framework\Application\Command\CommandInterface;
use App\Framework\Application\Exception\ValidationException;
use App\Framework\Domain\Exception\InvalidAggregateIdException;
use App\Framework\Domain\Exception\InvalidDateTimeException;
use App\RaffleDemo\Raffle\Domain\Exception\InvalidCreatedException;
use App\RaffleDemo\Raffle\Domain\Exception\InvalidNameException;
use App\RaffleDemo\Raffle\Domain\Exception\InvalidPrizeException;
use App\RaffleDemo\Raffle\Domain\Exception\InvalidTicketPriceException;
use App\RaffleDemo\Raffle\Domain\Exception\InvalidTotalTicketsException;
use App\RaffleDemo\Raffle\Domain\Model\RaffleAggregateId;
use App\RaffleDemo\Raffle\Domain\ValueObject\CloseAt;
use App\RaffleDemo\Raffle\Domain\ValueObject\Created;
use App\RaffleDemo\Raffle\Domain\ValueObject\DrawAt;
use App\RaffleDemo\Raffle\Domain\ValueObject\Name;
use App\RaffleDemo\Raffle\Domain\ValueObject\Prize;
use App\RaffleDemo\Raffle\Domain\ValueObject\StartAt;
use App\RaffleDemo\Raffle\Domain\ValueObject\TicketPrice;
use App\RaffleDemo\Raffle\Domain\ValueObject\TotalTickets;

use function count;
use function sprintf;

final readonly class CreateRaffleCommand implements CommandInterface
{
    private function __construct(
        public RaffleAggregateId $id,
        public Name $name,
        public Prize $prize,
        public StartAt $startAt,
        public CloseAt $closeAt,
        public DrawAt $drawAt,
        public TotalTickets $totalTickets,
        public TicketPrice $ticketPrice,
        public Created $created,
    ) {
    }

    /** @param array{amount: int, currency: string} $ticketPrice */
    public static function create(
        string $name,
        string $prize,
        string $startAt,
        string $closeAt,
        string $drawAt,
        int $totalTickets,
        array $ticketPrice,
        string $createdBy,
    ): self {
        $errors = [];

        try {
            $id = RaffleAggregateId::fromNew();
        } catch (InvalidAggregateIdException $exception) {
            $errors[] = $exception->getMessage();
        }

        try {
            $name = Name::fromString($name);
        } catch (InvalidNameException $exception) {
            $errors[] = $exception->getMessage();
        }

        try {
            $prize = Prize::fromString($prize);
        } catch (InvalidPrizeException $exception) {
            $errors[] = $exception->getMessage();
        }

        try {
            $startAt = StartAt::fromString($startAt);
        } catch (InvalidDateTimeException $exception) {
            $errors[] = sprintf($exception->template, 'start at');
        }

        try {
            $closeAt = CloseAt::fromString($closeAt);
        } catch (InvalidDateTimeException $exception) {
            $errors[] = sprintf($exception->template, 'close at');
        }

        try {
            $drawAt = DrawAt::fromString($drawAt);
        } catch (InvalidDateTimeException $exception) {
            $errors[] = sprintf($exception->template, 'draw at');
        }

        try {
            $totalTickets = TotalTickets::fromInt($totalTickets);
        } catch (InvalidTotalTicketsException $exception) {
            $errors[] = $exception->getMessage();
        }

        try {
            $ticketPrice = TicketPrice::fromArray($ticketPrice);
        } catch (InvalidTicketPriceException $exception) {
            $errors[] = $exception->getMessage();
        }

        try {
            $createdBy = Created::from($createdBy, Clock::now());
        } catch (InvalidCreatedException $exception) {
            $errors[] = $exception->getMessage();
        }

        if (count($errors) > 0) {
            throw ValidationException::fromErrors($errors);
        }

        return new self(
            $id, // @phpstan-ignore variable.undefined
            $name, // @phpstan-ignore argument.type
            $prize, // @phpstan-ignore argument.type
            $startAt, // @phpstan-ignore argument.type
            $closeAt, // @phpstan-ignore argument.type
            $drawAt, // @phpstan-ignore argument.type
            $totalTickets, // @phpstan-ignore argument.type
            $ticketPrice, // @phpstan-ignore argument.type
            $createdBy, // @phpstan-ignore argument.type
        );
    }
}
