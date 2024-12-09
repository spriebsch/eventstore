<?php declare(strict_types=1);

namespace spriebsch\eventstore;

final class NoEventsToSourceFromException extends Exception
{
    public function __construct()
    {
        parent::__construct('No events to source from');
    }
}