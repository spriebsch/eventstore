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
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use stdClass;

#[CoversClass(NoEventsToSourceFromException::class)]
#[CoversClass(NoInitializeMethodException::class)]
class ExceptionsTest extends TestCase
{
    #[Group('exception')]
    public function test_NoEventsToSourceFromException(): void
    {
        $exception = new NoEventsToSourceFromException;

        $this->assertSame('No events to source from', $exception->getMessage());
    }

    #[Group('exception')]
    public function test_NoInitializeMethodException(): void
    {
        $exception = new NoInitializeMethodException(new stdClass);

        $this->assertSame('Class stdClass has no initialize() method', $exception->getMessage());
    }
}
