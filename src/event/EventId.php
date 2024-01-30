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

use spriebsch\uuid\UUID;

final class EventId
{
    private UUID $uuid;

    public static function generate(): self
    {
        return new self(UUID::generate());
    }

    public static function from(string $uuid): self
    {
        return new self(UUID::from($uuid));
    }

    public static function fromUUID(UUID $uuid): self
    {
        return new self($uuid);
    }

    private function __construct(UUID $uuid)
    {
        $this->uuid = $uuid;
    }

    public function asUUID(): UUID
    {
        return $this->uuid;
    }

    public function asString(): string
    {
        return $this->uuid->asString();
    }
}
