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
use spriebsch\uuid\UUID;

/**
 * @covers \spriebsch\eventstore\AbstractEventStream
 * @covers \spriebsch\eventstore\EventStreamHasNotBeenQueriedException
 * @uses \spriebsch\eventstore\EventId
 * @uses \spriebsch\eventstore\EventTrait
 * @uses \spriebsch\eventstore\Events
 */
class EventStreamTest extends TestCase
{
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
