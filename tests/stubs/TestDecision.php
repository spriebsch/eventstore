<?php declare(strict_types=1);

namespace spriebsch\eventstore;

use spriebsch\eventstore\tests\TestSourcingEvent;

class TestDecision
{
    use EventSourcedDecisionTrait;

    private TestSourcingEvent $event;
    private string            $parameter1;
    private array             $parameter2;
    private ?CorrelationId    $id = null;

    public static function from(CorrelationId $id, string $parameter1, array $parameter2): self
    {
        return new self(null, $id, $parameter1, $parameter2);
    }

    private function initialize(CorrelationId $id, string $parameter1, array $parameter2): Event
    {
        $this->id = $id;
        $this->parameter1 = $parameter1;
        $this->parameter2 = $parameter2;

        return TestSourcingEvent::from($id);
    }

    private function applyTestSourcingEvent(TestSourcingEvent $event): void
    {
        $this->event = $event;
    }

    public function event(): TestSourcingEvent
    {
        return $this->event;
    }

    public function foo(): string
    {
        return $this->parameter1;
    }

    public function bar(): array
    {
        return $this->parameter2;
    }

    public function changeState(TestSourcingEvent $event): void
    {
        $this->record($event);
    }
}