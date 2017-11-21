<?php
declare(strict_types=1);

namespace CodeTool\OpenTracing\Zipkin;

class Endpoint extends \CodeTool\OpenTracing\Jaeger\Thrift\Agent\Zipkin\Endpoint
{
    public function __construct(
        string $serviceName,
        string $ip4Address = '127.0.0.1',
        string $ip6Address = '::1',
        int $port = 5778
    ) {
        $this->service_name = $serviceName;
        $this->ipv4 = ip2long($ip4Address);
        $this->ipv6 = ip2long($ip6Address);
        $this->port = $port;
        parent::__construct();
    }
}
