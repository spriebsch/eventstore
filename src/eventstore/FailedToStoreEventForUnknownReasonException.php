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

final class FailedToStoreEventForUnknownReasonException extends Exception
{
    public function __construct(EventId $eventId)
    {
        parent::__construct(
            sprintf(
                'Failed to store event %s for unknown reasons',
                $eventId->asString(),
            )
        );
    }
}
