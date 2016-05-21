<?php

namespace UMA\Tests;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use UMA\MessageSerializer;

class MessageSerializerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider simpleRequestsProvider
     */
    public function testSimpleRequests(RequestInterface $request)
    {
        $expectedSerialization = "GET / HTTP/1.1\r\nHost: example.com\r\n\r\n";
        $actualSerialization = MessageSerializer::serialize($request);

        $this->assertSame($expectedSerialization, $actualSerialization);
    }

    public function simpleRequestsProvider()
    {
        return [
            // [new \Asika\Http\Request('http://example.com', 'GET')],      // Broken, reported at https://github.com/asika32764/http/issues/2
            [new \GuzzleHttp\Psr7\Request('GET', 'http://example.com')],
            [new \Phyrexia\Http\Request('GET', 'http://example.com')],
            [new \RingCentral\Psr7\Request('GET', 'http://example.com')],
            [new \Slim\Http\Request(
                'GET',
                new \Slim\Http\Uri('http', 'example.com'),
                new \Slim\Http\Headers(),
                [],
                [],
                new \Slim\Http\RequestBody())],
            [new \Wandu\Http\Psr\Request('GET', new \Wandu\Http\Psr\Uri('http://example.com'))],
            // [new \Zend\Diactoros\Request('http://example.com', 'GET')],  // Broken, reported at https://github.com/zendframework/zend-diactoros/issues/172
        ];
    }

    /**
     * @dataProvider simpleResponsesProvider
     */
    public function testSimpleResponses(ResponseInterface $response)
    {
        $expectedSerialization = "HTTP/1.1 200 OK\r\n\r\n";
        $actualSerialization = MessageSerializer::serialize($response);

        $this->assertSame($expectedSerialization, $actualSerialization);
    }

    public function simpleResponsesProvider()
    {
        return [
            [new \Asika\Http\Response()],
            [new \GuzzleHttp\Psr7\Response()],
            [new \Phyrexia\Http\Response()],
            [new \RingCentral\Psr7\Response()],
            [new \Slim\Http\Response()],
            [new \Wandu\Http\Psr\Response()],
            [new \Zend\Diactoros\Response()],
        ];
    }

    public function testSerializeNeitherRequestNorResponse()
    {
        $this->setExpectedException(\InvalidArgumentException::class);

        MessageSerializer::serialize(new \Asika\Http\Test\Stub\StubMessage());
    }
}
