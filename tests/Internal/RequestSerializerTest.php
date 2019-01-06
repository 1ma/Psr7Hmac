<?php

declare(strict_types=1);

namespace UMA\Tests\Psr7Hmac\Internal;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use UMA\Psr7Hmac\Internal\RequestSerializer;
use UMA\Tests\Psr7Hmac\RequestsProvider;

final class RequestSerializerTest extends TestCase
{
    use RequestsProvider;

    /**
     * @dataProvider simplestRequestProvider
     */
    public function testSimplestRequest(RequestInterface $request): void
    {
        $expectedSerialization = "GET /index.html HTTP/1.1\r\nhost: www.example.com\r\n\r\n";

        self::assertSame($expectedSerialization, RequestSerializer::serialize($request));
    }

    /**
     * @dataProvider emptyRequestWithHeadersProvider
     */
    public function testEmptyRequestWithHeaders(RequestInterface $request): void
    {
        $expectedSerialization = "GET /index.html HTTP/1.1\r\nhost: www.example.com\r\naccept: */*\r\naccept-encoding: gzip,deflate\r\nconnection: keep-alive\r\nuser-agent: PHP/5.6.21\r\n\r\n";

        self::assertSame($expectedSerialization, RequestSerializer::serialize($request));
    }

    /**
     * @dataProvider jsonRequestProvider
     */
    public function testJsonRequest(RequestInterface $request): void
    {
        $expectedSerialization = "POST /api/record.php HTTP/1.1\r\nhost: www.example.com\r\ncontent-length: 134\r\ncontent-type: application/json; charset=utf-8\r\n\r\n".'{"employees":[{"firstName":"John","lastName":"Doe"},{"firstName":"Anna","lastName":"Smith"},{"firstName":"Peter","lastName":"Jones"}]}';

        self::assertSame($expectedSerialization, RequestSerializer::serialize($request));
    }

    /**
     * @dataProvider queryParamsRequestProvider
     */
    public function testQueryParamsRequest(RequestInterface $request): void
    {
        $expectedSerialization = "GET /search?limit=10&offset=50&q=search+term HTTP/1.1\r\nhost: www.example.com\r\naccept: application/json; charset=utf-8\r\n\r\n";

        self::assertSame($expectedSerialization, RequestSerializer::serialize($request));
    }

    /**
     * @dataProvider simpleFormRequestProvider
     */
    public function testSimpleFormRequest(RequestInterface $request): void
    {
        $expectedSerialization = "POST /login.php HTTP/1.1\r\nhost: www.example.com\r\ncontent-length: 51\r\ncontent-type: application/x-www-form-urlencoded; charset=utf-8\r\n\r\nuser=john.doe&password=battery+horse+correct+staple";

        self::assertSame($expectedSerialization, RequestSerializer::serialize($request));
    }

    /**
     * @dataProvider binaryRequestProvider
     */
    public function testBinaryRequest(RequestInterface $request): void
    {
        $fh = fopen(__DIR__.'/../resources/avatar.png', 'r+b');

        $expectedSerialization = "POST /avatar/upload.php HTTP/1.1\r\nhost: www.example.com\r\ncontent-length: 13360\r\ncontent-type: image/png\r\n\r\n".stream_get_contents($fh);

        self::assertSame($expectedSerialization, RequestSerializer::serialize($request));
    }
}
