<?php

declare(strict_types=1);

namespace App\Framework\Infrastructure\Symfony\Messenger\Middleware;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\NoHandlerForMessageException;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;

use function sprintf;

final readonly class SendToPublisherMiddleware implements MiddlewareInterface
{
    public function __construct(
        private TransportInterface $transport,
    ) {
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        if ($envelope->last(SendToPublisherStamp::class) === null) {
            throw new NoHandlerForMessageException(sprintf('Unable to publish an unstamped message of type: "%s"', $envelope->getMessage()::class));
        }

        return $this->transport->send($envelope);
    }
}
