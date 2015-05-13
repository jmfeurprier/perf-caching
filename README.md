perf caching
============

caching package from perf, the PHP Extensible and Robust Framework.

## Usage

```php
<?php

$storage = new \perf\Caching\CacheClient::createDefault();

$cache = \perf\Caching\CacheClient::createDefault();

$cache->
// Will output something like "2015-01-23"
echo $clock->getDateString();

// Will output something like "15:16:17"
echo $clock->getTimeString();

// Will output something like "2015-01-23 15:16:17"
echo $clock->getDateTimeString();

// Will output something like "123456789"
echo $clock->getTimestamp();

// Will output something like "123456789.0123 "
echo $clock->getMicrotime();
```

