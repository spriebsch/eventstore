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

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use spriebsch\eventstore\tests\TestEvent;

#[CoversClass(EmitsEventsTrait::class)]
#[UsesClass(EventId::class)]
#[UsesClass(Events::class)]
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

        $events = iterator_to_array($emitter->newEvents());

        $this->assertCount(2, $events);
        $this->assertSame($event1, $events[0]);
        $this->assertSame($event2, $events[1]);
    }
}
