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

class KeyNotFoundInJsonException extends Exception
{
    public function __construct(string $key)
    {
        parent::__construct(
            sprintf('Key %s not found in JSON document', $key)
        );
    }
}
