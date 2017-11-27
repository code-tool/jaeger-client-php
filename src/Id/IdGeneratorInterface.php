<?php

namespace Jaeger\Id;

interface IdGeneratorInterface
{
    /**
     * @return int
     */
    public function next();
}
