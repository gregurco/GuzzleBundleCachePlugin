<?php

namespace Gregurco\Bundle\GuzzleBundleCachePlugin;

use EightPoints\Bundle\GuzzleBundle\PluginInterface;
use Gregurco\Bundle\GuzzleBundleCachePlugin\DependencyInjection\GuzzleCacheExtension;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class GuzzleBundleCachePlugin extends Bundle implements PluginInterface
{
    /**
     * @param array            $configs
     * @param ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $extension = new GuzzleCacheExtension();
        $extension->load($configs, $container);
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     * @param string           $clientName
     * @param Definition       $handler
     */
    public function loadForClient(array $config, ContainerBuilder $container, string $clientName, Definition $handler): void
    {
        if ($config['enabled']) {
            $cacheMiddlewareDefinitionName = sprintf('guzzle_bundle_cache_plugin.middleware.%s', $clientName);
            $cacheMiddlewareDefinition = new Definition('%guzzle_bundle_cache_plugin.middleware.class%');
            $cacheMiddlewareDefinition->setPublic(true);

            if ($config['strategy']) {
                $cacheMiddlewareDefinition->addArgument(new Reference($config['strategy']));
            }

            $container->setDefinition($cacheMiddlewareDefinitionName, $cacheMiddlewareDefinition);

            $cacheMiddlewareExpression = new Expression(sprintf('service("%s")', $cacheMiddlewareDefinitionName));

            $handler->addMethodCall('push', [$cacheMiddlewareExpression, 'cache']);

            $invalidateRequestSubscriberDefinition = $container->getDefinition('guzzle_bundle_cache_plugin.event_subscriber.invalidate_request');
            $invalidateRequestSubscriberDefinition->addMethodCall('addCacheMiddleware', [
                new Reference(sprintf('eight_points_guzzle.client.%s', $clientName)),
                new Reference($cacheMiddlewareDefinitionName)
            ]);
        }
    }

    /**
     * @param ArrayNodeDefinition $pluginNode
     */
    public function addConfiguration(ArrayNodeDefinition $pluginNode): void
    {
        $pluginNode
            ->canBeEnabled()
            ->children()
                ->scalarNode('strategy')->defaultNull()->end()
            ->end()
        ;
    }

    /**
     * @return string
     */
    public function getPluginName(): string
    {
        return 'cache';
    }
}
