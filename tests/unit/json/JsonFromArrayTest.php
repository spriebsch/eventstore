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

#[CoversClass(Json::class)]
#[CoversClass(KeyNotFoundInJsonException::class)]
class JsonFromArrayTest extends AbstractJsonTestBase
{
    protected function createJson(): Json
    {
        return Json::fromArray($this->array());
    }

    public function test_can_be_created_from_array(): void
    {
        $this->assertInstanceOf(Json::class, $this->createJson());
    }
}