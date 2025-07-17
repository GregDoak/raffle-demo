<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Domain\ValueObject;

use App\Foundation\Clock\Clock;
use App\RaffleDemo\Raffle\Domain\Exception\InvalidDrawnException;
use DateTimeInterface;

final readonly class Drawn
{
    private function __construct(
        private string $by,
        private DateTimeInterface $at,
    ) {
        if ($this->by === '') {
            throw InvalidDrawnException::fromEmptyBy();
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
