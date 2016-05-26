<?php

namespace UMA\Tests\Psr\Http\Message\Serializer;

use Asika\Http\Test\Stub\StubMessage;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use UMA\Psr\Http\Message\Serializer\MessageSerializer;
use UMA\Tests\Psr\Http\Message\MessageProviderTrait;

class MessageSerializerTest extends \PHPUnit_Framework_TestCase
{
    use MessageProviderTrait;

    public function testSerializeNeitherRequestNorResponse()
    {
        $this->setExpectedException(\InvalidArgumentException::class);

        MessageSerializer::serialize(new StubMessage());
    }

    /**
     * @dataProvider simplestRequestProvider
     *
     * @param RequestInterface $request
     */
    public function testSimplestRequest(RequestInterface $request)
    {
        $expectedSerialization = "GET /index.html HTTP/1.1\r\nHost: www.example.com\r\n\r\n";

        $actualSerialization = MessageSerializer::serialize($request);

        $this->assertSame($expectedSerialization, $actualSerialization);
    }

    public function simplestRequestProvider()
    {
        return $this->requests('GET', 'http://www.example.com/index.html');
    }

    /**
     * @dataProvider simplestResponseProvider
     *
     * @param ResponseInterface $response
     */
    public function testSimplestResponse(ResponseInterface $response)
    {
        $expectedSerialization = "HTTP/1.1 200 OK\r\n\r\n";

        $actualSerialization = MessageSerializer::serialize($response);

        $this->assertSame($expectedSerialization, $actualSerialization);
    }

    public function simplestResponseProvider()
    {
        return $this->responses(200);
    }

    /**
     * @dataProvider emptyRequestWithHeadersProvider
     *
     * @param RequestInterface $request
     */
    public function testEmptyRequestWithHeaders(RequestInterface $request)
    {
        $expectedSerialization = "GET /index.html HTTP/1.1\r\nHost: www.example.com\r\nAccept: */*\r\nAccept-Encoding: gzip, deflate\r\nConnection: keep-alive\r\nUser-Agent: PHP/5.6.21\r\n\r\n";

        $actualSerialization = MessageSerializer::serialize($request);

        $this->assertSame($expectedSerialization, $actualSerialization);
    }

    public function emptyRequestWithHeadersProvider()
    {
        $headers = [
            'User-Agent' => 'PHP/5.6.21',
            'Accept' => '*/*',
            'Connection' => 'keep-alive',
            'Accept-Encoding' => 'gzip, deflate',
        ];

        return $this->requests('GET', 'http://www.example.com/index.html', $headers);
    }

    /**
     * @dataProvider emptyResponseWithHeadersProvider
     *
     * @param ResponseInterface $response
     */
    public function testEmptyResponseWithHeaders(ResponseInterface $response)
    {
        $expectedSerialization = "HTTP/1.1 200 OK\r\nAccept-Ranges: bytes\r\nContent-Encoding: gzip\r\nContent-Length: 606\r\nContent-Type: text/html\r\n\r\n";

        $actualSerialization = MessageSerializer::serialize($response);

        $this->assertSame($expectedSerialization, $actualSerialization);
    }

    public function emptyResponseWithHeadersProvider()
    {
        $headers = [
            'Content-Type' => 'text/html',
            'Content-Encoding' => 'gzip',
            'Accept-Ranges' => 'bytes',
            'Content-Length' => '606',
        ];

        return $this->responses(200, $headers);
    }
}
