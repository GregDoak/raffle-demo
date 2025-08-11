<?php

declare(strict_types=1);

namespace App\Framework\Infrastructure\Symfony\Messenger;

use App\Framework\Application\Query\QueryBusInterface;
use App\Framework\Application\Query\QueryInterface;
use App\Framework\Application\Query\ResultInterface;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

final class SymfonyQueryBus implements QueryBusInterface
{
    use HandleTrait;

    public function __construct(
        MessageBusInterface $queryBus,
    ) {
        $this->messageBus = $queryBus;
    }

    public function query(QueryInterface $query): ResultInterface
    {
        return $this->handle($query); // @phpstan-ignore return.type
    }
}
