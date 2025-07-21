<?php

declare(strict_types=1);

namespace App\Tests\Double\Framework\Domain\Repository;

use App\Framework\Domain\Repository\TransactionBoundaryInterface;

final class TransactionBoundarySpy implements TransactionBoundaryInterface
{
    public bool $hasBegun = false;
    public bool $hasCommitted = false;
    public bool $hasRolledBack = false;

    public function begin(): void
    {
        $this->hasBegun = true;
    }

    public function commit(): void
    {
        $this->hasCommitted = true;
    }

    public function rollback(): void
    {
        $this->hasRolledBack = true;
    }
}
