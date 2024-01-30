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
use SQLite3Result;
use SQLite3Stmt;

abstract class SelectEventsSqlStatement
{
    private array $topics;

    public function __construct(
        private readonly ?int           $limit,
        private readonly ?CorrelationId $correlationId,
        private readonly ?EventId       $position,
        string                          ...$topics
    )
    {
        $this->topics = $topics;
    }

    public function execute(Connection $connection): SQLite3Result
    {
        $statement = $connection->prepare($this->buildSql());

        $this->bindCorrelationId($statement, $this->correlationId);
        $this->bindPosition($statement, $this->position);
        $this->bindTopics($statement, ...$this->topics);

        return $statement->execute();
    }

    private function buildSql(): string
    {
        return 'SELECT topic, event FROM events' . $this->buildWhereClause();
    }

    private function buildWhereClause(): string
    {
        $whereClauses = [];
        $limitClause = '';

        if ($this->correlationId !== null) {
            $whereClauses[] = 'correlationId=:correlationId';
        }

        if ($this->position !== null) {
            $whereClauses[] = $this->buildSubSelect();
        }

        if (count($this->topics) !== 0) {
            $whereClauses[] = 'topic IN (' . $this->topicPlaceholders(...$this->topics) . ')';
        }

        if ($this->limit !== null) {
            $limitClause = ' LIMIT ' . $this->limit;
        }

        if (count($whereClauses) === 0) {
            return $limitClause;
        }

        return ' WHERE ' . implode(' AND ', $whereClauses) . $limitClause;
    }

    private function topicPlaceholders(string ...$topics): string
    {
        return implode(
            ',',
            array_map(
                fn(int $index) => ':topic' . $index,
                range(1, count($topics))
            )
        );
    }

    private function bindCorrelationId(SQLite3Stmt $statement, ?CorrelationId $correlationId): void
    {
        if ($correlationId === null) {
            return;
        }

        $statement->bindValue(
            ':correlationId',
            $correlationId->asUUID()->asString(),
            SQLITE3_TEXT
        );
    }

    private function bindTopics(SQLite3Stmt $statement, string ...$topics): void
    {
        $position = 1;

        foreach ($topics as $topic) {
            $statement->bindValue(
                ':topic' . $position,
                $topic,
                SQLITE3_TEXT
            );
            $position++;
        }
    }

    private function bindPosition(
        SQLite3Stmt $statement,
        ?EventId    $position
    ): void
    {
        if ($position === null) {
            return;
        }

        $statement->bindValue(':eventId', $position->asString(), SQLITE3_TEXT);
    }

    abstract protected function buildSubSelect(): string;
}