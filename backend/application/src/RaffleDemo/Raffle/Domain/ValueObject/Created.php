<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Domain\ValueObject;

use App\Foundation\Clock\Clock;
use App\RaffleDemo\Raffle\Domain\Exception\InvalidCreatedException;
use DateTimeInterface;

final readonly class Created
{
    private function __construct(
        public string $by,
        public DateTimeInterface $at,
    ) {
        if ($this->by === '') {
            throw InvalidCreatedException::fromEmptyBy();
        }
    }

    public static function from(string $by, DateTimeInterface $at): self
    {
        return new self($by, $at);
    }

    /** @param array{by: ?string, at: ?string} $array */
    public static function fromArray(array $array): self
    {
        return new self($array['by'] ?? '', Clock::fromString($array['at'] ?? 'INVALID'));
    }

    /** @return array{by: string, at: string} */
    public function toArray(): array
    {
        return [
            'by' => $this->by,
            'at' => $this->at->format(DATE_ATOM),
        ];
    }
}
