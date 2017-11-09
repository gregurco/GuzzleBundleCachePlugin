<?php

namespace Gregurco\Bundle\GuzzleBundleCachePlugin;

use Gregurco\Bundle\GuzzleBundleCachePlugin\Event\InvalidateRequestEvent;
use Kevinrob\GuzzleCache\CacheMiddleware;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class InvalidateRequestSubscriber implements EventSubscriberInterface
{
    /**
     * @var CacheMiddleware
     */
    private $cacheMiddleware;

    /**
     * InvalidateRequestSubscriber constructor.
     *
     * @param CacheMiddleware $cacheMiddleware
     */
    public function __construct(CacheMiddleware $cacheMiddleware)
    {
        $this->cacheMiddleware = $cacheMiddleware;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            CacheEvents::INVALIDATE => [
                ['invalidate', 0],
            ],
        ];
    }

    /**
     * @param InvalidateRequestEvent $event
     */
    public function invalidate(InvalidateRequestEvent $event)
    {
        $cacheStorage = $this->cacheMiddleware->getCacheStorage();
        $requests     = $event->getRequests();
        array_walk($requests, [$cacheStorage, 'delete']);
    }
}
