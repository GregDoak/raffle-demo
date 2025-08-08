<?php

declare(strict_types=1);

namespace App\Framework\Domain\Exception;

use App\Framework\Domain\Model\AggregateIdInterface;
use RuntimeException;

final class AggregateNotFoundException extends RuntimeException
{
    private function __construct(
        public string $id,
    ) {
        parent::__construct('The requested id was not found.');
    }

    public static function fromAggregateId(AggregateIdInterface $id): self
    {
        return new self($id->toString());
    }
}
