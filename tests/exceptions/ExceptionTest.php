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

/**
 * @covers \spriebsch\eventstore\Exception
 * @covers \spriebsch\eventstore\EmptyEventsCollectionException
 * @covers \spriebsch\eventstore\EventHasNoTopicConstantException
 * @covers \spriebsch\eventstore\EventStreamHasNotBeenQueriedException
 * @covers \spriebsch\eventstore\FailedToStoreEventException
 * @covers \spriebsch\eventstore\FailedToStoreEventForUnknownReasonException
 * @covers \spriebsch\eventstore\KeyNotFoundInJsonException
 * @covers \spriebsch\eventstore\NoEventWithThatIdException
 * @covers \spriebsch\eventstore\NoEventWithThatTopicException
 * @covers \spriebsch\eventstore\NoSuchSinceEventIdException
 * @covers \spriebsch\eventstore\EventId
 */
class ExceptionTest extends TestCase
{
    public function test_EmptyEventsCollectionException(): void
    {
        $exception = new EmptyEventsCollectionException;

        $this->assertStringContainsString(
            'No events in the collection, cannot retrieve lastEventId',
            $exception->getMessage()
        );
    }

    public function test_EventStreamHasNotBeenQueriedException(): void
    {
        $eventStream = $this->createMock(EventStream::class);

        $exception = new EventStreamHasNotBeenQueriedException($eventStream);
    
        $this->assertStringContainsString(
            'must be queried before calling lastEventId()',
            $exception->getMessage()
        );
    }


    public function test_NoEventWithThatIdException(): void
    {
        $id = EventId::generate();

        $exception = new NoEventWithThatIdException($id);

        $this->assertStringContainsString(
            'because that event id does not exist',
            $exception->getMessage()
        );
    }

    public function test_EventHasNoTopicConstantException(): void
    {
        $exception = new EventHasNoTopicConstantException('the-class');

        $this->assertStringContainsString(
            'Event the-class must have a topic constant',
            $exception->getMessage()
        );
    }

    public function test_FailedToStoreEventException(): void
    {
        $id = EventId::generate();
        $previous = new Exception('the-message');
        $exception = new FailedToStoreEventException($id, $previous);

        $this->assertStringContainsString(
            sprintf(
                'Failed to store event %s: %s',
                $id->asString(),
                $previous->getMessage()
            ),
            $exception->getMessage()
        );
    }

    public function test_FailedToStoreEventForUnknownReasonException(): void
    {
        $id = EventId::generate();
        $exception = new FailedToStoreEventForUnknownReasonException($id);

        $this->assertStringContainsString(
            sprintf(
                'Failed to store event %s for unknown reasons',
                $id->asString()
            ),
            $exception->getMessage()
        );
    }

    public function test_KeyNotFoundInJsonException(): void
    {
        $key = 'the-key';
        $exception = new KeyNotFoundInJsonException($key);

        $this->assertStringContainsString(
            'Key the-key not found in JSON document',
            $exception->getMessage()
        );
    }

    public function test_NoEventWithThatTopicException(): void
    {
        $topic = 'the-topic';
        $exception = new NoEventWithThatTopicException($topic);

        $this->assertStringContainsString(
            'There is no event with topic the-topic',
            $exception->getMessage()
        );
    }

    public function test_NoSuchSinceEventIdException(): void
    {
        $id = EventId::generate();
        $exception = new NoSuchSinceEventIdException($id);

        $this->assertStringContainsString(
            sprintf(
                'Failed to load events since %s because that event id does not exist',
                $id->asString()
            ),
            $exception->getMessage()
        );
    }
}
