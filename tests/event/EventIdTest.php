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

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use spriebsch\uuid\UUID;

#[CoversClass(EventId::class)]
#[UsesClass(UUID::class)]
class EventIdTest extends TestCase
{
    public function test_can_be_generated(): void
    {
        $this->assertInstanceOf(EventId::class, EventId::generate());
    }

    public function test_can_be_created_from_string(): void
    {
        $uuid = UUID::generate();

        $this->assertSame($uuid->asString(), EventId::from($uuid->asString())->asString());
    }

    public function test_can_be_created_from_UUID(): void
    {
        $uuid = UUID::generate();

        $this->assertSame($uuid->asString(), EventId::fromUUID($uuid)->asString());
    }

    public function test_can_be_converted_to_string(): void
    {
        $uuid = UUID::generate();

        $this->assertSame($uuid->asString(), EventId::from($uuid->asString())->asString());
    }

    public function test_can_be_converted_to_UUID(): void
    {
        $uuid = UUID::generate();

        $this->assertSame(
            $uuid->asString(),
            EventId::fromUUID($uuid)->asUUID()->asString()
        );
    }
}
