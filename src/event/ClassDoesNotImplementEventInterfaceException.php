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

final class ClassDoesNotImplementEventInterfaceException extends Exception
{
    public function __construct(string $class, string $topic)
    {
        parent::__construct(
            sprintf(
                'Class %s configured for topic %s does not implement %s interface',
                $class,
                $topic,
                Event::class
            )
        );
    }
}
