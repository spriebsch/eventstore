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

use PHPUnit\Framework\TestCase;
use spriebsch\eventstore\tests\TestEvent;

/**
 * @covers \spriebsch\eventstore\EmitsEventsTrait
 * @uses   \spriebsch\eventstore\Events
 * @uses   \spriebsch\eventstore\EventId
 */
class EmitsEventsTraitTest extends TestCase
{
    public function test_emits_events(): void
    {
        $event1 = TestEvent::generate();
        $event2 = TestEvent::generate();

        $emitter = new class() {
            use EmitsEventsTrait;

            public function emitEvents(Event ...$events): void
            {
                foreach ($events as $event) {
                    $this->emit($event);
                }
            }
        };

        $emitter->emitEvents($event1, $event2);

        $events = iterator_to_array($emitter->events());

        $this->assertCount(2, $events);
        $this->assertSame($event1, $events[0]);
        $this->assertSame($event2, $events[1]);
    }
}
