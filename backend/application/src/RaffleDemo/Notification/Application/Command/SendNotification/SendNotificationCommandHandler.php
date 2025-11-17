<?php

declare(strict_types=1);

namespace App\RaffleDemo\Notification\Application\Command\SendNotification;

use App\Framework\Application\Command\CommandHandlerInterface;
use App\Framework\Domain\Repository\TransactionBoundaryInterface;
use App\RaffleDemo\Notification\Application\Service\Notifier\NotifierInterface;
use App\RaffleDemo\Notification\Domain\Model\Notification;
use App\RaffleDemo\Notification\Domain\Repository\NotificationRepositoryInterface;
use App\RaffleDemo\Notification\Domain\ValueObject\Status;
use Throwable;

final readonly class SendNotificationCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private NotificationRepositoryInterface $repository,
        private NotifierInterface $notifier,
        private TransactionBoundaryInterface $transactionBoundary,
    ) {
    }

    public function __invoke(SendNotificationCommand $command): void
    {
        try {
            $this->transactionBoundary->begin();
            $notification = Notification::create(
                $command->notification->getId(),
                $command->notification->getType(),
                $command->notification->getChannel(),
                $command->notification->getRecipient(),
                $command->notification->getCcRecipients(),
                $command->notification->getBccRecipients(),
                $command->notification->getSender(),
                $command->notification->getSubject(),
                $command->notification->getBody(),
                Status::SENT,
            );

            $this->repository->store($notification);
            $this->notifier->notify($command->notification);
            $this->transactionBoundary->commit();
        } catch (Throwable $exception) {
            $this->transactionBoundary->rollback();
            throw $exception;
        }
    }
}
