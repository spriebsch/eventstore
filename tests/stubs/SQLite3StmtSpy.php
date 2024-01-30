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

use SQLite3Result;
use SQLite3Stmt;

class SQLite3StmtSpy extends SQLite3Stmt
{
    private array $bound = [];

    public function __construct(
        private readonly SQLite3Result $result
    ) {}

    public function bindValue(
        string|int $param,
        mixed      $value,
        int        $type = SQLITE3_TEXT
    ): bool
    {
        $this->bound[] = [$param, $value, $type];

        return true;
    }

    public function boundValues(): array
    {
        return $this->bound;
    }

    public function execute(): SQLite3Result|false
    {
        return $this->result;
    }
}