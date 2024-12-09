<?php declare(strict_types=1);

namespace spriebsch\eventstore;

class NoInitializeMethodException extends Exception
{
    public function __construct(object $object)
    {
        parent::__construct(
            sprintf(
                'Class %s has no initialize() method',
                $object::class
            )
        );
    }
}