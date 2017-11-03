<?php

namespace Gregurco\Bundle\GuzzleBundleCachePlugin\Test;

use EightPoints\Bundle\GuzzleBundle\EightPointsGuzzleBundlePlugin;
use Gregurco\Bundle\GuzzleBundleCachePlugin\GuzzleBundleCachePlugin;
use Kevinrob\GuzzleCache\CacheMiddleware;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use PHPUnit\Framework\TestCase;

class GuzzleBundleCachePluginTest extends TestCase
{
    /** @var GuzzleBundleCachePlugin */
    protected $plugin;

    public function setUp()
    {
        parent::setUp();

        $this->plugin = new GuzzleBundleCachePlugin();
    }

    public function testSubClassesOfPlugin()
    {
        $this->assertInstanceOf(EightPointsGuzzleBundlePlugin::class, $this->plugin);
        $this->assertInstanceOf(Bundle::class, $this->plugin);
    }

    public function testAddConfiguration()
    {
        $arrayNode = new ArrayNodeDefinition('node');

        $this->plugin->addConfiguration($arrayNode);

        $node = $arrayNode->getNode();

        $this->assertFalse($node->isRequired());
        $this->assertTrue($node->hasDefaultValue());
        $this->assertSame(
            ['enabled' => false, 'strategy' => null],
            $node->getDefaultValue()
        );
    }

    public function testGetPluginName()
    {
        $this->assertEquals('cache', $this->plugin->getPluginName());
    }

    public function testLoad()
    {
        $container = new ContainerBuilder();

        $this->plugin->load([], $container);

        $this->assertTrue($container->hasParameter('guzzle_bundle_cache_plugin.middleware.class'));
        $this->assertEquals(
            CacheMiddleware::class,
            $container->getParameter('guzzle_bundle_cache_plugin.middleware.class')
        );
    }

    public function testLoadForClientWithNoStrategy()
    {
        $handler = new Definition();
        $container = new ContainerBuilder();

        $this->plugin->loadForClient(
            ['enabled' => true, 'strategy' => null],
            $container, 'api_payment', $handler
        );

        $this->assertTrue($container->hasDefinition('guzzle_bundle_cache_plugin.middleware.api_payment'));
        $this->assertCount(1, $handler->getMethodCalls());
        $this->assertTrue(isset($handler->getMethodCalls()[0][1][1]), 'No name provided for middleware');
        $this->assertEquals('cache', $handler->getMethodCalls()[0][1][1]);

        $clientMiddlewareDefinition = $container->getDefinition('guzzle_bundle_cache_plugin.middleware.api_payment');
        $this->assertCount(0, $clientMiddlewareDefinition->getArguments());
    }

    public function testLoadForClientWithWrongStrategy()
    {
        $this->expectException(ParameterNotFoundException::class);

        $handler = new Definition();
        $container = new ContainerBuilder();

        $this->plugin->loadForClient(
            ['enabled' => true, 'strategy' => 'fakeStrategyServiceId'],
            $container, 'api_payment', $handler
        );

        $this->assertTrue($container->hasDefinition('guzzle_bundle_cache_plugin.middleware.api_payment'));
        $container->compile();
    }
}
