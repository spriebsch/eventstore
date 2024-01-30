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

abstract class SqliteEventStoreTest_AbstractEvent implements Event
{
    use EventTrait;

    public static function from(EventId $id, CorrelationId $correlationId, Timestamp $timestamp): static
    {
        return new static($id, $correlationId, $timestamp);
    }

    public static function fromJson(Json $json): static
    {
        return new static(
            EventId::from($json->get('id')),
            TestCorrelationId::from($json->get('correlationId')),
            Timestamp::from($json->get('timestamp'))
        );
    }

    private function __construct(
        private readonly EventId       $id,
        private readonly CorrelationId $correlationId,
        private readonly Timestamp     $timestamp
    ) {}

    public function jsonSerialize(): array
    {
        return [
            'id'            => $this->id()->asString(),
            'correlationId' => $this->correlationId()->asString(),
            'timestamp'     => $this->timestamp()->asString()
        ];
    }

    public static function topic(): string
    {
        return '@todo';
    }
}