<?php

namespace Gregurco\Bundle\GuzzleBundleCachePlugin\Test\EventListener;

use Gregurco\Bundle\GuzzleBundleCachePlugin\Event\InvalidateRequestEvent;
use Gregurco\Bundle\GuzzleBundleCachePlugin\EventListener\InvalidateRequestSubscriber;
use Gregurco\Bundle\GuzzleBundleCachePlugin\GuzzleBundleCacheEvents;
use GuzzleHttp\Psr7\Request;
use Kevinrob\GuzzleCache\CacheMiddleware;
use Kevinrob\GuzzleCache\Storage\CacheStorageInterface;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;

class InvalidateRequestSubscriberTest extends TestCase
{
    public function testGeneralUseCase()
    {
        $request = $this->getMockBuilder(Request::class)->disableOriginalConstructor()->getMock();

        /** @var Client|\PHPUnit_Framework_MockObject_MockObject $clientMock */
        $clientMock = $this->getMockBuilder(Client::class)->getMock();

        /** @var InvalidateRequestEvent|\PHPUnit_Framework_MockObject_MockObject $invalidateRequestEvent */
        $invalidateRequestEvent = $this->getMockBuilder(InvalidateRequestEvent::class)
            ->disableOriginalConstructor()
            ->getMock();

        $invalidateRequestEvent
            ->expects($this->once())
            ->method('getClient')
            ->willReturn($clientMock);

        $invalidateRequestEvent
            ->expects($this->once())
            ->method('getRequest')
            ->willReturn($request);

        $cacheStorage = $this->getMockBuilder(CacheStorageInterface::class)->getMock();
        $cacheStorage
            ->expects($this->once())
            ->method('delete')
            ->with($request);

        /** @var CacheMiddleware|\PHPUnit_Framework_MockObject_MockObject $cacheMiddlewareMock */
        $cacheMiddlewareMock = $this->getMockBuilder(CacheMiddleware::class)->getMock();
        $cacheMiddlewareMock
            ->expects($this->once())
            ->method('getCacheStorage')
            ->willReturn($cacheStorage);

        $invalidateRequestSubscriber = new InvalidateRequestSubscriber();
        $invalidateRequestSubscriber->addCacheMiddleware($clientMock, $cacheMiddlewareMock);
        $invalidateRequestSubscriber->invalidate($invalidateRequestEvent);
    }

    public function testGetSubscribedEventsResultType()
    {
        $this->assertIsArray(InvalidateRequestSubscriber::getSubscribedEvents());
    }

    public function testGetSubscribedEventsHasInvalidateEvent()
    {
        $this->assertArrayHasKey(
            GuzzleBundleCacheEvents::INVALIDATE,
            InvalidateRequestSubscriber::getSubscribedEvents()
        );
    }
}
