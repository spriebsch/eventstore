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

trait EventSourcedReadModelTrait
{
    use EventSourcedTrait {
        apply as public;
    }

    private function __construct(Events $events)
    {
        $this->reconstituteFrom($events);
    }
}