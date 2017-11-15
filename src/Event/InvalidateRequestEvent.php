<?php

namespace Gregurco\Bundle\GuzzleBundleCachePlugin\Event;

use GuzzleHttp\Client;
use Symfony\Component\EventDispatcher\Event;

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
}
