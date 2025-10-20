<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Infrastructure\Postgres\Repository\Projection\Raffle\V1;

use App\Foundation\Clock\Clock;
use App\RaffleDemo\Raffle\Domain\Projection\Raffle\V1\Raffle;
use App\RaffleDemo\Raffle\Domain\Projection\Raffle\V1\RaffleProjectionRepositoryInterface;
use App\RaffleDemo\Raffle\Domain\Projection\Raffle\V1\RaffleQuery;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;

use function array_map;
use function is_int;

final readonly class PostgresRaffleProjectionRepository implements RaffleProjectionRepositoryInterface
{
    public function __construct(
        private Connection $connection,
    ) {
    }

    public function store(Raffle $raffle): void
    {
        $sql = <<<SQL
            INSERT INTO raffle.projection_raffles_v1
                (id, name, prize, status, created_at, created_by, start_at, started_at, started_by, total_tickets, remaining_tickets, ticket_amount, ticket_currency, close_at, closed_at, closed_by, draw_at, drawn_at, drawn_by, winning_allocation, winning_ticket_number, won_by, ended_at, ended_by, ended_reason, last_occurred_at)
            VALUES
                (:id, :name, :prize, :status, :created_at, :created_by, :start_at, :started_at, :started_by, :total_tickets, :remaining_tickets, :ticket_amount, :ticket_currency, :close_at, :closed_at, :closed_by, :draw_at, :drawn_at, :drawn_by, :winning_allocation, :winning_ticket_number, :won_by, :ended_at, :ended_by, :ended_reason, :last_occurred_at)
            ON CONFLICT (id) DO UPDATE
                SET
                    name = :name,
                    prize = :prize,
                    status = :status,
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
                    ended_at = :ended_at,
                    ended_by = :ended_by,
                    ended_reason = :ended_reason,
                    last_occurred_at = :last_occurred_at
        SQL;

        $statement = $this->connection->prepare($sql);

        $statement->bindValue('id', $raffle->id);
        $statement->bindValue('name', $raffle->name);
        $statement->bindValue('prize', $raffle->prize);
        $statement->bindValue('status', $raffle->status);
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
        $statement->bindValue('ended_at', $raffle->endedAt?->format('Y-m-d H:i:s.u O'));
        $statement->bindValue('ended_by', $raffle->endedBy);
        $statement->bindValue('ended_reason', $raffle->endedReason);
        $statement->bindValue('last_occurred_at', $raffle->lastOccurredAt->format('Y-m-d H:i:s.u O'));

        $statement->executeStatement();
    }

    public function getById(string $id): ?Raffle
    {
        $sql = <<<SQL
            SELECT
                *
            FROM
                raffle.projection_raffles_v1
            WHERE
                projection_raffles_v1.id = :id
        SQL;

        $statement = $this->connection->prepare($sql);

        $statement->bindValue('id', $id);

        $record = $statement->executeQuery()->fetchAssociative();

        if ($record === false) {
            return null;
        }

        return self::mapRecordToObject($record); // @phpstan-ignore-line argument.type
    }

    public function query(RaffleQuery $query): array
    {
        $sql = 'SELECT * FROM raffle.projection_raffles_v1 WHERE 1=1';
        $params = [];

        if ($query->name !== null) {
            $sql .= ' AND projection_raffles_v1.name LIKE :name';
            $params['name'] = '%'.$query->name.'%';
        }

        if ($query->prize !== null) {
            $sql .= ' AND projection_raffles_v1.prize LIKE :prize';
            $params['prize'] = '%'.$query->prize.'%';
        }

        if ($query->status !== null) {
            $sql .= ' AND projection_raffles_v1.status LIKE :status';
            $params['status'] = '%'.$query->status.'%';
        }

        if ($query->startAt !== null) {
            $sql .= ' AND projection_raffles_v1.start_at <= :start_at';
            $params['start_at'] = $query->startAt->format('Y-m-d H:i:s.u O');
        }

        if ($query->closeAt !== null) {
            $sql .= ' AND projection_raffles_v1.close_at <= :close_at';
            $params['close_at'] = $query->closeAt->format('Y-m-d H:i:s.u O');
        }

        if ($query->drawAt !== null) {
            $sql .= ' AND projection_raffles_v1.draw_at <= :draw_at';
            $params['draw_at'] = $query->drawAt->format('Y-m-d H:i:s.u O');
        }

        if ($query->sortField !== null) {
            $sql .= ' ORDER BY '.$this->convertToSnakeCase($query->sortField).' '.$query->sortOrder;
        }

        if ($query->limit !== null) {
            $sql .= ' LIMIT :limit OFFSET :offset';
            $params['limit'] = $query->limit;
            $params['offset'] = $query->offset;
        }

        $statement = $this->connection->prepare($sql);

        foreach ($params as $key => $value) {
            $type = is_int($value) ? ParameterType::INTEGER : ParameterType::STRING;
            $statement->bindValue($key, $value, $type);
        }

        return array_map(
            self::mapRecordToObject(...), // @phpstan-ignore-line argument.type
            $statement->executeQuery()->fetchAllAssociative(),
        );
    }

    private function convertToSnakeCase(string $value): string
    {
        return (string) preg_replace_callback(
            pattern: '/[A-Z]/',
            callback: static fn (array $matches): string => '_'.strtolower($matches[0]),
            subject: $value,
        );
    }

    /** @param array{
     * id: string,
     * name: string,
     * prize: string,
     * status: string,
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
     * ended_at: ?string,
     * ended_by: ?string,
     * ended_reason: ?string,
     * last_occurred_at: string,
     * } $record
     */
    private static function mapRecordToObject(array $record): Raffle
    {
        return new Raffle(
            id: $record['id'],
            name: $record['name'],
            prize: $record['prize'],
            status: $record['status'],
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
            endedAt: Clock::fromNullableString($record['ended_at']),
            endedBy: $record['ended_by'],
            endedReason: $record['ended_reason'],
            lastOccurredAt: Clock::fromString($record['last_occurred_at']),
        );
    }
}
