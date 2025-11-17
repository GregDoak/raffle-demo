<?php

declare(strict_types=1);

namespace App\RaffleDemo\Notification\Infrastructure\Repository;

use App\Foundation\Serializer\JsonSerializer;
use App\RaffleDemo\Notification\Domain\Exception\InvalidNotificationException;
use App\RaffleDemo\Notification\Domain\Model\Notification;
use App\RaffleDemo\Notification\Domain\Model\NotificationId;
use App\RaffleDemo\Notification\Domain\Repository\NotificationRepositoryInterface;
use App\RaffleDemo\Notification\Domain\ValueObject\AbstractRecipient;
use App\RaffleDemo\Notification\Domain\ValueObject\Body;
use App\RaffleDemo\Notification\Domain\ValueObject\Channel;
use App\RaffleDemo\Notification\Domain\ValueObject\EmailAddress;
use App\RaffleDemo\Notification\Domain\ValueObject\OccurredAt;
use App\RaffleDemo\Notification\Domain\ValueObject\RecipientCollection;
use App\RaffleDemo\Notification\Domain\ValueObject\Status;
use App\RaffleDemo\Notification\Domain\ValueObject\Subject;
use App\RaffleDemo\Notification\Domain\ValueObject\Type;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

final readonly class PostgresNotificationRepository implements NotificationRepositoryInterface
{
    public function __construct(
        private Connection $connection,
    ) {
    }

    public function getById(NotificationId $id): ?Notification
    {
        $sql = <<<SQL
            SELECT
                notifications.id,
                notifications.type,
                notifications.channel,
                notifications.recipient,
                notifications.cc_recipients,
                notifications.bcc_recipients,
                notifications.sender,
                notifications.subject,
                notifications.body,
                notifications.status,
                notifications.occurred_at
            FROM
                notification.notifications
            WHERE
                notifications.id = :id
        SQL;

        $statement = $this->connection->prepare($sql);

        $statement->bindValue('id', $id->toString());

        $record = $statement->executeQuery()->fetchAssociative();

        if ($record === false) {
            return null;
        }

        return self::mapRecordToObject($record); // @phpstan-ignore-line argument.type
    }

    public function getByRecipient(AbstractRecipient $recipient): array
    {
        $sql = <<<SQL
            SELECT
                notifications.id,
                notifications.type,
                notifications.channel,
                notifications.recipient,
                notifications.cc_recipients,
                notifications.bcc_recipients,
                notifications.sender,
                notifications.subject,
                notifications.body,
                notifications.status,
                notifications.occurred_at
            FROM
                notification.notifications
            WHERE
                notifications.recipient = :recipient
            ORDER BY
                notifications.serial DESC
        SQL;

        $statement = $this->connection->prepare($sql);

        $statement->bindValue('recipient', $recipient->toString());

        return array_map(
            self::mapRecordToObject(...), // @phpstan-ignore-line argument.type
            $statement->executeQuery()->fetchAllAssociative(),
        );
    }

    /** @throws InvalidNotificationException */
    public function store(Notification $notification): void
    {
        $sql = <<<SQL
            INSERT INTO notification.notifications
                (id, type, channel, recipient, cc_recipients, bcc_recipients, sender, subject, body, status, occurred_at)
            VALUES
                (:id, :type, :channel, :recipient, :cc_recipients, :bcc_recipients, :sender, :subject, :body, :status, :occurred_at)
        SQL;

        $statement = $this->connection->prepare($sql);

        $statement->bindValue('id', $notification->id->toString());
        $statement->bindValue('type', $notification->type->toString());
        $statement->bindValue('channel', $notification->channel->value);
        $statement->bindValue('recipient', $notification->recipient->toString());
        $statement->bindValue('cc_recipients', JsonSerializer::serialize($notification->ccRecipients->toArray()));
        $statement->bindValue('bcc_recipients', JsonSerializer::serialize($notification->bccRecipients->toArray()));
        $statement->bindValue('sender', $notification->sender->toString());
        $statement->bindValue('subject', $notification->subject->toString());
        $statement->bindValue('body', $notification->body->toString());
        $statement->bindValue('status', $notification->status->value);
        $statement->bindValue('occurred_at', $notification->occurredAt->toString('Y-m-d H:i:s.u O'));

        try {
            $statement->executeStatement();
        } catch (UniqueConstraintViolationException) {
            throw InvalidNotificationException::fromDuplicateNotification();
        }
    }

    /** @param array{
     * id: string,
     * type: string,
     * channel: string,
     * recipient: string,
     * cc_recipients: string,
     * bcc_recipients: string,
     * sender: string,
     * subject: string,
     * body: string,
     * status: string,
     * occurred_at: string,
     * } $record
     */
    private static function mapRecordToObject(array $record): Notification
    {
        $channel = Channel::from($record['channel']);

        return new Notification(
            id: NotificationId::fromString($record['id']),
            type: Type::fromString($record['type']),
            channel: $channel,
            recipient: self::getRecipientFromChannel($channel, $record['recipient']),
            ccRecipients: RecipientCollection::fromArray(
                ...array_map(
                    static fn (string $recipient) => self::getRecipientFromChannel($channel, $recipient), // @phpstan-ignore-line argument.type
                    (array) JsonSerializer::deserialize($record['cc_recipients'])),
            ),
            bccRecipients: RecipientCollection::fromArray(
                ...array_map(
                    static fn (string $recipient) => self::getRecipientFromChannel($channel, $recipient), // @phpstan-ignore-line argument.type
                    (array) JsonSerializer::deserialize($record['bcc_recipients'])),
            ),
            sender: self::getRecipientFromChannel($channel, $record['sender']),
            subject: Subject::fromString($record['subject']),
            body: Body::fromString($record['body']),
            status: Status::from($record['status']),
            occurredAt: OccurredAt::fromString($record['occurred_at']),
        );
    }

    private static function getRecipientFromChannel(Channel $channel, string $recipient): AbstractRecipient
    {
        return match ($channel) {
            Channel::EMAIL => EmailAddress::fromString($recipient),
        };
    }
}
