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

abstract class AbstractEventStream implements EventStream
{
    private ?int     $limitForNextQuery = null;
    private ?EventId $lastEventId       = null;

    final public function __construct(private readonly EventReader $eventReader) {}

    final public function limitNextQuery(int $limit): void
    {
        $this->limitForNextQuery = $limit;
    }

    final public function source(
        EventId       $position,
        ?CorrelationId $correlationId = null
    ): Events
    {
        return $this->query(
            fn() => $this->eventReader->source(
                   $position,
                   $this->limitForNextQuery,
                   $correlationId,
                ...$this->topics()
            )
        );
    }

    final public function queued(
        ?EventId      $position,
        ?CorrelationId $correlationId = null
    ): Events
    {
        return $this->query(
            fn() => $this->eventReader->queued(
                   $position,
                   $this->limitForNextQuery,
                   $correlationId,
                ...$this->topics()
            )
        );
    }

    final public function all(
        ?CorrelationId $correlationId = null
    ): Events
    {
        return $this->query(
            fn() => $this->eventReader->queued(
                   null,
                   $this->limitForNextQuery,
                   $correlationId,
                ...$this->topics()
            )
        );
    }

    final public function lastEvent(): ?EventId
    {
        return $this->lastEventId;
    }

    private function query(callable $query): Events
    {
        $this->lastEventId = null;

        $result = $query();

        $this->limitForNextQuery = null;

        if (count($result) !== 0) {
            $this->lastEventId = $result->lastEventId();
        }

        return $result;
    }

    abstract protected function topics(): array;
}
