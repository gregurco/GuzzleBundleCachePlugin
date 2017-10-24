Guzzle Bundle Cache Plugin
==================

[![Build Status](https://travis-ci.org/gregurco/GuzzleBundleCachePlugin.svg?branch=master)](https://travis-ci.org/gregurco/GuzzleBundleCachePlugin) [![Coverage Status](https://coveralls.io/repos/gregurco/GuzzleBundleCachePlugin/badge.svg?branch=master)](https://coveralls.io/r/gregurco/GuzzleBundleCachePlugin)

This plugin integrates [Cache][1] functionality into Guzzle Bundle, a bundle for building RESTful web service clients.


Requirements
------------
 - PHP 7.0 or above
 - [Guzzle Bundle][2]

 
Installation
------------
Using [composer][3]:

``` json
{
    "require": {
        "gregurco/guzzle-bundle-cache-plugin": "dev-master"
    }
}
```


Usage
-----
Load plugin in AppKernel.php:
``` php
new EightPoints\Bundle\GuzzleBundle\EightPointsGuzzleBundle([
    new Gregurco\Bundle\GuzzleBundleCachePlugin\GuzzleBundleCachePlugin(),
])
```

Configuration in config.yml:
``` yaml
eight_points_guzzle:
    clients:
        api_payment:
            base_url: "http://api.domain.tld"

            # define headers, options

            # plugin settings
            plugin:
                cache:
                    enabled: true
                    strategy: "strategy_service_id" # optional
```

License
-------
This middleware is licensed under the MIT License - see the LICENSE file for details

[1]: http://www.xml.com/pub/a/2003/12/17/dive.html
[2]: https://github.com/8p/EightPointsGuzzleBundle
[3]: https://getcomposer.org/
