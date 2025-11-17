<?php

declare(strict_types=1);

namespace App\RaffleDemo\Notification\Domain\Model;

use App\RaffleDemo\Notification\Domain\ValueObject\AbstractRecipient;
use App\RaffleDemo\Notification\Domain\ValueObject\Body;
use App\RaffleDemo\Notification\Domain\ValueObject\Channel;
use App\RaffleDemo\Notification\Domain\ValueObject\OccurredAt;
use App\RaffleDemo\Notification\Domain\ValueObject\RecipientCollection;
use App\RaffleDemo\Notification\Domain\ValueObject\Status;
use App\RaffleDemo\Notification\Domain\ValueObject\Subject;
use App\RaffleDemo\Notification\Domain\ValueObject\Type;

final readonly class Notification
{
    public function __construct(
        public NotificationId $id,
        public Type $type,
        public Channel $channel,
        public AbstractRecipient $recipient,
        public RecipientCollection $ccRecipients,
        public RecipientCollection $bccRecipients,
        public AbstractRecipient $sender,
        public Subject $subject,
        public Body $body,
        public Status $status,
        public OccurredAt $occurredAt,
    ) {
    }

    public static function create(
        NotificationId $id,
        Type $type,
        Channel $channel,
        AbstractRecipient $recipient,
        RecipientCollection $ccRecipients,
        RecipientCollection $bccRecipients,
        AbstractRecipient $sender,
        Subject $subject,
        Body $body,
        Status $status,
    ): self {
        return new self(
            $id,
            $type,
            $channel,
            $recipient,
            $ccRecipients,
            $bccRecipients,
            $sender,
            $subject,
            $body,
            $status,
            OccurredAt::fromNow(),
        );
    }
}
