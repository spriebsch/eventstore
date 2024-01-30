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
    public function createEventForTopic(string $topic, Json $json): Event
    {
        return ($this->findEventClassForTopic($topic))::fromJson($json);
    }

    private function findEventClassForTopic(string $topic): string
    {
        foreach (get_declared_classes() as $class) {
            if ($this->classDoesNotImplementEventInterface($class)) {
                continue;
            }

            // @todo that class needs to have the method, not just the base class?

            if ($class::topic() === $topic) {
                return $class;
            }
        }

        throw new NoEventWithThatTopicException($topic);
    }

    private function classDoesNotImplementEventInterface(string $class): bool
    {
        return !in_array(Event::class, (class_implements($class)), true);
    }
}
