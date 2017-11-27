<?php

namespace CodeTool\OpenTracing\Id;

interface IdGeneratorInterface
{
    /**
     * @return int
     */
    public function next();
}
