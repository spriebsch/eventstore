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

class Json
{
    private ?array $data = null;
    private string $json;

    public static function from(string $json): self
    {
        return new self($json);
    }

    public static function fromArray(array $data): self
    {
        return new self(json_encode($data, JSON_THROW_ON_ERROR));
    }

    private function __construct(string $json)
    {
        $this->json = $json;
    }

    public function get(?string $key = null): mixed
    {
        $this->lazilyDecodeData();

        if ($key !== null) {
            return $this->getKey($key);
        }

        return $this->data;
    }

    private function decode(string $json): array
    {
        return json_decode($this->json, true, flags: JSON_THROW_ON_ERROR);
    }

    private function lazilyDecodeData(): void
    {
        if ($this->data !== null) {
            return;
        }

        $this->data = $this->decode($this->json);
    }

    private function getKey(string $key): mixed
    {
        if ($this->doesNotHaveKey($key)) {
            throw new KeyNotFoundInJsonException($key);
        }

        return $this->data[$key];
    }

    private function doesNotHaveKey(string $key): bool
    {
        return !isset($this->data[$key]);
    }
}
