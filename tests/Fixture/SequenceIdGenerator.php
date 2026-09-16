<?php

declare(strict_types=1);

namespace Jaeger\Tests\Fixture;

use Jaeger\Id\IdGeneratorInterface;
use RuntimeException;

/**
 * Hands out a predetermined sequence of ids so span identifiers are assertable.
 */
final class SequenceIdGenerator implements IdGeneratorInterface
{
    private int $position = 0;

    /**
     * @param list<int> $ids
     */
    public function __construct(private readonly array $ids) {}

    public function next(): int
    {
        if (!\array_key_exists($this->position, $this->ids)) {
            throw new RuntimeException(\sprintf('SequenceIdGenerator ran out of ids after %d calls', $this->position));
        }

        return $this->ids[$this->position++];
    }

    public function callCount(): int
    {
        return $this->position;
    }
}
