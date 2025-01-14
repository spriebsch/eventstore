<?php declare(strict_types=1);

/*
 * This file is part of EventStore.
 *
 * (c) Stefan Priebsch <stefan@priebsch.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spriebsch\eventstore\tests;

class TestValueObject
{
    private function __construct(
        private readonly string $payload
    ) {}

    public static function from(string $payload): self
    {
        return new self($payload);
    }

    public function asString(): string
    {
        return $this->payload;
    }
}
