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

class JsonEvent
{
    private string $topic;
    private Json   $json;

    public static function from(string $topic, Json $json): self
    {
        return new self($topic, $json);
    }

    private function __construct(string $topic, Json $json)
    {
        $this->topic = $topic;
        $this->json = $json;
    }

    public function topic(): string
    {
        return $this->topic;
    }

    public function json(): Json
    {
        return $this->json;
    }
}
