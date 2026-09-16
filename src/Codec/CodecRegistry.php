<?php

declare(strict_types=1);

namespace Jaeger\Codec;

use ArrayAccess;
use InvalidArgumentException;

/**
 * @implements ArrayAccess<string, CodecInterface>
 */
class CodecRegistry implements ArrayAccess
{
    /**
     * @var array<string, CodecInterface>
     */
    private array $codecs = [];

    public function offsetExists(mixed $offset): bool
    {
        return \array_key_exists($offset, $this->codecs);
    }

    public function offsetGet(mixed $offset): ?CodecInterface
    {
        return $this->codecs[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (null === $offset) {
            throw new InvalidArgumentException('A codec must be registered under a key, appending is not supported');
        }

        if (!$value instanceof CodecInterface) {
            throw new InvalidArgumentException(
                \sprintf('Codec must implement %s, %s given', CodecInterface::class, get_debug_type($value)),
            );
        }

        $this->codecs[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->codecs[$offset]);
    }
}
