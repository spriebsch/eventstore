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
use spriebsch\sqlite\Connection;
use spriebsch\sqlite\SqliteConnection;
use spriebsch\timestamp\Timestamp;
use spriebsch\uuid\UUID;
use SQLite3Stmt;

#[CoversClass(SelectEventsSqlStatement::class)]
#[CoversClass(SqliteEventWriter::class)]
#[CoversClass(SqliteEventReader::class)]
#[CoversClass(NoEventWithThatIdException::class)]
#[UsesClass(NoEventWithThatTopicException::class)]
#[UsesClass(EventId::class)]
#[UsesClass(EventTrait::class)]
#[UsesClass(EventFactory::class)]
#[UsesClass(Json::class)]
#[UsesClass(JsonEvent::class)]
#[UsesClass(UUID::class)]
class SqliteEventStoreTest extends TestCase
{
    private ?SqliteConnection $connection = null;

    #[Group('feature')]
    public function test_stores_events(): void
    {
        $connection = $this->connection();

        $event1 = TestEvent::from(
            EventId::generate(),
            TestCorrelationId::generate(),
            Timestamp::generate(),
            'the-payload'
        );
        $event2 = TestEvent::from(
            EventId::generate(),
            TestCorrelationId::generate(),
            Timestamp::generate(),
            'the-payload'
        );

        $writer = SqliteEventWriter::from($connection);
        $writer->store(Events::from($event1, $event2));

        $reader = SqliteEventReader::from($connection);

        $readEvents = $reader->queued(null, null, null);

        $this->assertCount(2, $readEvents);
        $this->assertEquals($event1, $readEvents->asArray()[0]);
        $this->assertEquals($event2, $readEvents->asArray()[1]);
    }

    #[Group('feature')]
    public function test_reads_queued_events(): void
    {
        $connection = $this->connection();
        $writer = SqliteEventWriter::from($connection);

        $eventId = EventId::generate();

        $events = Events::from(
            SqliteEventStoreTest_Event1::from(
                EventId::generate(),
                TestCorrelationId::generate(),
                Timestamp::generate()
            ),
            SqliteEventStoreTest_Event2::from(
                $eventId,
                TestCorrelationId::generate(),
                Timestamp::generate()
            ),
            SqliteEventStoreTest_Event3::from(
                EventId::generate(),
                TestCorrelationId::generate(),
                Timestamp::generate()
            )
        );

        $writer->store($events);

        $loadedEvents = SqliteEventReader::from($connection)->queued(
            $eventId,
            null,
            null
        )->asArray();

        $this->assertCount(1, $loadedEvents);
        $this->assertEquals($events->asArray()[2], $loadedEvents[0]);
    }

    #[Group('feature')]
    public function test_queue_is_empty_when_position_is_last_event(): void
    {
        $connection = $this->connection();
        $writer = SqliteEventWriter::from($connection);

        $eventId = EventId::generate();

        $events = Events::from(
            SqliteEventStoreTest_Event1::from(
                $eventId,
                TestCorrelationId::generate(),
                Timestamp::generate()
            )
        );

        $writer->store($events);

        $loadedEvents = SqliteEventReader::from($connection)->queued(
            $eventId,
            null,
            null,
        )->asArray();

        $this->assertCount(0, $loadedEvents);
    }

    #[Group('feature')]
    public function test_queue_is_empty_when_position_does_not_exist(): void
    {
        $connection = $this->connection();

        $result = SqliteEventReader::from($connection)->queued(
            EventId::generate(),
            null,
            null
        );

        $this->assertEmpty($result);
    }

    #[Group('exception')]
    public function test_exception_when_event_cannot_be_written(): void
    {
        $connection = $this->createMock(Connection::class);
        $writer = SqliteEventWriter::from($connection);

        $statement = $this->createMock(SQLite3Stmt::class);
        $statement->method('execute')->willReturn(false);

        $connection->method('prepare')->willReturn($statement);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Failed to store event');

        $writer->store(
            Events::from(
                SqliteEventStoreTest_Event1::from(
                    EventId::generate(),
                    TestCorrelationId::generate(),
                    Timestamp::generate()
                )
            )
        );
    }

