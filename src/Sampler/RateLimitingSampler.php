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
     * @param int    $tracerId
     * @param string $operationName
     *
     * @return SamplerResult
     */
    public function doDecide($tracerId, $operationName)
    {
        $key = $this->generator->generate($tracerId, $operationName);
        $ttl = max((int)(1 / $this->rate + 1), 1);
        if (apcu_add($key, sprintf('%s:%d', time(), 1), $ttl)) {
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
            list ($timestamp, $count) = explode(':', $current);
            if ((int)$count / (time() - (int)$timestamp) > $this->rate) {
                return new SamplerResult(false, 0);
            }
            if (false === apcu_cas($key, $current, sprintf('%s:%d', $timestamp, (int)$count + 1))) {
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
