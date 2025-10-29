<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Domain\Projection\Raffle\V1;

use DateTimeInterface;

use function in_array;

final readonly class RaffleQuery
{
    private const ?int DEFAULT_LIMIT = null;
    private const int DEFAULT_OFFSET = 0;
    private const ?string DEFAULT_SORT_FIELD = null;
    private const string DEFAULT_SORT_ORDER = 'ASC';
    private const array ALLOWED_SORT_ORDERS = ['ASC', 'DESC'];

    public function __construct(
        public ?string $name = null,
        public ?string $prize = null,
        public ?string $status = null,
        public ?DateTimeInterface $startAt = null,
        public ?DateTimeInterface $closeAt = null,
        public ?DateTimeInterface $drawAt = null,
        public ?int $limit = self::DEFAULT_LIMIT,
        public int $offset = self::DEFAULT_OFFSET,
        public ?string $sortField = self::DEFAULT_SORT_FIELD,
        public string $sortOrder = self::DEFAULT_SORT_ORDER,
    ) {
    }

    public function withName(string $name): self
    {
        return new self(
            name: $name,
            prize: $this->prize,
            status: $this->status,
            startAt: $this->startAt,
            closeAt: $this->closeAt,
            drawAt: $this->drawAt,
            limit: $this->limit,
            offset: $this->offset,
            sortField: $this->sortField,
            sortOrder: $this->sortOrder,
        );
    }

    public function withPrize(string $prize): self
    {
        return new self(
            name: $this->name,
            prize: $prize,
            status: $this->status,
            startAt: $this->startAt,
            closeAt: $this->closeAt,
            drawAt: $this->drawAt,
            limit: $this->limit,
            offset: $this->offset,
            sortField: $this->sortField,
            sortOrder: $this->sortOrder,
        );
    }

    public function withStatus(string $status): self
    {
        return new self(
            name: $this->name,
            prize: $this->prize,
            status: $status,
            startAt: $this->startAt,
            closeAt: $this->closeAt,
            drawAt: $this->drawAt,
            limit: $this->limit,
            offset: $this->offset,
            sortField: $this->sortField,
            sortOrder: $this->sortOrder,
        );
    }

    public function withStartAt(DateTimeInterface $startAt): self
    {
        return new self(
            name: $this->name,
            prize: $this->prize,
            status: $this->status,
            startAt: $startAt,
            closeAt: $this->closeAt,
            drawAt: $this->drawAt,
            limit: $this->limit,
            offset: $this->offset,
            sortField: $this->sortField,
            sortOrder: $this->sortOrder,
        );
    }

    public function withCloseAt(DateTimeInterface $closeAt): self
    {
        return new self(
            name: $this->name,
            prize: $this->prize,
            status: $this->status,
            startAt: $this->startAt,
            closeAt: $closeAt,
            drawAt: $this->drawAt,
            limit: $this->limit,
            offset: $this->offset,
            sortField: $this->sortField,
            sortOrder: $this->sortOrder,
        );
    }

    public function withDrawAt(DateTimeInterface $drawAt): self
    {
        return new self(
            name: $this->name,
            prize: $this->prize,
            status: $this->status,
            startAt: $this->startAt,
            closeAt: $this->closeAt,
            drawAt: $drawAt,
            limit: $this->limit,
            offset: $this->offset,
            sortField: $this->sortField,
            sortOrder: $this->sortOrder,
        );
    }

    public function paginate(int $limit, int $offset = self::DEFAULT_OFFSET): self
    {
        return new self(
            name: $this->name,
            prize: $this->prize,
            status: $this->status,
            startAt: $this->startAt,
            closeAt: $this->closeAt,
            drawAt: $this->drawAt,
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
            name: $this->name,
            prize: $this->prize,
            status: $this->status,
            startAt: $this->startAt,
            closeAt: $this->closeAt,
            drawAt: $this->drawAt,
            limit: $this->limit,
            offset: $this->offset,
            sortField: $sortField,
            sortOrder: $sortOrder,
        );
    }
}
