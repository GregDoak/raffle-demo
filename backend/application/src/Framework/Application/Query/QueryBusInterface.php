<?php

declare(strict_types=1);

namespace App\Framework\Application\Query;

interface QueryBusInterface
{
    public function query(QueryInterface $query): ResultInterface;
}
