<?php
namespace Jaeger\Sampler;

class RateLimitingSampler extends AbstractSampler
{
    private $rate;

    private $generator;

    /**
     * RateLimitingSampler constructor.
     *
     * @param float              $rate
     * @param GeneratorInterface $generator
     */
    public function __construct($rate, GeneratorInterface $generator)
    {
        $this->rate = $rate;
        $this->generator = $generator;
    }

    /**
     * @param int $sec
     * @param int $count
     *
     * @return int
     */
    public function value($sec, $count)
    {
        return (($sec & 0xffffffff) << 16) + ($count & 0xffff);
    }

    /**
     * @param int $value
     *
     * @return array
     */
    public function spec($value)
    {
        return [$value >> 16, $value & 0xffff];
    }

    /**
     * @param int    $tracerId
     * @param string $operationName
     *
     * @return SamplerResult
     */
    public function doDecide($tracerId, $operationName)
    {
        $key = $this->generator->generate($tracerId, $operationName);
        $ttl = max((int)(1 / $this->rate + 1), 1);
        if (apcu_add($key, $this->value(time(), 1), $ttl)) {
            return new SamplerResult(
                true, 0x01, [
                        new SamplerTypeTag('ratelimiting'),
                        new SamplerDecisionTag(true),
                        new SamplerFlagsTag(0x01),
                        new SamplerParamTag((string)$this->rate)
                    ]
            );
        }

        $retries = 0;
        while ($retries < 5) {
            if (false === ($current = apcu_fetch($key))) {
                return $this->doDecide($tracerId, $operationName);
            }
            list ($timestamp, $count) = $this->spec((int)$current);
            $now = time();
            $diff = ($now === $timestamp) ? 1 : $now - $timestamp;
            if ($this->rate * $diff <= $count) {
                return new SamplerResult(false, 0);
            }
            if (false === apcu_cas($key, (int)$current, $this->value($timestamp, $count + 1))) {
                $retries++;
                continue;
            }

            return new SamplerResult(
                true, 0x01, [
                        new SamplerTypeTag('ratelimiting'),
                        new SamplerDecisionTag(true),
                        new SamplerFlagsTag(0x01),
                        new SamplerParamTag($key),
                        new SamplerParamTag((string)$this->rate)
                    ]
            );
        }

        return new SamplerResult(false, 0);
    }
}
