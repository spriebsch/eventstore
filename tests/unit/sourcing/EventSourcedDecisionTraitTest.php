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
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use spriebsch\eventstore\tests\TestSourcingEvent;

#[CoversClass(EventSourcedDecisionTrait::class)]
#[CoversClass(EventSourcedTrait::class)]
#[UsesClass(EventId::class)]
#[UsesClass(EventSourcedTrait::class)]
#[UsesClass(Events::class)]
#[UsesClass(EventTrait::class)]
#[UsesClass(EmitsEventsTrait::class)]
class EventSourcedDecisionTraitTest extends TestCase
{
    #[Group('feature')]
    public function test_can_be_created(): void
    {
        $id = TestCorrelationId::generate();
        $foo = 'the-foo';
        $bar = ['the-bar'];

        $decision = TestDecision::from($id, $foo, $bar);

        $this->assertSame($foo, $decision->foo());
        $this->assertSame($bar, $decision->bar());
    }

    #[Group('feature')]
    public function test_last_event_id_can_be_retrieved(): void
    {
        $id = TestCorrelationId::generate();
        $event = TestSourcingEvent::from($id);

        $sourced = TestDecision::sourceFrom(Events::from($event));

        $this->assertSame($event->id()->asString(), $sourced->lastEventId()->asString());
    }

    #[Group('feature')]
    public function test_last_event_id_can_be_retrieved_after_state_change(): void
    {
        $id = TestCorrelationId::generate();
        $event1 = TestSourcingEvent::from($id);
        $event2 = TestSourcingEvent::from($id);

        $sourced = TestDecision::sourceFrom(Events::from($event1));
        $sourced->changeState($event2);

        $this->assertSame($event1->id()->asString(), $sourced->lastSourcedEventId()->asString());
        $this->assertSame($event2->id()->asString(), $sourced->lastEventId()->asString());
    }

    #[Group('feature')]
    public function test_can_be_sourced(): void
    {
        $id = TestCorrelationId::generate();
        $event = TestSourcingEvent::from($id);

        $decision = TestDecision::sourceFrom(Events::from($event));

        $this->assertSame($event, $decision->event());
    }
}
