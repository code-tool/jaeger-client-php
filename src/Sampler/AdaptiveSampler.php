<?php
namespace Jaeger\Sampler;

class AdaptiveSampler implements SamplerInterface
{
    private $rateLimit;

    private $probabilistic;

    public function __construct(SamplerInterface $rateLimit, SamplerInterface $probabilistic)
    {
        $this->rateLimit = $rateLimit;
        $this->probabilistic = $probabilistic;
    }

    /**
     * @param int    $tracerId
     * @param string $operationName
     * @param string $debugId
     *
     * @return SamplerResult
     */
    public function decide($tracerId, $operationName, $debugId)
    {
        $rateLimitResult = $this->rateLimit->decide($tracerId, $operationName, $debugId);
        if ($rateLimitResult->isSampled()) {
            return new SamplerResult(
                true,
                $rateLimitResult->getFlags(),
                array_merge([new SamplerTypeTag('adaptive'),], $rateLimitResult->getTags())
            );
        }

        $probabilisticResult = $this->probabilistic->decide($tracerId, $operationName, $debugId);
        if ($probabilisticResult->isSampled()) {
            return new SamplerResult(
                true,
                $rateLimitResult->getFlags(),
                array_merge([new SamplerTypeTag('adaptive'),], $rateLimitResult->getTags())
            );
        }

        return new SamplerResult(
            false,
            0,
            [
                new SamplerTypeTag('adaptive'),
                new SamplerDecisionTag(false),
                new SamplerFlagsTag(0x00),
            ]
        );
    }
}
