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

trait SerializableEventTrait
{
    private readonly EventId       $id;
    private readonly CorrelationId $correlationId;
    private readonly Timestamp     $timestamp;

    public function jsonSerialize(): array
    {
        return array_merge(
            [
                'id'            => $this->id()->asString(),
                'correlationId' => $this->correlationId()->asUUID()->asString(),
                'timestamp'     => $this->timestamp()->asString()
            ],
            $this->serialize()
        );
    }

    abstract protected function serialize(): array;
}
