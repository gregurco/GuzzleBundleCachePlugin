<?php

namespace Gregurco\Bundle\GuzzleBundleWssePlugin\Test\DependencyInjection;

use Gregurco\Bundle\GuzzleBundleCachePlugin\DependencyInjection\GuzzleCacheExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Kevinrob\GuzzleCache\CacheMiddleware;
use PHPUnit\Framework\TestCase;

class GuzzleCacheExtensionTest extends TestCase
{
    public function testLoad()
    {
        $container = new ContainerBuilder();

        $extension = new GuzzleCacheExtension();
        $extension->load([], $container);

        $this->assertTrue($container->hasParameter('guzzle_bundle_cache_plugin.middleware.class'));
        $this->assertEquals(
            CacheMiddleware::class,
            $container->getParameter('guzzle_bundle_cache_plugin.middleware.class')
        );
    }
}
