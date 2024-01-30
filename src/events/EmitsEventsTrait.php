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

trait EmitsEventsTrait
{
    private array $events = [];

    private function emit(Event $event): void
    {
        $this->events[] = $event;
    }

    public function events(): Events
    {
        $events = Events::from(...$this->events);

        $this->events = [];

        return $events;
    }
}
