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

final class NoEventWithThatTopicException extends Exception
{
    public function __construct(string $topic)
    {
        parent::__construct(
            sprintf(
                'There is no event with topic %s',
                $topic
            )
        );
    }
}
