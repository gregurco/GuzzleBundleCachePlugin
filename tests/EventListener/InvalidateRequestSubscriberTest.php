<?php

namespace Gregurco\Bundle\GuzzleBundleCachePlugin\Test\EventListener;

use Gregurco\Bundle\GuzzleBundleCachePlugin\Event\InvalidateRequestEvent;
use Gregurco\Bundle\GuzzleBundleCachePlugin\EventListener\InvalidateRequestSubscriber;
use Guzzle\Http\Message\RequestInterface;
use Guzzle\Plugin\Cache\CacheStorageInterface;
use Kevinrob\GuzzleCache\CacheMiddleware;
use PHPUnit\Framework\TestCase;

class InvalidateRequestSubscriberTest extends TestCase
{
    public function testGeneralUseCase()
    {
        $request = $this->getMockBuilder(RequestInterface::class)->getMock();

        /** @var InvalidateRequestEvent|\PHPUnit_Framework_MockObject_MockObject $invalidateRequestEvent */
        $invalidateRequestEvent = $this->getMockBuilder(InvalidateRequestEvent::class)
            ->disableOriginalConstructor()
            ->getMock();

        $invalidateRequestEvent
            ->expects($this->once())
            ->method('getRequests')
            ->willReturn([$request]);

        $cacheStorage = $this->getMockBuilder(CacheStorageInterface::class)->getMock();
        $cacheStorage->expects($this->once())
            ->method('delete');

        /** @var CacheMiddleware|\PHPUnit_Framework_MockObject_MockObject $cacheMiddlewareMock */
        $cacheMiddlewareMock = $this->getMockBuilder(CacheMiddleware::class)->getMock();
        $cacheMiddlewareMock
            ->expects($this->once())
            ->method('getCacheStorage')
            ->willReturn($cacheStorage);

        $invalidateRequestSubscriber = new InvalidateRequestSubscriber($cacheMiddlewareMock);
        $invalidateRequestSubscriber->invalidate($invalidateRequestEvent);
    }
}
