<?php declare(strict_types=1);

namespace spriebsch\eventstore;

use spriebsch\eventstore\tests\TestSourcingEvent;

class TestMultipleCorrelationIdsReadModel
{
    use EventSourcedReadModelTrait;

    private function applyTestSourcingEvent(TestSourcingEvent $event): void
    {
    }
}