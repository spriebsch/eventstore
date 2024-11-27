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
    private Events $sourcedEvents;

    public static function sourceFrom(Events $events): static
    {
        return new static($events);
    }

    public function lastEventId(): EventId
    {
        if (!isset($this->newEvents)) {
            return $this->lastSourcedEventId();
        }

        $count = count($this->newEvents);

        if ($count === 0) {
            return $this->lastSourcedEventId();
        }

        return $this->newEvents[$count - 1]->id();
    }

    public function lastSourcedEventId(): EventId
    {
        return $this->sourcedEvents->lastEventId();
    }

    private function reconstituteFrom(Events $events): void
    {
        $this->sourcedEvents = $events;

        foreach ($events as $event) {
            $this->apply($event);
        }
    }

    private function apply(Event $event): void
    {
        if (!isset($this->id)) {
            $this->id = $event->correlationId();
        }

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

    private function ensureCorrelationIdDoesNotChange(CorrelationId $correlationId): void
    {
        if ($correlationId->asUUID()->asString() !== $this->id->asString()) {
            throw new CorrelationIdHasChangedException($this->id, $correlationId);
        }
    }
}
