<?php

declare(strict_types=1);

namespace App\Framework\Domain\Repository;

interface TransactionBoundaryInterface
{
    public function begin(): void;

    public function commit(): void;

    public function rollback(): void;
}
