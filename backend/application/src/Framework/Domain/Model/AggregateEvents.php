<?php

declare(strict_types=1);

namespace App\Framework\Domain\Model;

use App\Framework\Domain\Model\Event\AggregateEventInterface;
use ArrayIterator;
use IteratorAggregate;
use Traversable;

use function count;

/** @implements IteratorAggregate<AggregateEventInterface> */
final readonly class AggregateEvents implements AggregateEventsInterface, IteratorAggregate
{
    /** @var AggregateEventInterface[] */
    private array $events;

    private function __construct(
        AggregateEventInterface ...$events,
    ) {
        $this->events = $events;
    }

    public function add(AggregateEventInterface $event): self
    {
        return new self(...[...$this->events, $event]);
    }

    public static function fromNew(): self
    {
        return new self();
    }

    public function count(): int
    {
        return count($this->events);
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    /** @return AggregateEventInterface[] */
    public function toArray(): array
    {
        return $this->events;
    }

    /** @return Traversable<AggregateEventInterface> */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->events);
    }
}
