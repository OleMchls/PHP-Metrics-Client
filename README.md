# PHP Metrics Client

[![Build Status](https://secure.travis-ci.org/nesQuick/PHP-Metrics-Client.png?branch=master)](http://travis-ci.org/nesQuick/PHP-Metrics-Client)

A PHP Client for sending data to [librato metrics][].
Inspired or ported from [node-librato-metrics](https://github.com/holidayextras/node-librato-metrics) from [@felixge](https://twitter.com/felixge)

[librato metrics]: metrics.librato.com

## Install

Installation should be done via [composer](http://packagist.org/).

```
{
    "require": {
        "nesQuick/Metrics": "master-dev"
    }
}
```

## Example

```php
use Metrics\Client;

$client = new Client('user@example.org', '...');
$client->post('/metrics', array(
  'gauges' => array(
    array('name' => 'metric1', 'value' => 123)
  )
));
```

## ToDo's
* Documentation

## License

Licensed under the MIT license.