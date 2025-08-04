<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Domain\ValueObject;

use App\RaffleDemo\Raffle\Domain\Exception\InvalidTicketPriceException;

final readonly class TicketPrice
{
    private function __construct(
        public int $amount,
        public string $currency,
    ) {
        if ($this->amount < 0) {
            throw InvalidTicketPriceException::fromNegativeAmount();
        }

        if ($this->currency === '') {
            throw InvalidTicketPriceException::fromEmptyCurrency();
        }
    }

    public static function from(int $amount, string $currency): self
    {
        return new self($amount, $currency);
    }

    /**
     * @infection-ignore-all
     * @param array{amount: ?int, currency: ?string} $array
     */
    public static function fromArray(array $array): self
    {
        return new self(amount: $array['amount'] ?? 0, currency: $array['currency'] ?? '');
    }

    /** @return array{amount: int, currency: string} */
    public function toArray(): array
    {
        return [
            'amount' => $this->amount,
            'currency' => $this->currency,
        ];
    }
}
