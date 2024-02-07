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

use spriebsch\sqlite\Connection;
use Throwable;
use const JSON_THROW_ON_ERROR;
use const SQLITE3_TEXT;

class SqliteEventWriter implements EventWriter
{
    private Connection $connection;

    public static function from(Connection $connection): self
    {
        return new self($connection);
    }

    private function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function store(Events $events): void
    {
        foreach ($events as $event) {
            $this->insertEvent(
                $event::topic(),
                $event->id(),
                $event->correlationId(),
                json_encode($event, JSON_THROW_ON_ERROR)
            );
        }
    }

    private function insertEvent(string $topic, EventId $eventId, CorrelationId $correlationId, string $json): void
    {
        try {
            $statement = $this->connection->prepare(
                'INSERT INTO events (topic, eventId, correlationId, event) VALUES(:topic, :eventId, :correlationId, :event)'
            );

            $statement->bindValue(':topic', $topic, SQLITE3_TEXT);
            $statement->bindValue(':eventId', $eventId->asString(), SQLITE3_TEXT);
            $statement->bindValue(':correlationId', $correlationId->asUUID()->asString(), SQLITE3_TEXT);
            $statement->bindValue(':event', $json, SQLITE3_TEXT);

            $result = $statement->execute();

            if ($result === false) {
                throw new FailedToStoreEventForUnknownReasonException($eventId);
            }
        } catch (Throwable $exception) {
            throw new FailedToStoreEventException($eventId, $exception);
        }
    }
}