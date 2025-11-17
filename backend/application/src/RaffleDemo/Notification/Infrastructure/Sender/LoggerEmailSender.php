<?php

declare(strict_types=1);

namespace App\RaffleDemo\Notification\Infrastructure\Sender;

use App\RaffleDemo\Notification\Application\Command\SendNotification\Notification\NotificationInterface;
use App\RaffleDemo\Notification\Application\Service\Sender\SenderInterface;
use App\RaffleDemo\Notification\Domain\ValueObject\Channel;
use Psr\Log\LoggerInterface;

use function sprintf;

final readonly class LoggerEmailSender implements SenderInterface
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    public function supports(NotificationInterface $notification): bool
    {
        return $notification->getChannel() === Channel::EMAIL; // @phpstan-ignore-line identical.alwaysTrue
    }

    public function send(NotificationInterface $notification): void
    {
        $this->logger->notice(
            sprintf('Sending notification: %s', $notification::class),
            ['notification' => $notification],
        );
    }
}
