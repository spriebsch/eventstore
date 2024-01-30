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
use spriebsch\eventstore\tests\TestEvent;
use spriebsch\timestamp\Timestamp;

#[CoversClass(AbstractEventStream::class)]
#[CoversClass(EventStreamHasNotBeenQueriedException::class)]
#[UsesClass(EventId::class)]
#[UsesClass(EventTrait::class)]
#[UsesClass(Events::class)]
class QueuedEventsEventStreamTest extends TestCase
{
    #[Group('feature')]
    public function test_retrieves_queued_events_with_specified_topics(): void
    {
        $events = Events::from(
            TestEvent::from(
                EventId::generate(),
                TestCorrelationId::generate(),
                Timestamp::generate(),
                'the-payload'
            )
        );

        $reader = $this->createMock(EventReader::class);
        $reader
            ->expects($this->once())
            ->method('queued')
            ->with(null, null, null, 'the-topic-1', 'the-topic-2')
            ->willReturn($events);

        $stream = $this->stream($reader);

        $stream->queued(null);
    }

    #[Group('feature')]
    public function test_retrieves_queued_events_with_specified_topics_with_limit(): void
    {
        $limit = 3;

        $events = Events::from(
            TestEvent::from(
                EventId::generate(),
                TestCorrelationId::generate(),
                Timestamp::generate(),
                'the-payload'
            )
        );

        $reader = $this->createMock(EventReader::class);
        $reader
            ->expects($this->once())
            ->method('queued')
            ->with($events->lastEventId(), $limit, null, 'the-topic-1', 'the-topic-2')
            ->willReturn($events);

        $stream = $this->stream($reader);
        $stream->limitNextQuery($limit);

        $stream->queued($events->lastEventId());
    }

    #[Group('feature')]
    public function test_retrieves_queued_events_with_specified_topics_for_correlation_id(): void
    {
        $correlationId = TestCorrelationId::generate();
        $sinceId = EventId::generate();
        $events = Events::from(
            TestEvent::from(
                $sinceId,
                $correlationId,
                Timestamp::generate(),
                'the-payload'
            )
        );

        $reader = $this->createMock(EventReader::class);
        $reader
            ->expects($this->once())
            ->method('queued')
            ->with($sinceId, null, $correlationId)
            ->willReturn($events);

        $stream = $this->stream($reader);

        $stream->queued($sinceId, $correlationId);
    }

    #[Group('feature')]
    public function test_keeps_id_of_last_event(): void
    {
        $events = $this->events();
        $sinceId = iterator_to_array($events)[0]->id();

        $reader = $this->createMock(EventReader::class);
        $reader
            ->expects($this->once())
            ->method('queued')
            ->with($sinceId, null, null)
            ->willReturn($events);

        $stream = $this->stream($reader);

        $stream->queued($sinceId);

        $eventsArray = iterator_to_array($events);
        $lastId = end($eventsArray)->id();

        $this->assertSame($lastId->asString(), $stream->lastEvent()->asString());
    }

    private function stream(EventReader $reader): EventStream
    {
        return new class($reader) extends AbstractEventStream {
            protected function topics(): array
            {
                return ['the-topic-1', 'the-topic-2'];
            }
        };
    }

    private function events(): Events
    {
        return Events::from(TestEvent::generate());
    }
}
