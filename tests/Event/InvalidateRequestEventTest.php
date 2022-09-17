<?php

namespace Gregurco\Bundle\GuzzleBundleCachePlugin\Test\Event;

use Gregurco\Bundle\GuzzleBundleCachePlugin\Event\InvalidateRequestEvent;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;

class InvalidateRequestEventTest extends TestCase
{
    public function testGeneralUseCase()
    {
        $baseUri = Psr7\Utils::uriFor('http://api.domain.tld');

        /** @var Client|\PHPUnit_Framework_MockObject_MockObject $client */
        $client = $this->getMockBuilder(Client::class)->getMock();
        $client->expects($this->once())
            ->method('getConfig')
            ->with('base_uri')
            ->willReturn($baseUri);

        $invalidateRequestEvent = new InvalidateRequestEvent($client, 'GET', '/ping');
        $this->assertEquals($client, $invalidateRequestEvent->getClient());
        $this->assertEquals('GET', $invalidateRequestEvent->getMethod());
        $this->assertEquals('/ping', $invalidateRequestEvent->getUri());

        $request = $invalidateRequestEvent->getRequest();
        $this->assertInstanceOf(Request::class, $request);
        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('http://api.domain.tld/ping', (string)$request->getUri());
    }

    public function testCaseWhenClientWithoutBaseUri()
    {
        /** @var Client|\PHPUnit_Framework_MockObject_MockObject $client */
        $client = $this->getMockBuilder(Client::class)->getMock();
        $client->expects($this->once())
            ->method('getConfig')
            ->with('base_uri')
            ->willReturn(null);

        $invalidateRequestEvent = new InvalidateRequestEvent($client, 'GET', 'http://api.domain.tld/ping');
        $this->assertEquals($client, $invalidateRequestEvent->getClient());
        $this->assertEquals('GET', $invalidateRequestEvent->getMethod());
        $this->assertEquals('http://api.domain.tld/ping', $invalidateRequestEvent->getUri());

        $request = $invalidateRequestEvent->getRequest();
        $this->assertInstanceOf(Request::class, $request);
        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('http://api.domain.tld/ping', (string)$request->getUri());
    }
}
