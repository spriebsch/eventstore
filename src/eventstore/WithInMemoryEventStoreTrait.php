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

use spriebsch\sqlite\SqliteConnection;

trait WithInMemoryEventStoreTrait
{
    private ?SqliteConnection $connection = null;
    private SqliteEventWriter $eventWriter;
    private SqliteEventReader $eventReader;

    private function setupEventStore(): void
    {
        $this->eventWriter = SqliteEventWriter::from($this->connection());
        $this->eventReader = SqliteEventReader::from($this->connection());
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