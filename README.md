# PHP OpenTracing API using Jaeger 

## Installation

```bash
composer require code-tool/jaeger-client-php
```

## Getting Started

It is strictly advised to use any form of DI container (e.g. [Symfony Bundle](https://github.com/code-tool/jaeger-client-symfony-bridge)).

```php
<?php

$span = $tracer->start('Parent Operation Name', [new StringTag('test.tag', 'Hello world in parent')]);
$childSpan = $tracer->start('Child Operation Name', [new StringTag('test.tag', 'Hello world in child')]);
$tracer->finish($childSpan);
$tracer->finish($span);  
```
