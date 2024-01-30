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

abstract class AbstractJsonTestBase extends TestCase
{
    abstract protected function createJson(): Json;

    public function test_scalar_value_can_be_retrieved(): void
    {
        $this->assertEquals(
            $this->array()['the-scalar-key'],
            $this->createJson()->get('the-scalar-key')
        );
    }

    public function test_array_can_be_retrieved(): void
    {
        $this->assertEquals(
            $this->array()['the-array-key'],
            $this->createJson()->get('the-array-key')
        );
    }

    public function test_exception_when_key_does_not_exist(): void
    {
        $json = $this->createJson();

        $this->expectException(KeyNotFoundInJsonException::class);

        $json->get('does-not-exist');
    }

    public function test_full_document_can_be_retrieved(): void
    {
        $this->assertEquals(
            $this->array(),
            $this->createJson()->get()
        );
    }

    /**
     * Achieve path coverage in lazilyDecodeData()
     */
    public function test_repeated_access_works(): void
    {
        $json = $this->createJson();

        $this->assertSame($json->get('the-scalar-key'), $json->get('the-scalar-key'));
    }

    protected function array(): array
    {
        return [
            'the-scalar-key' => 'the-scalar-value',
            'the-array-key'  => ['the', 'array', 'value']
        ];
    }
}
