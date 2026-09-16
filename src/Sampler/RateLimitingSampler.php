<?php

declare(strict_types=1);

namespace Jaeger\Sampler;

class RateLimitingSampler extends AbstractSampler
{
    public function __construct(
        private readonly float $rate,
        private readonly GeneratorInterface $generator,
    ) {}

    public function value(int $sec, int $count): int
    {
        return (($sec & 0xffffffff) << 16) + ($count & 0xffff);
    }

    public function spec(int $value): array
    {
        return [$value >> 16, $value & 0xffff];
    }

    public function doDecide(int $tracerId, string $operationName): SamplerResult
    {
        $key = $this->generator->generate($tracerId, $operationName);
        $ttl = max((int) (1 / $this->rate + 1), 1);
        if (apcu_add($key, $this->value(time(), 1), $ttl)) {
            return new SamplerResult(
                true,
                0x01,
                [
                    new SamplerTypeTag('ratelimiting'),
                    new SamplerDecisionTag(true),
                    new SamplerFlagsTag(0x01),
                    new SamplerParamTag((string) $this->rate),
                ],
            );
        }

        $retries = 0;
        while ($retries < 5) {
            if (false === ($current = apcu_fetch($key))) {
                return $this->doDecide($tracerId, $operationName);
            }

            [$timestamp, $count] = $this->spec((int) $current);
            $now = time();
            $diff = ($now === $timestamp) ? 1 : $now - $timestamp;
            if ($this->rate * $diff <= $count) {
                return new SamplerResult(false, 0);
            }

            if (false === apcu_cas($key, (int) $current, $this->value($timestamp, $count + 1))) {
                $retries++;
                continue;
            }

            return new SamplerResult(
                true,
                0x01,
                [
                    new SamplerTypeTag('ratelimiting'),
                    new SamplerDecisionTag(true),
                    new SamplerFlagsTag(0x01),
                    new SamplerParamTag($key),
                    new SamplerParamTag((string) $this->rate),
                ],
            );
        }

        return new SamplerResult(false, 0);
    }
}
