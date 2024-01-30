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

use PHPUnit\Framework\TestCase;

/**
 * @covers \spriebsch\eventstore\JsonEvent
 * @uses   \spriebsch\eventstore\Json
 */
class JsonEventTest extends TestCase
{
    public function test_topic(): void
    {
        $json = Json::from(json_encode([]));

        $this->assertEquals(
            'the-topic',
            (JsonEvent::from('the-topic', $json))->topic()
        );
    }

    public function test_from_JSON(): void
    {
        $json = Json::from(json_encode([]));

        $this->assertEquals($json, (JsonEvent::from('the-topic', $json))->json());
    }
}
