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
use spriebsch\eventstore\tests\TestSourcingEvent;

#[CoversClass(EventSourcedDecisionTrait::class)]
#[CoversClass(EventSourcedTrait::class)]
#[UsesClass(EventId::class)]
#[UsesClass(EventSourcedTrait::class)]
#[UsesClass(Events::class)]
#[UsesClass(EventTrait::class)]
#[UsesClass(EmitsEventsTrait::class)]
class EventSourcedDecisionTraitTest extends TestCase
{
    public function test_can_be_created(): void
    {
        $id = TestCorrelationId::generate();
        $foo = 'the-foo';
        $bar = ['the-bar'];

        $decision = TestDecision::from($id, $foo, $bar);

        $this->assertSame($foo, $decision->foo());
        $this->assertSame($bar, $decision->bar());
    }

    public function test_can_be_sourced(): void
    {
        $id = TestCorrelationId::generate();
        $event = TestSourcingEvent::from($id);

        $decision = TestDecision::sourceFrom(Events::from($event));

        $this->assertSame($event, $decision->event());
    }
}
