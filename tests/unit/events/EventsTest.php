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
use spriebsch\uuid\UUID;

#[CoversClass(Events::class)]
#[CoversClass(EmptyEventsCollectionException::class)]
#[CoversClass(NoEventWithThatIdException::class)]
#[UsesClass(NoEventWithThatTopicException::class)]
#[UsesClass(EventId::class)]
#[UsesClass(EventTrait::class)]
#[UsesClass(EventFactory::class)]
#[UsesClass(Json::class)]
#[UsesClass(JsonEvent::class)]
#[UsesClass(UUID::class)]
class EventsTest extends TestCase
{
    #[Group('feature')]
    public function test_can_be_created_from_json_events(): void
    {
        $id1 = EventId::generate();
        $id2 = EventId::generate();

        $jsonEvents = [
            JsonEvent::from(
                TestEvent::topic(),
                Json::from(
                    json_encode(
                        [
                            'id'            => $id1->asString(),
                            'correlationId' => TestCorrelationId::generate()->asString(),
                            'timestamp'     => '2023-05-11T09:16:06+02:00.295105',
                            'payload'       => 'the-payload'
                        ]
                    )
                )
            ),
            JsonEvent::from(
                TestEvent::topic(),
                Json::from(
                    json_encode(
                        [
                            'id'            => $id2->asString(),
                            'correlationId' => TestCorrelationId::generate()->asString(),
                            'timestamp'     => '2023-05-11T09:16:06+02:00.295105',
                            'payload'       => 'the-payload'
                        ]
                    )
                )
            ),
        ];

        $events = Events::fromJsonEvents(...$jsonEvents)->asArray();

        $this->assertCount(2, $events);

        $this->assertEquals($id1->asString(), $events[0]->id()->asString());
        $this->assertEquals($id2->asString(), $events[1]->id()->asString());
    }

    #[Group('feature')]
    public function test_last_event_id(): void
    {
        $events = $this->collectionWithTwoEvents();
        $id = iterator_to_array($events)[1]->id();

        $this->assertEquals($id->asString(), $events->lastEventId()->asString());
    }

    #[Group('feature')]
    public function test_retrieve_id_of_last_event_when_empty(): void
    {
        $events = Events::from();

        $this->expectException(EmptyEventsCollectionException::class);
        $this->expectExceptionMessage('cannot retrieve');

        $events->lastEventId();
    }

    #[Group('feature')]
    public function test_can_be_counted(): void
    {
        $this->assertCount(2, $this->collectionWithTwoEvents());
    }

    #[Group('feature')]
    public function test_can_be_iterated_over(): void
    {
        $this->assertCount(2, iterator_to_array($this->collectionWithTwoEvents(), true));
    }

    private function collectionWithTwoEvents(): Events
    {
        return Events::from(
            TestEvent::generate(),
            TestEvent::generate()
        );
    }
}
