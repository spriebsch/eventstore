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

use PHPUnit\Framework\TestCase;

class AbstractSqlStatementTestBase extends TestCase
{
    public function assertCorrelationId(SQLite3StmtSpy $statement, self $test): void
    {
        $test->assertContains(
            [
                ':correlationId',
                'b83262fc-8927-4e4d-ba26-4d5726a8ccd2',
                SQLITE3_TEXT
            ],
            $statement->boundValues()
        );
    }

    public function assertEventId(SQLite3StmtSpy $statement, self $test): void
    {
        $test->assertContains(
            [
                ':eventId',
                '52749ba8-0b87-4aa7-9416-5ccb860cc8a6',
                SQLITE3_TEXT
            ],
            $statement->boundValues()
        );
    }

    public function assertTopics(SQLite3StmtSpy $statement, self $test): void
    {
        $test->assertContains(
            [
                ':topic1',
                'topic-1',
                SQLITE3_TEXT
            ],
            $statement->boundValues()
        );
        $test->assertContains(
            [
                ':topic2',
                'topic-2',
                SQLITE3_TEXT
            ],
            $statement->boundValues()
        );
    }
}
