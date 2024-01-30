<?php declare(strict_types=1);

namespace spriebsch\eventstore;

class WithInMemoryEventStore
{
    use WithInMemoryEventStoreTrait;

    public function writer(): EventWriter
    {
        $this->setupEventStore();

        return $this->eventWriter;
    }

    public function reader(): EventReader
    {
        $this->setupEventStore();

        return $this->eventReader;
    }
}