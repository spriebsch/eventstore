<?php declare(strict_types=1);

namespace spriebsch\eventstore;

class EventStreamHasNotBeenQueriedException extends Exception
{
    public function __construct(EventStream $eventStream)
    {
        parent::__construct(
            sprintf(
                'Event stream %s must be queried before calling lastEventId()',
                $eventStream::class
            )
        );
    }
}