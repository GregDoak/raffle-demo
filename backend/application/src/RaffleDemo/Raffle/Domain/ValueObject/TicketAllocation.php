<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Domain\ValueObject;

use App\Foundation\Clock\Clock;
use App\RaffleDemo\Raffle\Domain\Exception\InvalidTicketAllocationException;
use DateTimeInterface;

use function sprintf;
use function strlen;

final readonly class TicketAllocation
{
    public string $hash;

    private function __construct(
        public int $quantity,
        public string $allocatedTo,
        public DateTimeInterface $allocatedAt,
    ) {
        $this->hash = $this->generateHash();

        if ($this->quantity < 1) {
            throw InvalidTicketAllocationException::fromInvalidQuantity();
        }

        if ($this->allocatedTo === '') {
            throw InvalidTicketAllocationException::fromEmptyAllocatedTo();
        }

        if (strlen($this->allocatedTo) > 200) {
            throw InvalidTicketAllocationException::fromAllocatedToTooLong();
        }
    }

    public static function from(int $quantity, string $allocatedTo, DateTimeInterface $allocatedAt): self
    {
        return new self($quantity, $allocatedTo, $allocatedAt);
    }

    /**
     * @infection-ignore-all
     * @param array{quantity: ?int, allocatedTo: ?string, allocatedAt: ?string} $array
     */
    public static function fromArray(array $array): self
    {
        return new self(
            quantity: $array['quantity'] ?? 0,
            allocatedTo: $array['allocatedTo'] ?? '',
            allocatedAt: Clock::fromString($array['allocatedAt'] ?? 'INVALID'),
        );
    }

    /** @return array{quantity: int, allocatedTo: string, allocatedAt: string} */
    public function toArray(): array
    {
        return [
            'quantity' => $this->quantity,
            'allocatedTo' => $this->allocatedTo,
            'allocatedAt' => $this->allocatedAt->format(DATE_ATOM),
        ];
    }

    private function generateHash(): string
    {
        return hash(
            'sha256',
            sprintf('%d:%s:%d', $this->quantity, $this->allocatedTo, $this->allocatedAt->getTimestamp()),
        );
    }
}
