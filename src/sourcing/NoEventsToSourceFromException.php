<?php declare(strict_types=1);

namespace spriebsch\eventstore;

class NoEventsToSourceFromException extends Exception
{
    public function __construct()
    {
        parent::__construct(sprintf('No events to source from'));
    }
}