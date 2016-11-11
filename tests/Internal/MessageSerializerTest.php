<?php

namespace UMA\Tests\Psr7Hmac\Internal;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use UMA\Psr7Hmac\Internal\MessageSerializer;
use UMA\Tests\Psr7Hmac\RequestsProvider;
use UMA\Tests\Psr7Hmac\ResponsesProvider;
use Windwalker\Http\Test\Stub\StubMessage;

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
        $expectedSerialization = "GET /index.html HTTP/1.1\r\nhost: www.example.com\r\n\r\n";

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
        $expectedSerialization = "GET /index.html HTTP/1.1\r\nhost: www.example.com\r\naccept: */*\r\naccept-encoding: gzip, deflate\r\nconnection: keep-alive\r\nuser-agent: PHP/5.6.21\r\n\r\n";

        $this->assertSame($expectedSerialization, MessageSerializer::serialize($request));
    }

    /**
     * @dataProvider emptyResponseWithHeadersProvider
     *
     * @param ResponseInterface $response
     */
    public function testEmptyResponseWithHeaders(ResponseInterface $response)
    {
        $expectedSerialization = "HTTP/1.1 200 OK\r\naccept-ranges: bytes\r\ncontent-encoding: gzip\r\ncontent-length: 606\r\ncontent-type: text/html\r\n\r\n";

        $this->assertSame($expectedSerialization, MessageSerializer::serialize($response));
    }

    /**
     * @dataProvider jsonRequestProvider
     *
     * @param RequestInterface $request
     */
    public function testJsonRequest(RequestInterface $request)
    {
        $expectedSerialization = "POST /api/record.php HTTP/1.1\r\nhost: www.example.com\r\ncontent-length: 134\r\ncontent-type: application/json; charset=utf-8\r\n\r\n".'{"employees":[{"firstName":"John","lastName":"Doe"},{"firstName":"Anna","lastName":"Smith"},{"firstName":"Peter","lastName":"Jones"}]}';

        $this->assertSame($expectedSerialization, MessageSerializer::serialize($request));
    }

    /**
     * @dataProvider jsonResponseProvider
     *
     * @param ResponseInterface $response
     */
    public function testJsonResponse(ResponseInterface $response)
    {
        $expectedSerialization = "HTTP/1.1 200 OK\r\ncontent-length: 134\r\ncontent-type: application/json; charset=utf-8\r\n\r\n".'{"employees":[{"firstName":"John","lastName":"Doe"},{"firstName":"Anna","lastName":"Smith"},{"firstName":"Peter","lastName":"Jones"}]}';

        $this->assertSame($expectedSerialization, MessageSerializer::serialize($response));
    }

    /**
     * @dataProvider queryParamsRequestProvider
     *
     * @param RequestInterface $request
     */
    public function testQueryParamsRequest(RequestInterface $request)
    {
        $expectedSerialization = "GET /search?limit=10&offset=50&q=search+term HTTP/1.1\r\nhost: www.example.com\r\naccept: application/json; charset=utf-8\r\n\r\n";

        $this->assertSame($expectedSerialization, MessageSerializer::serialize($request));
    }

    /**
     * @dataProvider simpleFormRequestProvider
     *
     * @param RequestInterface $request
     */
    public function testSimpleFormRequest(RequestInterface $request)
    {
        $expectedSerialization = "POST /login.php HTTP/1.1\r\nhost: www.example.com\r\ncontent-length: 51\r\ncontent-type: application/x-www-form-urlencoded; charset=utf-8\r\n\r\nuser=john.doe&password=battery+horse+correct+staple";

        $this->assertSame($expectedSerialization, MessageSerializer::serialize($request));
    }

    /**
     * @dataProvider binaryRequestProvider
     *
     * @param RequestInterface $request
     */
    public function testBinaryRequest(RequestInterface $request)
    {
        $fh = fopen(__DIR__.'/../Resources/avatar.png', 'r');

        $expectedSerialization = "POST /avatar/upload.php HTTP/1.1\r\nhost: www.example.com\r\ncontent-length: 13360\r\ncontent-type: image/png\r\n\r\n".stream_get_contents($fh);

        $this->assertSame($expectedSerialization, MessageSerializer::serialize($request));
    }

    /**
     * @dataProvider binaryResponseProvider
     *
     * @param ResponseInterface $response
     */
    public function testBinaryResponse(ResponseInterface $response)
    {
        $fh = fopen(__DIR__.'/../Resources/avatar.png', 'r');

        $expectedSerialization = "HTTP/1.1 200 OK\r\ncontent-length: 13360\r\ncontent-type: image/png\r\n\r\n".stream_get_contents($fh);

        $this->assertSame($expectedSerialization, MessageSerializer::serialize($response));
    }
}
