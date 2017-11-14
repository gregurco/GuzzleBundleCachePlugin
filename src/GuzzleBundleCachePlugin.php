<?php

namespace Gregurco\Bundle\GuzzleBundleCachePlugin;

use EightPoints\Bundle\GuzzleBundle\EightPointsGuzzleBundlePlugin;
use Gregurco\Bundle\GuzzleBundleCachePlugin\DependencyInjection\GuzzleCacheExtension;
use Gregurco\Bundle\GuzzleBundleCachePlugin\EventDispatcher\NullEventDispatcher;
use Gregurco\Bundle\GuzzleBundleCachePlugin\EventListener\InvalidateRequestSubscriber;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class GuzzleBundleCachePlugin extends Bundle implements EightPointsGuzzleBundlePlugin
{
    /**
     * @param array            $configs
     * @param ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container)
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
    public function loadForClient(array $config, ContainerBuilder $container, string $clientName, Definition $handler)
    {
        $eventDispatcherName = sprintf('guzzle_bundle_cache_plugin.event_dispatcher.%s', $clientName);

        $eventDispatcherClass = $config['enabled'] ? EventDispatcher::class : NullEventDispatcher::class;

        $eventDispatcherDefinition = new Definition($eventDispatcherClass);
        $eventDispatcherDefinition
            ->setPublic(true);
        $container->setDefinition($eventDispatcherName, $eventDispatcherDefinition);

        if ($config['enabled']) {
            $cacheMiddlewareDefinitionName = sprintf('guzzle_bundle_cache_plugin.middleware.%s', $clientName);
            $cacheMiddlewareDefinition     = new Definition('%guzzle_bundle_cache_plugin.middleware.class%');

            if ($config['strategy']) {
                $cacheMiddlewareDefinition->addArgument(new Reference($config['strategy']));
            }

            $container->setDefinition($cacheMiddlewareDefinitionName, $cacheMiddlewareDefinition);

            $cacheMiddlewareExpression = new Expression(sprintf('service("%s")', $cacheMiddlewareDefinitionName));

            $handler->addMethodCall('push', [$cacheMiddlewareExpression, 'cache']);

            $invalidateRequestSubscriberName       = sprintf('guzzle_bundle_cache_plugin.event_subscriber.invalidate_%s', $clientName);
            $invalidateRequestSubscriberDefinition = new Definition(InvalidateRequestSubscriber::class);
            $invalidateRequestSubscriberDefinition
                ->addArgument(new Reference($cacheMiddlewareDefinitionName))
                ->addTag(sprintf('guzzle_bundle_cache_plugin.event_subscriber.%s', $clientName))
            ;
            $container->setDefinition($invalidateRequestSubscriberName, $invalidateRequestSubscriberDefinition);

            $registerListenerPass = new RegisterListenersPass(
                $eventDispatcherName,
                sprintf('guzzle_bundle_cache_plugin.event_listener.%s', $clientName),
                sprintf('guzzle_bundle_cache_plugin.event_subscriber.%s', $clientName)
            );

            $registerListenerPass->process($container);
        }
    }

    /**
     * @param ArrayNodeDefinition $pluginNode
     */
    public function addConfiguration(ArrayNodeDefinition $pluginNode)
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
