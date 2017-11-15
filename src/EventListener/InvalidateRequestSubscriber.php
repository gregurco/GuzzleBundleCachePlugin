<?php

namespace Gregurco\Bundle\GuzzleBundleCachePlugin\EventListener;

use Gregurco\Bundle\GuzzleBundleCachePlugin\GuzzleBundleCacheEvents;
use Gregurco\Bundle\GuzzleBundleCachePlugin\Event\InvalidateRequestEvent;
use Psr\Http\Message\UriInterface;
use Kevinrob\GuzzleCache\CacheMiddleware;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7;

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
    public static function getSubscribedEvents() : array
    {
        return [
            GuzzleBundleCacheEvents::INVALIDATE => 'invalidate',
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

            $request = $this->buildRequest(
                $event->getMethod(),
                $client->getConfig('base_uri'),
                $event->getUri()
            );

            $cacheStorage->delete($request);
        }
    }

    /**
     * @param string $method
     * @param UriInterface|null $baseUri
     * @param string $requestUri
     *
     * @return Request
     */
    protected function buildRequest(string $method, $baseUri, string $requestUri) : Request
    {
        if ($baseUri instanceof UriInterface) {
            $uri = Psr7\UriResolver::resolve($baseUri, Psr7\uri_for($requestUri));
        } else {
            $uri = $requestUri;
        }

        return new Request($method, $uri);
    }

    /**
     * @param Client $client
     *
     * @return string
     */
    protected function getClientIdentifier(Client $client) : string
    {
        return spl_object_hash($client);
    }

}
