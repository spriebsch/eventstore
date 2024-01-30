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

#[CoversClass(WithInMemoryEventStoreTrait::class)]
#[UsesClass(SqliteEventReader::class)]
#[UsesClass(SqliteEventWriter::class)]
#[UsesClass(SqliteEventStoreSchema::class)]
#[UsesClass(SqliteConnection::class)]
class WithInMemoryEventStoreTraitTest extends TestCase
{
    public function test_reader(): void
    {
        $withInMemoryEventStore = new WithInMemoryEventStore;

        $this->assertInstanceOf(EventReader::class, $withInMemoryEventStore->reader());
    }

    public function test_writer(): void
    {
        $withInMemoryEventStore = new WithInMemoryEventStore;

        $this->assertInstanceOf(EventWriter::class, $withInMemoryEventStore->writer());
    }
}
