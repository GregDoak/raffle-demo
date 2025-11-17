<?php

declare(strict_types=1);

namespace App\RaffleDemo\Notification\Domain\ValueObject;

final readonly class RecipientCollection
{
    /** @var AbstractRecipient[] */
    private array $recipients;

    private function __construct(AbstractRecipient ...$recipients)
    {
        $this->recipients = $recipients;
    }

    public static function fromNew(): self
    {
        return new self();
    }

    public static function fromArray(AbstractRecipient ...$recipients): self
    {
        return new self(...$recipients);
    }

    /** @return AbstractRecipient[] */
    public function getRecipients(): array
    {
        return $this->recipients;
    }

    /** @return array<string> */
    public function toArray(): array
    {
        return array_map(static fn (AbstractRecipient $recipient) => $recipient->toString(), $this->getRecipients());
    }
}
