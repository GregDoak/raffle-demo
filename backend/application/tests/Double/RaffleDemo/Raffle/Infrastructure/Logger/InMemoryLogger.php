<?php

declare(strict_types=1);

namespace App\Tests\Double\RaffleDemo\Raffle\Infrastructure\Logger;

use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;
use Stringable;

final class InMemoryLogger implements LoggerInterface
{
    use LoggerTrait;
    /** @var array<string, array<array{message: string, context: array<int|string, mixed>}>> */
    private array $logger = [];

    /** @return array<string, array<array{message: string, context: array<int|string, mixed>}>> */
    public function getLogs(): array
    {
        return $this->logger;
    }

    /** @return array<array{message: string, context: array<int|string, mixed>}> */
    public function getLogsForLevel(string $level): array
    {
        return $this->logger[$level] ?? [];
    }

    public function log($level, Stringable|string $message, array $context = []): void
    {
        $this->logger[(string) $level][] = ['message' => (string) $message, 'context' => $context]; // @phpstan-ignore cast.string
    }
}
