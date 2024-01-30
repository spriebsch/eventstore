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
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use spriebsch\sqlite\Connection;
use SQLite3Result;

#[CoversClass(SourcingSelectEventsSqlStatement::class)]
#[CoversClass(SelectEventsSqlStatement::class)]
#[UsesClass(EventId::class)]
class SourcingSqlStatementTest extends AbstractSqlStatementTestBase
{
    #[Test]
    #[Group('feature')]
    #[DataProviderExternal(ProvideSourcingQueries::class, 'provideQueries')]
    public function sql(
        string                           $sql,
        SourcingSelectEventsSqlStatement $statement,
        callable                         $assertBindValues
    ): void
    {
        $connection = $this->createMock(Connection::class);
        $result = $this->createMock(SQLite3Result::class);
        $spy = new SQLite3StmtSpy($result);

        $connection
            ->expects($this->once())
            ->method('prepare')
            ->with($sql)
            ->willReturn($spy);

        $statement->execute($connection);

        $assertBindValues($spy, $this);
    }
}
