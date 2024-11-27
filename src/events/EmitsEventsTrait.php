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
    private array $newEvents = [];

    private function emit(Event $event): void
    {
        $this->newEvents[] = $event;
    }

    public function newEvents(): Events
    {
        $events = Events::from(...$this->newEvents);

        $this->newEvents = [];

        return $events;
    }
}
