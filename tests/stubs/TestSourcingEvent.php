<?php declare(strict_types=1);

/*
 * This file is part of EventStore.
 *
 * (c) Stefan Priebsch <stefan@priebsch.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spriebsch\eventstore\tests;

use spriebsch\eventstore\CorrelationId;
use spriebsch\eventstore\Event;
use spriebsch\eventstore\EventId;
use spriebsch\eventstore\EventTrait;
use spriebsch\eventstore\Json;
use spriebsch\eventstore\SerializableEventTrait;
use spriebsch\eventstore\TestCorrelationId;
use spriebsch\timestamp\Timestamp;

class TestSourcingEvent implements Event
{
    use EventTrait;
    use SerializableEventTrait;

    private function __construct(
        private readonly EventId       $id,
        private readonly CorrelationId $correlationId,
        private readonly Timestamp     $timestamp
    ) {}

    public static function from(
        CorrelationId $correlationId
    ): self
    {
        return new self(
            EventId::generate(), $correlationId, Timestamp::generate()
        );
    }

    public static function fromJson(Json $json): self
    {
        return new self(
            EventId::from($json->get('id')),
            TestCorrelationId::from($json->get('correlationId')),
            Timestamp::from($json->get('timestamp'))
        );
    }

    public static function topic(): string
    {
        return 'spriebsch.eventstore.the-test-sourcing-topic';
    }

    protected function serialize(): array
    {
        return [];
    }
}