<?php

namespace Gregurco\Bundle\GuzzleBundleCachePlugin\Test\Event;

use Gregurco\Bundle\GuzzleBundleCachePlugin\Event\InvalidateRequestEvent;
use Psr\Http\Message\RequestInterface;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;

class InvalidateRequestEventTest extends TestCase
{
    public function testGeneralUseCase()
    {
        $request = $this->getMockBuilder(RequestInterface::class)->getMock();
        $request->expects($this->once())
            ->method('getUri')
            ->willReturn('http://api.domain.tld');

        $request->expects($this->once())
            ->method('withUri')
            ->willReturnSelf();

        /** @var Client|\PHPUnit_Framework_MockObject_MockObject $client */
        $client = $this->getMockBuilder(Client::class)->getMock();
        $client->expects($this->once())
            ->method('getConfig')
            ->with('base_uri')
            ->willReturn(null);

        $invalidateRequestEvent = new InvalidateRequestEvent($client, [$request]);
        $this->assertEquals($client, $invalidateRequestEvent->getClient());
        $this->assertEquals([$request], $invalidateRequestEvent->getRequests());
    }
    public function testCaseWhenClientHasBaseUri()
    {
        $request = $this->getMockBuilder(RequestInterface::class)->getMock();
        $request->expects($this->once())
            ->method('getUri')
            ->willReturn('http://api.domain.tld');

        $request->expects($this->once())
            ->method('withUri')
            ->willReturnSelf();

        /** @var Client|\PHPUnit_Framework_MockObject_MockObject $client */
        $client = $this->getMockBuilder(Client::class)->getMock();
        $client->expects($this->once())
            ->method('getConfig')
            ->with('base_uri')
            ->willReturn('http://api.domain.tld');

        $invalidateRequestEvent = new InvalidateRequestEvent($client, [$request]);
        $this->assertEquals($client, $invalidateRequestEvent->getClient());
        $this->assertEquals([$request], $invalidateRequestEvent->getRequests());
    }
}
