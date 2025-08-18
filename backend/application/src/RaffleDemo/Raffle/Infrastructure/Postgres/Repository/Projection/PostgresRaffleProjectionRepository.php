<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Infrastructure\Postgres\Repository\Projection;

use App\Foundation\Clock\Clock;
use App\RaffleDemo\Raffle\Domain\Projection\Raffle\Raffle;
use App\RaffleDemo\Raffle\Domain\Projection\Raffle\RaffleProjectionRepositoryInterface;
use DateTimeInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;

use function array_map;

final readonly class PostgresRaffleProjectionRepository implements RaffleProjectionRepositoryInterface
{
    public function __construct(
        private Connection $connection,
    ) {
    }

    public function store(Raffle $raffle): void
    {
        $sql = <<<SQL
            INSERT INTO raffle.projection_raffle
                (id, name, prize, created_at, created_by, start_at, started_at, started_by, total_tickets, remaining_tickets, ticket_amount, ticket_currency, close_at, closed_at, closed_by, draw_at, drawn_at, drawn_by, winning_allocation, winning_ticket_number, won_by, last_occurred_at)
            VALUES
                (:id, :name, :prize, :created_at, :created_by, :start_at, :started_at, :started_by, :total_tickets, :remaining_tickets, :ticket_amount, :ticket_currency, :close_at, :closed_at, :closed_by, :draw_at, :drawn_at, :drawn_by, :winning_allocation, :winning_ticket_number, :won_by, :last_occurred_at)
            ON CONFLICT (id) DO UPDATE
                SET
                    name = :name,
                    prize = :prize,
                    created_at = :created_at,
                    created_by = :created_by,
                    start_at = :start_at,
                    started_at = :started_at,
                    started_by = :started_by,
                    total_tickets = :total_tickets,
                    remaining_tickets = :remaining_tickets,
                    ticket_amount = :ticket_amount,
                    ticket_currency = :ticket_currency,
                    close_at = :close_at,
                    closed_at = :closed_at,
                    closed_by = :closed_by,
                    draw_at = :draw_at,
                    drawn_at = :drawn_at,
                    drawn_by = :drawn_by,
                    winning_allocation = :winning_allocation,
                    winning_ticket_number = :winning_ticket_number,
                    won_by = :won_by,
                    last_occurred_at = :last_occurred_at
        SQL;

        $statement = $this->connection->prepare($sql);

        $statement->bindValue('id', $raffle->id);
        $statement->bindValue('name', $raffle->name);
        $statement->bindValue('prize', $raffle->prize);
        $statement->bindValue('created_at', $raffle->createdAt->format('Y-m-d H:i:s.u O'));
        $statement->bindValue('created_by', $raffle->createdBy);
        $statement->bindValue('start_at', $raffle->startAt->format('Y-m-d H:i:s.u O'));
        $statement->bindValue('started_at', $raffle->startedAt?->format('Y-m-d H:i:s.u O'));
        $statement->bindValue('started_by', $raffle->startedBy);
        $statement->bindValue('total_tickets', $raffle->totalTickets, ParameterType::INTEGER);
        $statement->bindValue('remaining_tickets', $raffle->remainingTickets, ParameterType::INTEGER);
        $statement->bindValue('ticket_amount', $raffle->ticketAmount, ParameterType::INTEGER);
        $statement->bindValue('ticket_currency', $raffle->ticketCurrency);
        $statement->bindValue('close_at', $raffle->closeAt->format('Y-m-d H:i:s.u O'));
        $statement->bindValue('closed_at', $raffle->closedAt?->format('Y-m-d H:i:s.u O'));
        $statement->bindValue('closed_by', $raffle->closedBy);
        $statement->bindValue('draw_at', $raffle->drawAt->format('Y-m-d H:i:s.u O'));
        $statement->bindValue('drawn_at', $raffle->drawnAt?->format('Y-m-d H:i:s.u O'));
        $statement->bindValue('drawn_by', $raffle->drawnBy);
        $statement->bindValue('winning_allocation', $raffle->winningAllocation);
        $statement->bindValue('winning_ticket_number', $raffle->winningTicketNumber, ParameterType::INTEGER);
        $statement->bindValue('won_by', $raffle->wonBy);
        $statement->bindValue('last_occurred_at', $raffle->lastOccurredAt->format('Y-m-d H:i:s.u O'));

        $statement->executeStatement();
    }

    public function getById(string $id): ?Raffle
    {
        $sql = <<<SQL
            SELECT
                *
            FROM
                raffle.projection_raffle
            WHERE
                projection_raffle.id = :id
        SQL;

        $statement = $this->connection->prepare($sql);

        $statement->bindValue('id', $id);

        $record = $statement->executeQuery()->fetchAssociative();

        if ($record === false) {
            return null;
        }

        return self::mapRecordToObject($record); // @phpstan-ignore-line argument.type
    }

    public function getRafflesDueToBeStarted(DateTimeInterface $now): array
    {
        $sql = <<<SQL
            SELECT
                *
            FROM
                raffle.projection_raffle
            WHERE
                projection_raffle.started_at IS NULL
                AND projection_raffle.start_at <= :start_at
            ORDER BY
                projection_raffle.start_at ASC
        SQL;

        $statement = $this->connection->prepare($sql);

        $statement->bindValue('start_at', $now->format('Y-m-d H:i:s.u O'));

        return array_map(
            self::mapRecordToObject(...), // @phpstan-ignore-line argument.type
            $statement->executeQuery()->fetchAllAssociative(),
        );
    }

    public function getRafflesDueToBeClosed(DateTimeInterface $now): array
    {
        $sql = <<<SQL
            SELECT
                *
            FROM
                raffle.projection_raffle
            WHERE
                projection_raffle.started_at IS NOT NULL
                AND projection_raffle.closed_at IS NULL
                AND projection_raffle.close_at <= :close_at
            ORDER BY
                projection_raffle.close_at ASC
        SQL;

        $statement = $this->connection->prepare($sql);

        $statement->bindValue('close_at', $now->format('Y-m-d H:i:s.u O'));

        return array_map(
            self::mapRecordToObject(...), // @phpstan-ignore-line argument.type
            $statement->executeQuery()->fetchAllAssociative(),
        );
    }

    public function getRafflesDueToBeDrawn(DateTimeInterface $now): array
    {
        $sql = <<<SQL
            SELECT
                *
            FROM
                raffle.projection_raffle
            WHERE
                projection_raffle.started_at IS NOT NULL
                AND projection_raffle.closed_at IS NOT NULL
                AND projection_raffle.drawn_at IS NULL
                AND projection_raffle.draw_at <= :draw_at
            ORDER BY
                projection_raffle.draw_at ASC
        SQL;

        $statement = $this->connection->prepare($sql);

        $statement->bindValue('draw_at', $now->format('Y-m-d H:i:s.u O'));

        return array_map(
            self::mapRecordToObject(...), // @phpstan-ignore-line argument.type
            $statement->executeQuery()->fetchAllAssociative(),
        );
    }

    /** @param array{
     * id: string,
     * name: string,
     * prize: string,
     * created_at: string,
     * created_by: string,
     * start_at: string,
     * started_at: ?string,
     * started_by: ?string,
     * total_tickets: int,
     * remaining_tickets: int,
     * ticket_amount: int,
     * ticket_currency: string,
     * close_at: string,
     * closed_at: ?string,
     * closed_by: ?string,
     * draw_at: string,
     * drawn_at: ?string,
     * drawn_by: ?string,
     * winning_allocation: ?string,
     * winning_ticket_number: ?int,
     * won_by: ?string,
     * last_occurred_at: string,
     * } $record
     */
    private static function mapRecordToObject(array $record): Raffle
    {
        return new Raffle(
            id: $record['id'],
            name: $record['name'],
            prize: $record['prize'],
            createdAt: Clock::fromString($record['created_at']),
            createdBy: $record['created_by'],
            startAt: Clock::fromString($record['start_at']),
            startedAt: Clock::fromNullableString($record['started_at']),
            startedBy: $record['started_by'],
            totalTickets: $record['total_tickets'],
            remainingTickets: $record['remaining_tickets'],
            ticketAmount: $record['ticket_amount'],
            ticketCurrency: $record['ticket_currency'],
            closeAt: Clock::fromString($record['close_at']),
            closedAt: Clock::fromNullableString($record['closed_at']),
            closedBy: $record['closed_by'],
            drawAt: Clock::fromString($record['draw_at']),
            drawnAt: Clock::fromNullableString($record['drawn_at']),
            drawnBy: $record['drawn_by'],
            winningAllocation: $record['winning_allocation'],
            winningTicketNumber: $record['winning_ticket_number'],
            wonBy: $record['won_by'],
            lastOccurredAt: Clock::fromString($record['last_occurred_at']),
        );
    }
}
