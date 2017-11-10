<?php

namespace Gregurco\Bundle\GuzzleBundleCachePlugin;

final class CacheEvents
{
    /**
     * The INVALIDATE event occurs manually.
     *
     * This event allows you to invalidate a response for a request.
     *
     * @Event("Gregurco\Bundle\GuzzleBundleCachePlugin\Event\InvalidateRequestEvent")
     *
     * @var string
     */
    const INVALIDATE = 'guzzle_bundle_cache_plugin.invalidate';
}
