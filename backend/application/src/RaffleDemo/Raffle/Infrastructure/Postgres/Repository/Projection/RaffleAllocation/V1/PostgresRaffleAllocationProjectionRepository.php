<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Infrastructure\Postgres\Repository\Projection\RaffleAllocation\V1;

use App\Foundation\Clock\Clock;
use App\RaffleDemo\Raffle\Domain\Projection\RaffleAllocation\V1\RaffleAllocation;
use App\RaffleDemo\Raffle\Domain\Projection\RaffleAllocation\V1\RaffleAllocationProjectionRepositoryInterface;
use App\RaffleDemo\Raffle\Domain\Projection\RaffleAllocation\V1\RaffleAllocationQuery;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;

use function array_map;
use function is_int;

final readonly class PostgresRaffleAllocationProjectionRepository implements RaffleAllocationProjectionRepositoryInterface
{
    public function __construct(
        private Connection $connection,
    ) {
    }

    public function store(RaffleAllocation $raffleAllocation): void
    {
        $sql = <<<SQL
            INSERT INTO raffle.projection_raffle_allocations_v1
                (raffle_id, hash, allocated_at, allocated_to, quantity, last_occurred_at)
            VALUES
                (:raffle_id, :hash, :allocated_at, :allocated_to, :quantity, :last_occurred_at)
        SQL;

        $statement = $this->connection->prepare($sql);

        $statement->bindValue('raffle_id', $raffleAllocation->raffleId);
        $statement->bindValue('hash', $raffleAllocation->hash);
        $statement->bindValue('allocated_at', $raffleAllocation->allocatedAt->format('Y-m-d H:i:s.u O'));
        $statement->bindValue('allocated_to', $raffleAllocation->allocatedTo);
        $statement->bindValue('quantity', $raffleAllocation->quantity, ParameterType::INTEGER);
        $statement->bindValue('last_occurred_at', $raffleAllocation->lastOccurredAt->format('Y-m-d H:i:s.u O'));

        $statement->executeStatement();
    }

    public function query(RaffleAllocationQuery $query): array
    {
        $sql = 'SELECT * FROM raffle.projection_raffle_allocations_v1 WHERE 1=1';
        $params = [];

        if ($query->raffleId !== null) {
            $sql .= ' AND projection_raffle_allocations_v1.raffle_id = :raffle_id';
            $params['raffle_id'] = $query->raffleId;
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
     * raffle_id: string,
     * hash: string,
     * allocated_at: string,
     * allocated_to: string,
     * quantity: int,
     * last_occurred_at: string,
     * } $record
     */
    private static function mapRecordToObject(array $record): RaffleAllocation
    {
        return new RaffleAllocation(
            raffleId: $record['raffle_id'],
            hash: $record['hash'],
            allocatedAt: Clock::fromString($record['allocated_at']),
            allocatedTo: $record['allocated_to'],
            quantity: $record['quantity'],
            lastOccurredAt: Clock::fromString($record['last_occurred_at']),
        );
    }
}
