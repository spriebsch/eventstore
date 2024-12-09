<?php declare(strict_types=1);

namespace spriebsch\eventstore;

class TestDecisionWithoutInitializeMethod
{
    use EventSourcedDecisionTrait;

    public static function from(): self
    {
        return new self(null);
    }
}