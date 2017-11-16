<?php

namespace Gregurco\Bundle\GuzzleBundleCachePlugin\EventListener;

use Gregurco\Bundle\GuzzleBundleCachePlugin\GuzzleBundleCacheEvents;
use Gregurco\Bundle\GuzzleBundleCachePlugin\Event\InvalidateRequestEvent;
use Kevinrob\GuzzleCache\CacheMiddleware;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use GuzzleHttp\Client;

class InvalidateRequestSubscriber implements EventSubscriberInterface
{
    /** @var CacheMiddleware[] */
    protected $cacheMiddlewares;

    /**
     * @param Client $client
     * @param CacheMiddleware $cacheMiddleware
     */
    public function addCacheMiddleware(Client $client, CacheMiddleware $cacheMiddleware)
    {
        $objectIdentifier = $this->getClientIdentifier($client);

        if (!isset($this->cacheMiddlewares[$objectIdentifier])) {
            $this->cacheMiddlewares[$objectIdentifier] = $cacheMiddleware;
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
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
        $client = $event->getClient();
        $objectIdentifier = $this->getClientIdentifier($client);

        if (isset($this->cacheMiddlewares[$objectIdentifier])) {
            $cacheStorage = $this->cacheMiddlewares[$objectIdentifier]->getCacheStorage();
            $cacheStorage->delete($event->getRequest());
        }
    }

    /**
     * @param Client $client
     *
     * @return string
     */
    protected function getClientIdentifier(Client $client): string
    {
        return spl_object_hash($client);
    }

}
