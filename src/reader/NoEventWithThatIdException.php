<?php declare(strict_types=1);

namespace spriebsch\eventstore;

final class NoEventWithThatIdException extends Exception
{
    public function __construct(EventId $eventId)
    {
        parent::__construct(
            sprintf(
                'Failed to filter collection at %s because that event id does not exist',
                $eventId->asString()
            )
        );
    }
}
