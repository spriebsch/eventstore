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
use spriebsch\sqlite\SqliteSchema;

final class SqliteEventStoreSchema extends SqliteSchema
{
    protected function schemaExists(Connection $connection): bool
    {
        $result = $connection->query(
            "SELECT sql FROM sqlite_master WHERE name='events';"
        );

        $row = $result->fetchArray(SQLITE3_ASSOC);

        if ($row === false) {
            return false;
        }

        return $row['sql'] !== $this->sql();
    }

    protected function createSchema(Connection $connection): void
    {
        $connection->exec($this->sql());
    }

    private function sql(): string
    {
        return 'BEGIN TRANSACTION; CREATE TABLE `events` (
            `id` INTEGER PRIMARY KEY AUTOINCREMENT,
            `eventId` TEXT UNIQUE,
            `correlationId` TEXT,
            `topic` TEXT,
            `event` TEXT
        ); END TRANSACTION;';
    }
}
