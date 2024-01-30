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

use Error;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use spriebsch\eventstore\tests\TestEvent;
use spriebsch\timestamp\Timestamp;

#[CoversClass(SerializableEventTrait::class)]
#[CoversClass(EventTrait::class)]
#[UsesClass(EventId::class)]
class EventTest extends TestCase
{
    #[Group('feature')]
    public function test_has_correlation_id(): void
    {
        $id = EventId::generate();
        $correlationId = TestCorrelationId::generate();
        $timestamp = Timestamp::generate();

        $event = TestEvent::from(
            $id,
            $correlationId,
            $timestamp,
            'the-payload'
        );

        $this->assertSame(
            $correlationId->asString(),
            $event->correlationId()->asUUID()->asString()
        );
    }

    /**
     *
     */
    public function test_has_timestamp(): void
    {
        $id = EventId::generate();
        $correlationId = TestCorrelationId::generate();
        $timestamp = Timestamp::generate();

        $event = TestEvent::from(
            $id,
            $correlationId,
            $timestamp,
            'the-payload'
        );

        $this->assertSame($timestamp->asString(), $event->timestamp()->asString());
    }

    #[Group('feature')]
    public function test_has_topic(): void
    {
        $id = EventId::generate();
        $correlationId = TestCorrelationId::generate();
        $timestamp = Timestamp::generate();

        $event = TestEvent::from(
            $id,
            $correlationId,
            $timestamp,
            'the-payload'
        );

        $this->assertSame('spriebsch.eventstore.the-test-topic', $event::topic());
    }

    #[Group('feature')]
    public function test_has_payload(): void
    {
        $id = EventId::generate();
        $correlationId = TestCorrelationId::generate();
        $timestamp = Timestamp::generate();
        $payload = 'the-payload';

        $event = TestEvent::from(
            $id,
            $correlationId,
            $timestamp,
            $payload
        );

        $this->assertSame($payload, $event->payload());
    }

    #[Group('feature')]
    public function test_has_event_id(): void
    {
        $id = EventId::generate();
        $correlationId = TestCorrelationId::generate();
        $timestamp = Timestamp::generate();

        $event = TestEvent::from(
            $id,
            $correlationId,
            $timestamp,
            'the-payload'
        );

        $this->assertSame($id->asString(), $event->id()->asString());
    }

    #[Group('exception')]
    public function test_event_id_must_be_set(): void
    {
        $event = new SerializableEventTraitTest_UninitializedEvent();

        $this->expectException(Error::class);
        $this->expectExceptionMessage('not be accessed before initialization');

        $event->id();
    }

    #[Group('exception')]
    public function test_correlation_id_must_be_set(): void
    {
        $event = new SerializableEventTraitTest_UninitializedEvent();

        $this->expectException(Error::class);
        $this->expectExceptionMessage('not be accessed before initialization');

        $event->correlationId();
    }

    #[Group('exception')]
    public function test_timestamp_must_be_set(): void
    {
        $event = new SerializableEventTraitTest_UninitializedEvent();

        $this->expectException(Error::class);
        $this->expectExceptionMessage('not be accessed before initialization');

        $event->timestamp();
    }

    public function test_json(): void
    {
        $id = EventId::generate();
        $correlationId = TestCorrelationId::generate();
        $timestamp = Timestamp::generate();

        $event = TestEvent::from(
            $id,
            $correlationId,
            $timestamp,
            'the-payload'
        );

        $expectedJson = json_encode(
            [
                'id'            => $id->asString(),
                'correlationId' => $correlationId->asString(),
                'timestamp'     => $timestamp->asString(),
                'payload'       => 'the-payload'
            ],
            JSON_THROW_ON_ERROR
        );

        $this->assertJsonStringEqualsJsonString($expectedJson, json_encode($event));
    }
}
