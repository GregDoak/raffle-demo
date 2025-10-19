<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Domain\ValueObject;

use App\Foundation\Clock\Clock;
use App\RaffleDemo\Raffle\Domain\Exception\InvalidEndedException;
use DateTimeInterface;

final readonly class Ended
{
    private function __construct(
        public string $by,
        public DateTimeInterface $at,
        public string $reason,
    ) {
        if ($this->by === '') {
            throw InvalidEndedException::fromEmptyBy();
        }

        if ($this->reason === '') {
            throw InvalidEndedException::fromEmptyReason();
        }
    }

    public static function from(string $by, DateTimeInterface $at, string $reason): self
    {
        return new self($by, $at, $reason);
    }

    /** @param array{by: ?string, at: ?string, reason: ?string} $array */
    public static function fromArray(array $array): self
    {
        return new self($array['by'] ?? '', Clock::fromString($array['at'] ?? 'INVALID'), $array['reason'] ?? '');
    }

    /** @return array{by: string, at: string, reason: string} */
    public function toArray(): array
    {
        return [
            'by' => $this->by,
            'at' => $this->at->format(DATE_ATOM),
            'reason' => $this->reason,
        ];
    }
}
