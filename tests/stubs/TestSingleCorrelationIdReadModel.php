<?php declare(strict_types=1);

namespace spriebsch\eventstore;

use spriebsch\eventstore\tests\TestSourcingEvent;

class TestSingleCorrelationIdReadModel
{
    use EventSourcedReadModelTrait;

    private TestSourcingEvent $event;
    private ?CorrelationId    $id = null;

    private function applyTestSourcingEvent(TestSourcingEvent $event): void
    {
        $this->event = $event;
    }

    public function event(): TestSourcingEvent
    {
        return $this->event;
    }
}