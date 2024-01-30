<?php declare(strict_types=1);

/*
 * This file is part of EventStore.
 *
 * (c) Stefan Priebsch <stefan@priebsch.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spriebsch\eventstore;

trait EventSourcedDecisionTrait
{
    use EmitsEventsTrait;
    use EventSourcedTrait;

    private function __construct(?Events $events)
    {
        if ($events === null) {
            $this->record(
                $this->initialize(
                    ...$this->argumentsWithoutEvents(func_get_args())
                )
            );
        } else {
            $this->reconstituteFrom($events);
        }
    }

    private function argumentsWithoutEvents(array $constructorArguments): array
    {
        return array_splice($constructorArguments, 1);
    }

    private function record(Event $event): void
    {
        $this->apply($event);
        $this->emit($event);
    }
}