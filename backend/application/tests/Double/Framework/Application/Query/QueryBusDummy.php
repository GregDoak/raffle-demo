<?php

declare(strict_types=1);

namespace App\Tests\Double\Framework\Application\Query;

use App\Framework\Application\Query\QueryBusInterface;
use App\Framework\Application\Query\QueryInterface;
use App\Framework\Application\Query\ResultInterface;

final readonly class QueryBusDummy implements QueryBusInterface
{
    public function __construct(
        private ResultInterface $result,
    ) {
    }

    public function query(QueryInterface $query): ResultInterface
    {
        return $this->result;
    }
}
