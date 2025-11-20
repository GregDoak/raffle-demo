<?php

declare(strict_types=1);

namespace App\Framework\Infrastructure\Symfony\Messenger\Middleware;

use Symfony\Component\Messenger\Stamp\StampInterface;

final readonly class RetryStamp implements StampInterface
{
    private const int RETRY_LIMIT = 3;

    public function __construct(
        public int $retryCount,
    ) {
    }

    public function hasExceededRetryLimit(): bool
    {
        return $this->retryCount >= self::RETRY_LIMIT;
    }
}
