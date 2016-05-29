<?php

namespace UMA\Tests\Psr\Http\Message\HMAC;

use GuzzleHttp\Psr7\Request as GuzzleRequest;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use UMA\Psr\Http\Message\HMAC\Authenticator;
use UMA\Psr\Http\Message\HMAC\Specification;
use UMA\Tests\Psr\Http\Message\RequestsProvider;
use UMA\Tests\Psr\Http\Message\ResponsesProvider;

class AuthenticatorTest extends \PHPUnit_Framework_TestCase
{
    use RequestsProvider;
    use ResponsesProvider;

    const SECRET = '$ecr3t';

    /**
     * @var Authenticator|\PHPUnit_Framework_MockObject_MockObject
     */
    private $auth;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->auth = $this->getMockBuilder(Authenticator::class)
            ->setMethods(['doHMACSignature'])
            ->getMock();
    }

    public function testMissingAuthorizationHeader()
    {
        $request = new GuzzleRequest('GET', 'http://example.com');

        $this->auth
            ->expects($this->never())
            ->method('doHMACSignature');

        $this->assertFalse($this->auth->verify($request, "irrelevant, won't be even checked"));
    }

    public function testBadlyFormattedSignature()
    {
        $request = new GuzzleRequest('GET', 'http://example.com', [Specification::AUTH_HEADER => Specification::AUTH_PREFIX.' herpder=']);

        $this->auth
            ->expects($this->never())
            ->method('doHMACSignature');

        $this->assertFalse($this->auth->verify($request, "irrelevant, won't be even checked"));
    }

    /**
     * @dataProvider simplestRequestProvider
     *
     * @param RequestInterface $request
     */
    public function testSimplestRequest(RequestInterface $request)
    {
        $this->setExpectedSerialization(
            "GET /index.html HTTP/1.1\r\nhost: www.example.com\r\nsigned-headers: host,signed-headers\r\n\r\n"
        );

        $this->inspectSignedMessage($this->auth->sign($request, self::SECRET));
    }

    /**
     * @dataProvider simplestResponseProvider
     *
     * @param ResponseInterface $response
     */
    public function testSimplestResponse(ResponseInterface $response)
    {
        $this->setExpectedSerialization(
            "HTTP/1.1 200 OK\r\nsigned-headers: signed-headers\r\n\r\n"
        );

        $this->inspectSignedMessage($this->auth->sign($response, self::SECRET));
    }

    /**
     * @dataProvider emptyRequestWithHeadersProvider
     *
     * @param RequestInterface $request
     */
    public function testEmptyRequestWithHeaders(RequestInterface $request)
    {
        $this->setExpectedSerialization(
            "GET /index.html HTTP/1.1\r\nhost: www.example.com\r\naccept: */*\r\naccept-encoding: gzip, deflate\r\nconnection: keep-alive\r\nsigned-headers: accept,accept-encoding,connection,host,signed-headers,user-agent\r\nuser-agent: PHP/5.6.21\r\n\r\n"
        );

        $this->inspectSignedMessage($this->auth->sign($request, self::SECRET));
    }

    /**
     * @dataProvider emptyResponseWithHeadersProvider
     *
     * @param ResponseInterface $response
     */
    public function testEmptyResponseWithHeaders(ResponseInterface $response)
    {
        $this->setExpectedSerialization(
            "HTTP/1.1 200 OK\r\naccept-ranges: bytes\r\ncontent-encoding: gzip\r\ncontent-length: 606\r\ncontent-type: text/html\r\nsigned-headers: accept-ranges,content-encoding,content-length,content-type,signed-headers\r\n\r\n"
        );

        $this->inspectSignedMessage($this->auth->sign($response, self::SECRET));
    }

    /**
     * @dataProvider jsonRequestProvider
     *
     * @param RequestInterface $request
     */
    public function testJsonRequest(RequestInterface $request)
    {
        $this->setExpectedSerialization(
            "POST /api/record.php HTTP/1.1\r\nhost: www.example.com\r\ncontent-length: 134\r\ncontent-type: application/json; charset=utf-8\r\nsigned-headers: content-length,content-type,host,signed-headers\r\n\r\n{\"employees\":[{\"firstName\":\"John\",\"lastName\":\"Doe\"},{\"firstName\":\"Anna\",\"lastName\":\"Smith\"},{\"firstName\":\"Peter\",\"lastName\":\"Jones\"}]}"
        );

        $this->inspectSignedMessage($this->auth->sign($request, self::SECRET));
    }

    /**
     * @dataProvider jsonResponseProvider
     *
     * @param ResponseInterface $response
     */
    public function testJsonResponse(ResponseInterface $response)
    {
        $this->setExpectedSerialization(
            "HTTP/1.1 200 OK\r\ncontent-length: 134\r\ncontent-type: application/json; charset=utf-8\r\nsigned-headers: content-length,content-type,signed-headers\r\n\r\n{\"employees\":[{\"firstName\":\"John\",\"lastName\":\"Doe\"},{\"firstName\":\"Anna\",\"lastName\":\"Smith\"},{\"firstName\":\"Peter\",\"lastName\":\"Jones\"}]}"
        );

        $this->inspectSignedMessage($this->auth->sign($response, self::SECRET));
    }

    /**
     * @dataProvider queryParamsRequestProvider
     *
     * @param RequestInterface $request
     */
    public function testQueryParamsRequest(RequestInterface $request)
    {
        $this->setExpectedSerialization(
            "GET /search?limit=10&offset=50&q=search+term HTTP/1.1\r\nhost: www.example.com\r\naccept: application/json; charset=utf-8\r\nsigned-headers: accept,host,signed-headers\r\n\r\n"
        );

        $this->inspectSignedMessage($this->auth->sign($request, self::SECRET));
    }

    /**
     * @dataProvider simpleFormRequestProvider
     *
     * @param RequestInterface $request
     */
    public function testSimpleFormRequest(RequestInterface $request)
    {
        $this->setExpectedSerialization(
            "POST /login.php HTTP/1.1\r\nhost: www.example.com\r\ncontent-length: 51\r\ncontent-type: application/x-www-form-urlencoded; charset=utf-8\r\nsigned-headers: content-length,content-type,host,signed-headers\r\n\r\nuser=john.doe&password=battery+horse+correct+staple"
        );

        $this->inspectSignedMessage($this->auth->sign($request, self::SECRET));
    }

    /**
     * @dataProvider binaryRequestProvider
     *
     * @param RequestInterface $request
     */
    public function testBinaryRequest(RequestInterface $request)
    {
        $fh = fopen(__DIR__.'/../resources/avatar.png', 'r');

        $this->setExpectedSerialization(
            "POST /avatar/upload.php HTTP/1.1\r\nhost: www.example.com\r\ncontent-length: 13360\r\ncontent-type: image/png\r\nsigned-headers: content-length,content-type,host,signed-headers\r\n\r\n".stream_get_contents($fh)
        );

        $this->inspectSignedMessage($this->auth->sign($request, self::SECRET));
    }

    /**
     * @dataProvider binaryResponseProvider
     *
     * @param ResponseInterface $response
     */
    public function testBinaryResponse(ResponseInterface $response)
    {
        $fh = fopen(__DIR__.'/../resources/avatar.png', 'r');

        $this->setExpectedSerialization(
            "HTTP/1.1 200 OK\r\ncontent-length: 13360\r\ncontent-type: image/png\r\nsigned-headers: content-length,content-type,signed-headers\r\n\r\n".stream_get_contents($fh)
        );

        $this->inspectSignedMessage($this->auth->sign($response, self::SECRET));
    }

    /**
     * @param string $serialization
     */
    private function setExpectedSerialization($serialization)
    {
        $this->auth
            ->expects($this->once())
            ->method('doHMACSignature')
            ->with($serialization, self::SECRET)
            ->will($this->returnCallback(function ($data, $key) {
                $method = new \ReflectionMethod(Authenticator::class, 'doHMACSignature');
                $method->setAccessible(true);

                return $method->invoke(new Authenticator(self::SECRET), $data, $key);
            }));
    }

    /**
     * @param MessageInterface $signedMessage
     */
    private function inspectSignedMessage(MessageInterface $signedMessage)
    {
        $nonMockedAuth = new Authenticator();

        $this->assertTrue($signedMessage->hasHeader(Specification::AUTH_HEADER));
        $this->assertTrue($signedMessage->hasHeader(Specification::SIGN_HEADER));
        $this->assertTrue($nonMockedAuth->verify($signedMessage, self::SECRET));

        $this->assertFalse((new Authenticator())->verify($signedMessage, 'an0ther $ecr3t'));

        $modifiedMessage = $signedMessage->withHeader('X-Foo', 'Bar');
        $this->assertTrue($nonMockedAuth->verify($modifiedMessage, self::SECRET));

        $tamperByModifying = $signedMessage->withHeader(Specification::SIGN_HEADER, 'tampered,signed-headers,list');
        $this->assertFalse($nonMockedAuth->verify($tamperByModifying, self::SECRET));

        $tamperByDeleting = $signedMessage->withoutHeader(Specification::SIGN_HEADER);
        $this->assertFalse($nonMockedAuth->verify($tamperByDeleting, self::SECRET));
    }
}
