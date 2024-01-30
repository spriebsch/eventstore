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

class TestEvent implements Event
{
    use EventTrait;
    use SerializableEventTrait;

    private function __construct(
        private readonly EventId       $id,
        private readonly CorrelationId $correlationId,
        private readonly Timestamp     $timestamp,
        private readonly string        $payload
    ) {}

    public static function generate(): self
    {
        return new self(
            EventId::generate(),
            TestCorrelationId::generate(),
            Timestamp::generate(),
            'the-payload'
        );
    }

    public static function from(
        EventId       $eventId,
        CorrelationId $correlationId,
        Timestamp     $timestamp,
        string        $payload
    ): self
    {
        return new self(
            $eventId, $correlationId, $timestamp, $payload
        );
    }

    public static function fromJson(Json $json): self
    {
        return new self(
            EventId::from($json->get('id')),
            TestCorrelationId::from($json->get('correlationId')),
            Timestamp::from($json->get('timestamp')),
            $json->get('payload')
        );
    }

    public static function topic(): string
    {
        return 'spriebsch.eventstore.the-test-topic';
    }

    public function payload(): string
    {
        return $this->payload;
    }

    protected function serialize(): array
    {
        return [
            'payload' => $this->payload
        ];
    }
}