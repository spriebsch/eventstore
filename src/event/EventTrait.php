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

use spriebsch\timestamp\Timestamp;

trait EventTrait
{
    private readonly EventId $id;
    private readonly CorrelationId $correlationId;
    private readonly Timestamp $timestamp;

    public function id(): EventId
    {
        return $this->id;
    }

    public function timestamp(): Timestamp
    {
        return $this->timestamp;
    }

    public function correlationId(): CorrelationId
    {
        return $this->correlationId;
    }
}