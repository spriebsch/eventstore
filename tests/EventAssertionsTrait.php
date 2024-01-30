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

use PHPUnit\Framework\Constraint\IsEqual;
use PHPUnit\Framework\Constraint\TraversableContainsEqual;

trait EventAssertionsTrait
{
    private function assertContainsNumberOfEvents(
        int    $number,
        string $eventClass,
        Events $events
    ): void
    {
        $this->ensureClassExists($eventClass);
        $this->ensureClassIsEvent($eventClass);

        $types = $this->toEventTypes($events);

        $this->assertThat(
            $types,
            new TraversableContainsEqual($eventClass)
        );

        $counts = array_count_values($types);

        $this->assertThat(
            $number,
            new IsEqual($counts[$eventClass])
        );
    }

    private function assertContainsEvent(string $eventClass, Events $events): void
    {
        $this->ensureClassExists($eventClass);
        $this->ensureClassIsEvent($eventClass);

        $types = $this->toEventTypes($events);

        $this->assertThat(
            $types,
            new TraversableContainsEqual($eventClass)
        );
    }

    private function retrieve(string $eventClass, Events $events): Event
    {
        $this->ensureClassExists($eventClass);

        $types = $this->toEventTypes($events);
        $position = array_search($eventClass, $types);

        return $events->asArray()[$position];
    }

    private function retrieveLast(string $eventClass, Events $events): Event
    {
        $this->ensureClassExists($eventClass);

        $types = $this->toEventTypes($events);
        $position = array_search($eventClass, array_reverse($types));

        return array_reverse($events->asArray())[$position];
    }

    private function toEventTypes(Events $events): array
    {
        return array_map(
            fn(Event $event) => get_class($event),
            $events->asArray()
        );
    }

    private function ensureClassExists(string $eventClass): void
    {
        if (!class_exists($eventClass)) {
            $this->fail(sprintf('Class "%s" does not exist', $eventClass));
        }
    }

    private function ensureClassIsEvent(string $eventClass): void
    {
        if (!in_array(Event::class, class_implements($eventClass))) {
            $this->fail(sprintf('Class "%s" is no event', $eventClass));
        }
    }
}