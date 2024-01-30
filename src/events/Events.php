<?php declare(strict_types=1);

/*
 * This file is part of EventStore.
 *
 * (c) Stefan Priebsch <stefan@priebsch.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spriebsch\eventstore;

use ArrayIterator;
use Countable;
use Iterator;
use IteratorAggregate;

class Events implements IteratorAggregate, Countable
{
    /**
     * @var Event[]
     */
    private array $events;

    public static function from(Event ...$events): self
    {
        return new self(...$events);
    }

    public static function fromJsonEvents(JsonEvent ...$events): self
    {
        $factory = new EventFactory;

        return self::from(
            ...array_map(
                   fn(JsonEvent $jsonEvent) => $factory->createEventForTopic($jsonEvent->topic(), $jsonEvent->json()),
                   $events
               )
        );
    }

    private function __construct(Event ...$events)
    {
        $this->events = $events;
    }

    public function count(): int
    {
        return count($this->events);
    }

    public function lastEventId(): EventId
    {
        $key = array_key_last($this->events);

        if ($key === null) {
            throw new EmptyEventsCollectionException();
        }

        return $this->events[$key]->id();
    }

    public function asArray(): array
    {
        return $this->events;
    }

    public function getIterator(): Iterator
    {
        return new ArrayIterator($this->events);
    }
}