    #[Group('feature')]
    public function test_reads_all_events(): void
    {
        $connection = $this->connection();
        $writer = SqliteEventWriter::from($connection);

        $events = $this->threeTestEvents();

        $writer->store($events);

        $loadedEvents = SqliteEventReader::from($connection)->queued(
            null,
            null,
            null
        )->asArray();

        $this->assertEquals($events->asArray(),$loadedEvents);
    }

    #[Group('feature')]
    public function test_sources_events_for_correlation_id(): void
    {
        $eventId = EventId::generate();
        $correlationId = TestCorrelationId::generate();

        $connection = $this->connection();
        $writer = SqliteEventWriter::from($connection);

        $events = Events::from(
            SqliteEventStoreTest_Event1::from(
                EventId::generate(),
                TestCorrelationId::generate(),
                Timestamp::generate()
            ),
            SqliteEventStoreTest_Event1::from(
                $eventId,
                $correlationId,
                Timestamp::generate()
            ),
            SqliteEventStoreTest_Event2::from(
                EventId::generate(),
                $correlationId,
                Timestamp::generate()
            )
        );

        $writer->store($events);

        $loadedEvents = SqliteEventReader::from($connection)->source(
            $eventId,
            null,
            $correlationId
        )->asArray();

        $this->assertCount(1, $loadedEvents);
        $this->assertEquals(
            $events->asArray()[1],
            $loadedEvents[0]
        );
    }

    #[Group('feature')]
    public function test_reads_events_by_topic(): void
    {
        $connection = $this->connection();
        $writer = SqliteEventWriter::from($connection);

        $events = $this->threeTestEvents();

        $writer->store($events);

        $loadedEvents = SqliteEventReader::from($connection)->source(
            $events->lastEventId(),
            null,
            null,
            SqliteEventStoreTest_Event1::topic(),
            SqliteEventStoreTest_Event3::topic()
        )->asArray();

        $this->assertCount(2, $loadedEvents);
        $this->assertEquals($events->asArray()[0], $loadedEvents[0]);
        $this->assertEquals($events->asArray()[2], $loadedEvents[1]);
    }

    #[Group('feature')]
    public function test_reads_events_with_limit(): void
    {
        $connection = $this->connection();
        $writer = SqliteEventWriter::from($connection);

        $events = $this->threeTestEvents();

        $writer->store($events);

        $loadedEvents = SqliteEventReader::from($connection)->source(
            $events->lastEventId(),
            2,
            null,
        )->asArray();

        $this->assertCount(2, $loadedEvents);
        $this->assertEquals($events->asArray()[0], $loadedEvents[0]);
        $this->assertEquals($events->asArray()[1], $loadedEvents[1]);
    }

    #[Group('exception')]
    public function test_fails_to_store_event(): void
    {
        $connection = SqliteConnection::from(':memory:');
        $writer = SqliteEventWriter::from($connection);

        $event = TestEvent::from(
            EventId::generate(),
            TestCorrelationId::generate(),
            Timestamp::generate(),
            'the-payload'
        );

        $this->expectException(FailedToStoreEventException::class);

        $writer->store(Events::from($event));
    }

    private function threeTestEvents(): Events
    {
        return Events::from(
            SqliteEventStoreTest_Event1::from(
                EventId::generate(),
                TestCorrelationId::generate(),
                Timestamp::generate()
            ),
            SqliteEventStoreTest_Event2::from(
                EventId::generate(),
                TestCorrelationId::generate(),
                Timestamp::generate()
            ),
            SqliteEventStoreTest_Event3::from(
                EventId::generate(),
                TestCorrelationId::generate(),
                Timestamp::generate()
            )
        );
    }

    private function connection(): SqliteConnection
    {
        if ($this->connection === null) {
            $this->connection = SqliteConnection::from(':memory:');
            SqliteEventStoreSchema::from($this->connection)->createIfNotExists();
        }

        return $this->connection;
    }
}
