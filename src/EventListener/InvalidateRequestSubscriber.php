<?php

namespace Gregurco\Bundle\GuzzleBundleCachePlugin\EventListener;

use Gregurco\Bundle\GuzzleBundleCachePlugin\GuzzleBundleCacheEvents;
use Gregurco\Bundle\GuzzleBundleCachePlugin\Event\InvalidateRequestEvent;
use Kevinrob\GuzzleCache\CacheMiddleware;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class InvalidateRequestSubscriber implements EventSubscriberInterface
{
    /** @var CacheMiddleware */
    private $cacheMiddleware;

    /**
     * @param CacheMiddleware $cacheMiddleware
     */
    public function __construct(CacheMiddleware $cacheMiddleware)
    {
        $this->cacheMiddleware = $cacheMiddleware;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents() : array
    {
        return [
            GuzzleBundleCacheEvents::INVALIDATE => [
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
