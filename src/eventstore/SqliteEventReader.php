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
use const SQLITE3_ASSOC;

class SqliteEventReader implements EventReader
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

    public function source(
        EventId        $untilPosition,
        ?int           $limit,
        ?CorrelationId $correlationId,
        string         ...$topics
    ): Events
    {
        return $this->query(
            new SourcingSelectEventsSqlStatement(
                   $limit,
                   $correlationId,
                   $untilPosition,
                ...$topics
            )
        );
    }

    public function queued(
        ?EventId       $fromPosition,
        ?int           $limit,
        ?CorrelationId $correlationId,
        string         ...$topics
    ): Events
    {
        return $this->query(
            new QueueSelectEventsSqlStatement(
                   $limit,
                   $correlationId,
                   $fromPosition,
                ...$topics
            )
        );
    }

    private function query(SelectEventsSqlStatement $statement): Events
    {
        $queryResult = $statement->execute($this->connection);

        $jsonEvents = [];

        while ($row = $queryResult->fetchArray(SQLITE3_ASSOC)) {
            $jsonEvents[] = JsonEvent::from(
                $row['topic'],
                Json::from($row['event'])
            );
        }

        return Events::fromJsonEvents(...$jsonEvents);
    }
}
