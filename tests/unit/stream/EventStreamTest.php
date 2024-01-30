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

#[CoversClass(AbstractEventStream::class)]
#[CoversClass(EventStreamHasNotBeenQueriedException::class)]
#[UsesClass(EventId::class)]
#[UsesClass(EventTrait::class)]
#[UsesClass(Events::class)]
class EventStreamTest extends TestCase
{
    #[Group('exception')]
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
}
