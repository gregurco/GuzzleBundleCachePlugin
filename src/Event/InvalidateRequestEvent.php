<?php

namespace Gregurco\Bundle\GuzzleBundleCachePlugin\Event;

use Psr\Http\Message\UriInterface;
use Symfony\Contracts\EventDispatcher\Event;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Psr7\Request;

class InvalidateRequestEvent extends Event
{
    /** @var Client */
    protected $client;

    /** @var string */
    protected $method;

    /** @var string */
    protected $uri;

    /**
     * @param Client $client
     * @param string $method
     * @param string $uri
     */
    public function __construct(Client $client, string $method, string $uri)
    {
        $this->client = $client;
        $this->method = $method;
        $this->uri = $uri;
    }

    /**
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return string
     */
    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        $baseUri = $this->client->getConfig('base_uri');

        if ($baseUri instanceof UriInterface) {
            $uri = Psr7\UriResolver::resolve($baseUri, Psr7\Utils::uriFor($this->uri));
        } else {
            $uri = $this->uri;
        }

        return new Request($this->method, $uri);
    }
}
