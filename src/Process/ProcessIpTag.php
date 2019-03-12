<?php

declare(strict_types = 1);

namespace Jaeger\Process;

use Jaeger\Tag\StringTag;

class ProcessIpTag extends StringTag
{
    private function getIp(): string
    {
        if (\array_key_exists('SERVER_ADDR', $_SERVER) && '' !== ($ip = (string)$_SERVER['SERVER_ADDR'])) {
            return $ip;
        }

        if (false === $hostName = \gethostname()) {
            return '127.0.0.1';
        }

        return \gethostbyname($hostName);
    }

    public function __construct()
    {
        parent::__construct('ip', $this->getIp());
    }
}
