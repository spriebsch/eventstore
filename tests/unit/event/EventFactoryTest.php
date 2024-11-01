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
use spriebsch\timestamp\Timestamp;
use spriebsch\uuid\UUID;
use stdClass;
use const JSON_THROW_ON_ERROR;

#[CoversClass(EventFactory::class)]
#[CoversClass(NoEventWithThatTopicException::class)]
#[CoversClass(EventFactoryNotConfiguredException::class)]
#[CoversClass(ClassDoesNotImplementEventInterfaceException::class)]
#[UsesClass(EventId::class)]
#[UsesClass(EventTrait::class)]
#[UsesClass(Json::class)]
#[UsesClass(SerializableEventTrait::class)]
#[UsesClass(UUID::class)]
class EventFactoryTest extends TestCase
{
    public function test_exception_when_not_configured(): void
    {
        $eventFactory = new EventFactory;

        $this->expectException(EventFactoryNotConfiguredException::class);

        $eventFactory->createEventForTopic(
            'the-topic',
            Json::from(json_encode([]))
        );
    }

    public function test_exception_on_unknown_topic(): void
    {
        EventFactory::configureWith([]);
        $eventFactory = new EventFactory;

        $this->expectException(NoEventWithThatTopicException::class);

        $eventFactory->createEventForTopic(
            'unknown-topic',
            Json::from(json_encode([]))
        );
    }

    public function test_exception_when_class_is_no_event(): void
    {
        $topic = 'the-topic';

        EventFactory::configureWith([$topic => stdClass::class]);
        $eventFactory = new EventFactory;

        $this->expectException(ClassDoesNotImplementEventInterfaceException::class);

        $eventFactory->createEventForTopic(
            $topic,
            Json::from(json_encode([]))
        );
    }

    public function test_create_event(): void
    {
        $topic = TestEvent::topic();
        $class = TestEvent::class;

        $id = EventId::generate();
        $correlationId = TestCorrelationId::generate();
        $timestamp = Timestamp::generate();

        EventFactory::configureWith([$topic => TestEvent::class]);
        $eventFactory = new EventFactory;
        $event = $eventFactory->createEventForTopic(
            $topic,
            Json::from(
                json_encode(
                    TestEvent::from(
                        $id,
                        $correlationId,
                        $timestamp,
                        'the-payload'
                    ),
                    JSON_THROW_ON_ERROR
                )
            )
        );

        $this->assertInstanceOf($class, $event);
        $this->assertEquals($topic, $event::topic());
        $this->assertEquals($id->asString(), $event->id()->asString());
        $this->assertEquals(
            $correlationId->asString(),
            $event->correlationId()->asUUID()->asString()
        );
        $this->assertEquals(
            $timestamp->asString(),
            $event->timestamp()->asString()
        );
    }

    protected function tearDown(): void
    {
        EventFactory::reset();
    }
}
