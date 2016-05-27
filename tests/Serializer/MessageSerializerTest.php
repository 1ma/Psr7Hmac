<?php

namespace UMA\Tests\Psr\Http\Message\Serializer;

use Asika\Http\Test\Stub\StubMessage;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use UMA\Psr\Http\Message\Serializer\MessageSerializer;
use UMA\Tests\Psr\Http\Message\RequestsProvider;
use UMA\Tests\Psr\Http\Message\ResponsesProvider;

class MessageSerializerTest extends \PHPUnit_Framework_TestCase
{
    use RequestsProvider;
    use ResponsesProvider;

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

        $this->assertSame($expectedSerialization, MessageSerializer::serialize($request));
    }

    /**
     * @dataProvider simplestResponseProvider
     *
     * @param ResponseInterface $response
     */
    public function testSimplestResponse(ResponseInterface $response)
    {
        $expectedSerialization = "HTTP/1.1 200 OK\r\n\r\n";

        $this->assertSame($expectedSerialization, MessageSerializer::serialize($response));
    }

    /**
     * @dataProvider emptyRequestWithHeadersProvider
     *
     * @param RequestInterface $request
     */
    public function testEmptyRequestWithHeaders(RequestInterface $request)
    {
        $expectedSerialization = "GET /index.html HTTP/1.1\r\nHost: www.example.com\r\nAccept: */*\r\nAccept-Encoding: gzip, deflate\r\nConnection: keep-alive\r\nUser-Agent: PHP/5.6.21\r\n\r\n";

        $this->assertSame($expectedSerialization, MessageSerializer::serialize($request));
    }

    /**
     * @dataProvider emptyResponseWithHeadersProvider
     *
     * @param ResponseInterface $response
     */
    public function testEmptyResponseWithHeaders(ResponseInterface $response)
    {
        $expectedSerialization = "HTTP/1.1 200 OK\r\nAccept-Ranges: bytes\r\nContent-Encoding: gzip\r\nContent-Length: 606\r\nContent-Type: text/html\r\n\r\n";

        $this->assertSame($expectedSerialization, MessageSerializer::serialize($response));
    }

    /**
     * @dataProvider jsonRequestProvider
     *
     * @param RequestInterface $request
     */
    public function testJsonRequest(RequestInterface $request)
    {
        $expectedSerialization = "POST /api/record.php HTTP/1.1\r\nHost: www.example.com\r\nContent-Length: 134\r\nContent-Type: application/json; charset=utf-8\r\n\r\n".'{"employees":[{"firstName":"John","lastName":"Doe"},{"firstName":"Anna","lastName":"Smith"},{"firstName":"Peter","lastName":"Jones"}]}';

        $this->assertSame($expectedSerialization, MessageSerializer::serialize($request));
    }

    /**
     * @dataProvider jsonResponseProvider
     *
     * @param ResponseInterface $response
     */
    public function testJsonResponse(ResponseInterface $response)
    {
        $expectedSerialization = "HTTP/1.1 200 OK\r\nContent-Length: 134\r\nContent-Type: application/json; charset=utf-8\r\n\r\n".'{"employees":[{"firstName":"John","lastName":"Doe"},{"firstName":"Anna","lastName":"Smith"},{"firstName":"Peter","lastName":"Jones"}]}';

        $this->assertSame($expectedSerialization, MessageSerializer::serialize($response));
    }

    /**
     * @dataProvider queryParamsRequestProvider
     *
     * @param RequestInterface $request
     */
    public function testQueryParamsRequest(RequestInterface $request)
    {
        $expectedSerialization = "GET /search?q=search+term&limit=10&offset=50 HTTP/1.1\r\nHost: www.example.com\r\nAccept: application/json; charset=utf-8\r\n\r\n";

        $this->assertSame($expectedSerialization, MessageSerializer::serialize($request));
    }

    /**
     * @dataProvider simpleFormRequestProvider
     *
     * @param RequestInterface $request
     */
    public function testSimpleFormRequest(RequestInterface $request)
    {
        $expectedSerialization = "POST /login.php HTTP/1.1\r\nHost: www.example.com\r\nContent-Length: 51\r\nContent-Type: application/x-www-form-urlencoded; charset=utf-8\r\n\r\nuser=john.doe&password=battery+horse+correct+staple";

        $this->assertSame($expectedSerialization, MessageSerializer::serialize($request));
    }

    /**
     * @dataProvider binaryRequestProvider
     *
     * @param RequestInterface $request
     */
    public function testBinaryRequest(RequestInterface $request)
    {
        $fh = fopen(__DIR__.'/../fixtures/avatar.png', 'r');

        $expectedSerialization = "POST /avatar/upload.php HTTP/1.1\r\nHost: www.example.com\r\nContent-Length: 13360\r\nContent-Type: image/png\r\n\r\n".stream_get_contents($fh);

        $this->assertSame($expectedSerialization, MessageSerializer::serialize($request));
    }

    /**
     * @dataProvider binaryResponseProvider
     *
     * @param ResponseInterface $response
     */
    public function testBinaryResponse(ResponseInterface $response)
    {
        $fh = fopen(__DIR__.'/../fixtures/avatar.png', 'r');

        $expectedSerialization = "HTTP/1.1 200 OK\r\nContent-Length: 13360\r\nContent-Type: image/png\r\n\r\n".stream_get_contents($fh);

        $this->assertSame($expectedSerialization, MessageSerializer::serialize($response));
    }
}
