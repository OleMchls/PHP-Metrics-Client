# PHP Metrics Client

A PHP Client for sending data to [librato metrics][].

Inspired or ported from [node-librato-metrics](https://github.com/holidayextras/node-librato-metrics) from [@felixge](https://twitter.com/felixge)

[librato metrics]: metrics.librato.com

## Install

```
tbd - composer planned
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

## License

Licensed under the MIT license.