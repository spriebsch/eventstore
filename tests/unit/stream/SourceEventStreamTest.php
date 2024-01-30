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
use spriebsch\timestamp\Timestamp;

/**
 * @covers \spriebsch\eventstore\AbstractEventStream
 * @covers \spriebsch\eventstore\EventStreamHasNotBeenQueriedException
 * @uses   \spriebsch\eventstore\EventId
 * @uses   \spriebsch\eventstore\EventTrait
 * @uses   \spriebsch\eventstore\Events
 */
class SourceEventStreamTest extends TestCase
{
    /**
     * @group feature
     */
    public function test_sources_events_with_specified_topics(): void
    {
        $eventId = EventId::generate();
        $events = Events::from(
            TestEvent::from(
                $eventId,
                TestCorrelationId::generate(),
                Timestamp::generate(),
                'the-payload'
            )
        );

        $reader = $this->createMock(EventReader::class);
        $reader
            ->expects($this->once())
            ->method('source')
            ->with($events->lastEventId(), null, null, 'the-topic-1', 'the-topic-2')
            ->willReturn($events);

        $stream = $this->stream($reader);

        $stream->source($eventId);
    }

    /**
     * @group feature
     */
    public function test_sources_events_with_specified_topics_with_limit(): void
    {
        $limit = 3;
        $eventId = EventId::generate();
        $events = Events::from(
            TestEvent::from(
                $eventId,
                TestCorrelationId::generate(),
                Timestamp::generate(),
                'the-payload'
            )
        );

        $reader = $this->createMock(EventReader::class);
        $reader
            ->expects($this->once())
            ->method('source')
            ->with($events->lastEventId(), $limit, null, 'the-topic-1', 'the-topic-2')
            ->willReturn($events);

        $stream = $this->stream($reader);
        $stream->limitNextQuery($limit);

        $stream->source($eventId);
    }

    /**
     * @group feature
     */
    public function test_sources_events_with_specified_topics_for_correlation_id(): void
    {
        $correlationId = TestCorrelationId::generate();
        $events = Events::from(
            TestEvent::from(
                EventId::generate(),
                $correlationId,
                Timestamp::generate(),
                'the-payload'
            )
        );

        $reader = $this->createMock(EventReader::class);
        $reader
            ->expects($this->once())
            ->method('source')
            ->with(
                $events->lastEventId(),
                null,
                $correlationId,
                'the-topic-1',
                'the-topic-2'
            )
            ->willReturn($events);

        $stream = $this->stream($reader);

        $stream->source($events->lastEventId(), $correlationId);
    }

    /**
     * @group feature
     */
    public function test_sources_events_with_specified_topics_for_correlation_id_with_limit(): void
    {
        $limit = 3;
        $correlationId = TestCorrelationId::generate();

        $events = Events::from(
            TestEvent::from(
                EventId::generate(),
                $correlationId,
                Timestamp::generate(),
                'the-payload'
            )
        );

        $reader = $this->createMock(EventReader::class);
        $reader
            ->expects($this->once())
            ->method('source')
            ->with($events->lastEventId(), $limit, $correlationId, 'the-topic-1', 'the-topic-2')
            ->willReturn($events);

        $stream = $this->stream($reader);
        $stream->limitNextQuery($limit);

        $stream->source($events->lastEventId(), $correlationId);
    }

    /**
     * @group feature
     */
    public function test_keeps_id_of_last_event(): void
    {
        $event1 = TestEvent::generate();
        $event2 = TestEvent::generate();
        $events = Events::from($event1, $event2);

        $reader = $this->createMock(EventReader::class);
        $reader
            ->expects($this->once())
            ->method('source')
            ->willReturn($events);

        $stream = $this->stream($reader);

        $eventsArray = iterator_to_array($stream->source($event1->id()));
        $lastId = end($eventsArray)->id();

        $this->assertSame($lastId->asString(), $stream->lastEvent()->asString());
    }

    /**
     * @group exception
     */
    public function test_read_last_event_id_when_events_were_not_retrieved(): void
    {
        $reader = $this->createMock(EventReader::class);
        $stream = $this->stream($reader);

        $this->assertNull($stream->lastEvent());
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
