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
        $expectedSerialization = "GET /foo HTTP/1.1\r\nHost: example.com\r\n\r\n";
        $actualSerialization = MessageSerializer::serialize($request);

        $this->assertSame($expectedSerialization, $actualSerialization);
    }

    public function simpleRequestsProvider()
    {
        return [
            [new \Asika\Http\Request('http://example.com/foo', 'GET')],
            [new \GuzzleHttp\Psr7\Request('GET', 'http://example.com/foo')],
            [new \Phyrexia\Http\Request('GET', 'http://example.com/foo')],
            [new \RingCentral\Psr7\Request('GET', 'http://example.com/foo')],
            [new \Slim\Http\Request(
                'GET',
                new \Slim\Http\Uri('http', 'example.com', null, '/foo'),
                new \Slim\Http\Headers(),
                [],
                [],
                new \Slim\Http\RequestBody())],
            [new \Wandu\Http\Psr\Request('GET', new \Wandu\Http\Psr\Uri('http://example.com/foo'))],
            [new \Zend\Diactoros\Request('http://example.com/foo', 'GET')],
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
