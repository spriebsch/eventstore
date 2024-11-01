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

class EventFactory
{
    private static ?array $eventMap = null;

    public static function configureWith(array $eventMap): void
    {
        self::$eventMap = $eventMap;
    }

    public static function reset(): void
    {
        self::$eventMap = null;
    }

    public function createEventForTopic(string $topic, Json $json): Event
    {
        return ($this->findEventClassForTopic($topic))::fromJson($json);
    }

    private function findEventClassForTopic(string $topic): string
    {
        if ($this->eventMapIsNotConfigured()) {
            throw new EventFactoryNotConfiguredException;
        }

        if ($this->eventMapDoesNotContainTopic($topic)) {
            throw new NoEventWithThatTopicException($topic);
        }

        $class = self::$eventMap[$topic];

        if ($this->classDoesNotImplementEventInterface($class)) {
            throw new ClassDoesNotImplementEventInterfaceException($class, $topic);
        }

        return $class;
    }

    private function eventMapIsNotConfigured(): bool
    {
        return self::$eventMap === null;
    }

    private function eventMapDoesNotContainTopic(string $topic): bool
    {
        return !isset(self::$eventMap[$topic]);
    }

    private function classDoesNotImplementEventInterface(string $class): bool
    {
        return !in_array(Event::class, (class_implements($class)), true);
    }
}
