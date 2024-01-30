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

trait EventSourcedTrait
{
    private ?EventId $sourcedEventId = null;

    public static function sourceFrom(Events $events): self
    {
        return new self($events);
    }

    public function lastEventId(): EventId
    {
        $count = count($this->events);

        if ($count === 0) {
            return $this->lastSourcedEventId();
        }

        return $this->events[$count - 1]->id();
    }

    public function lastSourcedEventId(): EventId
    {
        return $this->sourcedEventId;
    }

    private function reconstituteFrom(Events $events): void
    {
        foreach ($events as $event) {
            $this->apply($event);
            $this->sourcedEventId = $event->id();
        }
    }

    private function apply(Event $event): void
    {
        $method = $this->determineApplyMethodNameFor($event);

        $this->ensureCorrelationIdDoesNotChange($event->correlationId());

        $this->$method($event);
    }

    private function determineApplyMethodNameFor(Event $event): string
    {
        $parts = explode('\\', $event::class);
        $basename = array_slice($parts, -1)[0];

        return 'apply' . $basename;
    }

    private function ensureCorrelationIdDoesNotChange(CorrelationId $correlationId)
    {
        if ($this->id === null) {
            return;
        }

        if ($correlationId->asUUID()->asString() !== $this->id->asString()) {
            throw new \RuntimeException('Correlation ID mismatch');
        }
    }
}
