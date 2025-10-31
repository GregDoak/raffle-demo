<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Infrastructure\Postgres\Repository\Projection\RaffleWinner\V1;

use App\Foundation\Clock\Clock;
use App\RaffleDemo\Raffle\Domain\Projection\RaffleWinner\V1\RaffleWinner;
use App\RaffleDemo\Raffle\Domain\Projection\RaffleWinner\V1\RaffleWinnerProjectionRepositoryInterface;
use App\RaffleDemo\Raffle\Domain\Projection\RaffleWinner\V1\RaffleWinnerQuery;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;

use function array_map;
use function is_int;

final readonly class PostgresRaffleWinnerProjectionRepository implements RaffleWinnerProjectionRepositoryInterface
{
    public function __construct(
        private Connection $connection,
    ) {
    }

    public function store(RaffleWinner $raffleWinner): void
    {
        $sql = <<<SQL
            INSERT INTO raffle.projection_raffle_winners_v1
                (raffle_id, raffle_allocation_hash, drawn_at, winning_ticket_number, winner, last_occurred_at)
            VALUES
                (:raffle_id, :raffle_allocation_hash, :drawn_at, :winning_ticket_number, :winner, :last_occurred_at)
        SQL;

        $statement = $this->connection->prepare($sql);

        $statement->bindValue('raffle_id', $raffleWinner->raffleId);
        $statement->bindValue('raffle_allocation_hash', $raffleWinner->raffleAllocationHash);
        $statement->bindValue('drawn_at', $raffleWinner->drawnAt->format('Y-m-d H:i:s.u O'));
        $statement->bindValue('winning_ticket_number', $raffleWinner->winningTicketNumber, ParameterType::INTEGER);
        $statement->bindValue('winner', $raffleWinner->winner);
        $statement->bindValue('last_occurred_at', $raffleWinner->lastOccurredAt->format('Y-m-d H:i:s.u O'));

        $statement->executeStatement();
    }

    public function query(RaffleWinnerQuery $query): array
    {
        $sql = 'SELECT * FROM raffle.projection_raffle_winners_v1 WHERE 1=1';
        $params = [];

        if ($query->raffleId !== null) {
            $sql .= ' AND projection_raffle_winners_v1.raffle_id = :raffle_id';
            $params['raffle_id'] = $query->raffleId;
        }

        if ($query->drawnAt !== null) {
            $sql .= ' AND projection_raffle_winners_v1.drawn_at <= :drawn_at';
            $params['drawn_at'] = $query->drawnAt->format('Y-m-d H:i:s.u O');
        }

        if ($query->sortField !== null) {
            $sql .= ' ORDER BY '.$this->convertToSnakeCase($query->sortField).' '.$query->sortOrder;
        } else {
            $sql .= ' ORDER BY raffle_id, drawn_at';
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
     * raffle_id: string,
     * raffle_allocation_hash: string,
     * drawn_at: string,
     * winning_ticket_number: int,
     * winner: string,
     * last_occurred_at: string,
     * } $record
     */
    private static function mapRecordToObject(array $record): RaffleWinner
    {
        return new RaffleWinner(
            raffleId: $record['raffle_id'],
            raffleAllocationHash: $record['raffle_allocation_hash'],
            drawnAt: Clock::fromString($record['drawn_at']),
            winningTicketNumber: $record['winning_ticket_number'],
            winner: $record['winner'],
            lastOccurredAt: Clock::fromString($record['last_occurred_at']),
        );
    }
}
