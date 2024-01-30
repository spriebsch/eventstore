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

use JsonSerializable;
use spriebsch\timestamp\Timestamp;

interface Event extends JsonSerializable
{
    public static function fromJson(Json $json): self;

    public static function topic(): string;

    public function id(): EventId;

    public function timestamp(): Timestamp;

    public function correlationId(): CorrelationId;

    public function jsonSerialize(): array;
}
