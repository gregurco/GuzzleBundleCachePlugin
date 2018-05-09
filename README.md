# Guzzle Bundle Cache Plugin

[![Build Status](https://travis-ci.org/gregurco/GuzzleBundleCachePlugin.svg?branch=master)](https://travis-ci.org/gregurco/GuzzleBundleCachePlugin)
[![Coverage Status](https://coveralls.io/repos/gregurco/GuzzleBundleCachePlugin/badge.svg?branch=master)](https://coveralls.io/r/gregurco/GuzzleBundleCachePlugin)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/08400bb3-540d-4616-b0b3-f694a73a72cf/mini.png)](https://insight.sensiolabs.com/projects/08400bb3-540d-4616-b0b3-f694a73a72cf)

This plugin integrates cache functionality into Guzzle Bundle, a bundle for building RESTful web service clients.

## Requirements
 - PHP 7.0 or above
 - [Guzzle Bundle][1]
 - [Guzzle Cache middleware][2]

 
## Installation
Using [composer][3]:

##### composer.json
``` json
{
    "require": {
        "gregurco/guzzle-bundle-cache-plugin": "dev-master"
    }
}
```

##### command line
``` bash
$ composer require gregurco/guzzle-bundle-cache-plugin
```

## Usage
### Enable bundle

#### Symfony 2.x and 3.x
Plugin will be activated/connected through bundle constructor in `app/AppKernel.php`, like this:

``` php 
new EightPoints\Bundle\GuzzleBundle\EightPointsGuzzleBundle([
    new Gregurco\Bundle\GuzzleBundleCachePlugin\GuzzleBundleCachePlugin(),
])
```

#### Symfony 4
The registration of bundles was changed in Symfony 4 and now you have to change `src/Kernel.php` to achieve the same functionality.  
Find next lines:

```php
foreach ($contents as $class => $envs) {
    if (isset($envs['all']) || isset($envs[$this->environment])) {
        yield new $class();
    }
}
```

and replace them by:

```php
foreach ($contents as $class => $envs) {
    if (isset($envs['all']) || isset($envs[$this->environment])) {
        if ($class === \EightPoints\Bundle\GuzzleBundle\EightPointsGuzzleBundle::class) {
            yield new $class([
                new \Gregurco\Bundle\GuzzleBundleCachePlugin\GuzzleBundleCachePlugin(),
            ]);
        } else {
            yield new $class();
        }
    }
}
```

### Basic configuration
``` yaml
# app/config/config.yml

eight_points_guzzle:
    clients:
        api_payment:
            base_url: "http://api.domain.tld"

            # define headers, options

            # plugin settings
            plugin:
                cache:
                    enabled: true
```

### Configuration with specific cache strategy
``` yaml
# app/config/services.yml

services:
    acme.filesystem_cache:
        class: Doctrine\Common\Cache\FilesystemCache
        arguments: ['/tmp/']
        public: false

    acme.doctrine_cache_storage:
        class: Kevinrob\GuzzleCache\Storage\DoctrineCacheStorage
        arguments: ['@acme.filesystem_cache']
        public: false

    acme.private_cache_strategy:
        class: Kevinrob\GuzzleCache\Strategy\PrivateCacheStrategy
        arguments: ['@acme.doctrine_cache_storage']
        public: false

```

``` yaml
# app/config/config.yml

eight_points_guzzle:
    clients:
        api_payment:
            plugin:
                cache:
                    enabled: true
                    strategy: "acme.private_cache_strategy"
```

More information about cache strategies can be found here: [Kevinrob/guzzle-cache-middleware][2]

### Invalidate cache
```php
# get client
$apiPaymentClient = $this->get('eight_points_guzzle.client.api_payment');

# do a request
$apiPaymentClient->request('GET', 'ping');

# invalidate cache
$event = new InvalidateRequestEvent($apiPaymentClient, 'GET', 'ping');
$this->get('event_dispatcher')->dispatch(GuzzleBundleCacheEvents::INVALIDATE, $event);
```

## License
This middleware is licensed under the MIT License - see the LICENSE file for details

[1]: https://github.com/8p/EightPointsGuzzleBundle
[2]: https://github.com/Kevinrob/guzzle-cache-middleware
[3]: https://getcomposer.org/
