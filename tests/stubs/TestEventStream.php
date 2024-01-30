<?php declare(strict_types=1);

/*
 * This file is part of Longbow.
 *
 * (c) Stefan Priebsch <stefan@priebsch.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spriebsch\longbow\tests;

use spriebsch\eventstore\CorrelationId;
use spriebsch\eventstore\EventId;
use spriebsch\eventstore\Events;
use spriebsch\eventstore\EventStream;

class TestEventStream implements EventStream
{
    public function limitNextQuery(int $limit): void {}

    public function all(CorrelationId $correlationId = null): Events {}

    public function sourceSince(EventId $eventId, CorrelationId $correlationId = null): Events {}

    public function processSince(EventId $eventId, CorrelationId $correlationId = null): Events {}

    public function lastEvent(): EventId {}
}
