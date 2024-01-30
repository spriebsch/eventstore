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
use spriebsch\sqlite\SqliteConnection;

#[CoversClass(SqliteEventStoreSchema::class)]
#[UsesClass(SqliteConnection::class)]
class SqliteEventStoreSchemaTest extends TestCase
{
    public function test_create_schema_in_memory(): void
    {
        $connection = SqliteConnection::from(':memory:');
        $schema = SqliteEventStoreSchema::from($connection);

        $schema->createIfNotExists();

        $this->assertEquals(
            5,
            $connection->query('SELECT * FROM events')->numColumns()
        );
    }

    public function test_create_schema_on_disk(): void
    {
        $tempDir = sys_get_temp_dir();

        if (!is_writable($tempDir)) {
            $this->markTestSkipped('Cannot write to temporary directory');
        }

        $db = tempnam($tempDir, 'test-db-');

        $connection = SqliteConnection::from($db);
        $schema = SqliteEventStoreSchema::from($connection);

        $schema->createIfNotExists();

        $this->assertEquals(
            5,
            $connection->query('SELECT * FROM events')->numColumns()
        );

        unlink($db);
    }

    public function test_create_schema_on_disk_when_already_exists(): void
    {
        $tempDir = sys_get_temp_dir();

        if (!is_writable($tempDir)) {
            $this->markTestSkipped('Cannot write to temporary directory');
        }

        $db = tempnam($tempDir, 'test-db-');

        $connection = SqliteConnection::from($db);
        $schema = SqliteEventStoreSchema::from($connection);

        $schema->createIfNotExists();
        $schema->createIfNotExists();

        $this->assertEquals(
            5,
            $connection->query('SELECT * FROM events')->numColumns()
        );

        unlink($db);
    }
}
