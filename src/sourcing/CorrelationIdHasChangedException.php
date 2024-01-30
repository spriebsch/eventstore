<?php declare(strict_types=1);

namespace spriebsch\eventstore;

class CorrelationIdHasChangedException extends Exception
{
    public function __construct(CorrelationId $initial, CorrelationId $new)
    {
        parent::__construct(
            sprintf(
                'Correlation ID mismatch, %s changed to %s',
                $initial->asUUID()->asString(),
                $new->asUUID()->asString(),
            )
        );
    }
}