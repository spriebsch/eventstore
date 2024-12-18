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

class SqliteEventStoreTest_Event1 extends SqliteEventStoreTest_AbstractEvent
{
    public static function topic(): string
    {
        return 'eventStore.test1';
    }
}
