<?php

declare(strict_types=1);

namespace App\Framework\Infrastructure\Doctrine;

use App\Framework\Domain\Repository\TransactionBoundaryInterface;
use Doctrine\DBAL\Connection;

final readonly class DbalTransactionBoundary implements TransactionBoundaryInterface
{
    public function __construct(
        private Connection $connection,
    ) {
    }

    public function begin(): void
    {
        $this->connection->beginTransaction();
    }

    public function commit(): void
    {
        if ($this->connection->isTransactionActive() === true) {
            $this->connection->commit();
        }
    }

    public function rollback(): void
    {
        if ($this->connection->isTransactionActive() === true) {
            $this->connection->rollBack();
        }
    }
}
