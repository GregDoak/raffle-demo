<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Domain\Projection\RaffleWinner\V1;

use DateTimeInterface;

use function in_array;

final readonly class RaffleWinnerQuery
{
    private const ?int DEFAULT_LIMIT = null;
    private const int DEFAULT_OFFSET = 0;
    private const ?string DEFAULT_SORT_FIELD = null;
    private const string DEFAULT_SORT_ORDER = 'ASC';
    private const array ALLOWED_SORT_ORDERS = ['ASC', 'DESC'];

    public function __construct(
        public ?string $raffleId = null,
        public ?DateTimeInterface $drawnAt = null,
        public ?int $limit = self::DEFAULT_LIMIT,
        public int $offset = self::DEFAULT_OFFSET,
        public ?string $sortField = self::DEFAULT_SORT_FIELD,
        public string $sortOrder = self::DEFAULT_SORT_ORDER,
    ) {
    }

    public function withRaffleId(string $raffleId): self
    {
        return new self(
            raffleId: $raffleId,
            drawnAt: $this->drawnAt,
            limit: $this->limit,
            offset: $this->offset,
            sortField: $this->sortField,
            sortOrder: $this->sortOrder,
        );
    }

    public function withDrawnAt(DateTimeInterface $drawnAt): self
    {
        return new self(
            raffleId: $this->raffleId,
            drawnAt: $drawnAt,
            limit: $this->limit,
            offset: $this->offset,
            sortField: $this->sortField,
            sortOrder: $this->sortOrder,
        );
    }

    public function paginate(int $limit, int $offset = self::DEFAULT_OFFSET): self
    {
        return new self(
            raffleId: $this->raffleId,
            drawnAt: $this->drawnAt,
            limit: $limit,
            offset: $offset,
            sortField: $this->sortField,
            sortOrder: $this->sortOrder,
        );
    }

    public function sortBy(string $sortField, string $sortOrder = self::DEFAULT_SORT_ORDER): self
    {
        if (property_exists($this, $sortField) === false) {
            return $this;
        }

        if (in_array($sortOrder, self::ALLOWED_SORT_ORDERS, true) === false) {
            return $this;
        }

        return new self(
            raffleId: $this->raffleId,
            drawnAt: $this->drawnAt,
            limit: $this->limit,
            offset: $this->offset,
            sortField: $sortField,
            sortOrder: $sortOrder,
        );
    }
}
