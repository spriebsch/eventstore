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

#[CoversClass(EventSourcedReadModelTrait::class)]
#[CoversClass(EventSourcedTrait::class)]
#[CoversClass(CorrelationIdHasChangedException::class)]
#[CoversClass(NoEventsToSourceFromException::class)]
#[UsesClass(EventId::class)]
#[UsesClass(Events::class)]
#[UsesClass(EventTrait::class)]
class EventSourcedReadModelTraitTest extends TestCase
{
    public function test_can_be_sourced(): void
    {
        $id = TestCorrelationId::generate();
        $event = TestSourcingEvent::from($id);

        $sourced = TestReadModel::sourceFrom(Events::from($event));

        $this->assertSame($event, $sourced->event());
    }

    public function test_events_can_be_applied(): void
    {
        $id = TestCorrelationId::generate();
        $event1 = TestSourcingEvent::from($id);
        $event2 = TestSourcingEvent::from($id);

        $sourced = TestReadModel::sourceFrom(Events::from($event1));
        $sourced->apply($event2);

        $this->assertSame($event2, $sourced->event());
    }

    public function test_last_sourced_event_id_can_be_retrieved(): void
    {
        $id = TestCorrelationId::generate();
        $event = TestSourcingEvent::from($id);

        $sourced = TestReadModel::sourceFrom(Events::from($event));

        $this->assertSame($event->id()->asString(), $sourced->lastSourcedEventId()->asString());
    }

    #[Group('exception')]
    public function test_CorrelationId_cannot_change(): void
    {
        $event1 = TestSourcingEvent::from(TestCorrelationId::generate());
        $event2 = TestSourcingEvent::from(TestCorrelationId::generate());

        $this->expectException(CorrelationIdHasChangedException::class);

        TestReadModel::sourceFrom(Events::from($event1, $event2));
    }

    #[Group('exception')]
    public function test_CorrelationId_cannot_change2(): void
    {
        $event1 = TestSourcingEvent::from(TestCorrelationId::generate());
        $event2 = TestSourcingEvent::from(TestCorrelationId::generate());

        $readModel = TestReadModel::sourceFrom(Events::from($event1));

        $this->expectException(CorrelationIdHasChangedException::class);

        $readModel->apply($event2);
    }
}
