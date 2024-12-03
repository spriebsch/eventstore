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

use spriebsch\uuid\UUID;

interface EventStream
{
    public function limitNextQuery(int $limit): void;

    public function source(EventId $position, ?CorrelationId $correlationId = null): Events;

    public function queued(?EventId $position, ?CorrelationId $correlationId = null): Events;

    public function all(?CorrelationId $correlationId = null): Events;

    public function lastEvent(): ?EventId;
}
