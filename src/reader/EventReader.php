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

interface EventReader
{
    public function source(
        EventId        $untilPosition,
        ?int           $limit,
        ?CorrelationId $correlationId,
        string         ...$topics
    ): Events;

    public function queued(
        ?EventId       $fromPosition,
        ?int           $limit,
        ?CorrelationId $correlationId,
        string         ...$topics
    ): Events;
}
