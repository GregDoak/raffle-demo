<?php

declare(strict_types=1);

namespace App\Framework\Infrastructure\Symfony\Messenger\Middleware;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;

final readonly class SendToSubscriberMiddleware implements MiddlewareInterface
{
    public function __construct(
        private TransportInterface $failedTransport,
    ) {
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $retryStamp = $envelope->last(RetryStamp::class);

        if ($retryStamp instanceof RetryStamp && $retryStamp->hasExceededRetryLimit() === true) {
            return $this->failedTransport->send($envelope);
        }

        return $stack->next()->handle($envelope, $stack);
    }
}
