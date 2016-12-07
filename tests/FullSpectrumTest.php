<?php

namespace UMA\Tests\Psr7Hmac;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use UMA\Psr7Hmac\Internal\HashCalculator;
use UMA\Psr7Hmac\Signer;
use UMA\Psr7Hmac\Specification;
use UMA\Psr7Hmac\Verifier;

class FullSpectrumTest extends \PHPUnit_Framework_TestCase
{
    use ReflectionUtil;
    use RequestsProvider;
    use ResponsesProvider;

    const SECRET = '$ecr3t';

    /**
     * @var HashCalculator|\PHPUnit_Framework_MockObject_MockObject
     */
    private $calculator;

    /**
     * @var Signer
     */
    private $signer;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->calculator = $this->getMockBuilder(HashCalculator::class)
            ->setMethods(['hmac'])
            ->getMock();

        $this->replaceInstanceProperty($this->signer = new Signer(self::SECRET), 'calculator', $this->calculator);
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

        $this->inspectSignedMessage($this->signer->sign($request));
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

        $this->inspectSignedMessage($this->signer->sign($response));
    }

    /**
     * @dataProvider emptyRequestWithHeadersProvider
     *
     * @param RequestInterface $request
     */
    public function testEmptyRequestWithHeaders(RequestInterface $request)
    {
        $this->setExpectedSerialization(
            "GET /index.html HTTP/1.1\r\nhost: www.example.com\r\naccept: */*\r\naccept-encoding: gzip,deflate\r\nconnection: keep-alive\r\nsigned-headers: accept,accept-encoding,connection,host,signed-headers,user-agent\r\nuser-agent: PHP/5.6.21\r\n\r\n"
        );

        $this->inspectSignedMessage($this->signer->sign($request));
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

        $this->inspectSignedMessage($this->signer->sign($response));
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

        $this->inspectSignedMessage($this->signer->sign($request));
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

        $this->inspectSignedMessage($this->signer->sign($response));
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

        $this->inspectSignedMessage($this->signer->sign($request));
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

        $this->inspectSignedMessage($this->signer->sign($request));
    }

    /**
     * @dataProvider binaryRequestProvider
     *
     * @param RequestInterface $request
     */
    public function testBinaryRequest(RequestInterface $request)
    {
        $fh = fopen(__DIR__.'/Resources/avatar.png', 'r');

        $this->setExpectedSerialization(
            "POST /avatar/upload.php HTTP/1.1\r\nhost: www.example.com\r\ncontent-length: 13360\r\ncontent-type: image/png\r\nsigned-headers: content-length,content-type,host,signed-headers\r\n\r\n".stream_get_contents($fh)
        );

        $this->inspectSignedMessage($this->signer->sign($request));
    }

    /**
     * @dataProvider binaryResponseProvider
     *
     * @param ResponseInterface $response
     */
    public function testBinaryResponse(ResponseInterface $response)
    {
        $fh = fopen(__DIR__.'/Resources/avatar.png', 'r');

        $this->setExpectedSerialization(
            "HTTP/1.1 200 OK\r\ncontent-length: 13360\r\ncontent-type: image/png\r\nsigned-headers: content-length,content-type,signed-headers\r\n\r\n".stream_get_contents($fh)
        );

        $this->inspectSignedMessage($this->signer->sign($response));
    }

    /**
     * @param string $serialization
     */
    private function setExpectedSerialization($serialization)
    {
        $this->calculator
            ->expects($this->once())
            ->method('hmac')
            ->with($serialization, self::SECRET)
            ->will($this->returnCallback(function ($data, $key) {
                return (new HashCalculator())->hmac($data, $key);
            }));
    }

    /**
     * @param MessageInterface $signedMessage
     */
    private function inspectSignedMessage(MessageInterface $signedMessage)
    {
        $freshVerifier = new Verifier();

        $this->assertTrue($signedMessage->hasHeader(Specification::AUTH_HEADER));
        $this->assertTrue($signedMessage->hasHeader(Specification::SIGN_HEADER));

        $this->assertTrue($freshVerifier->verify($signedMessage, self::SECRET));

        $this->assertFalse($freshVerifier->verify($signedMessage, 'an0ther $ecr3t'));

        $withAddedHeader = $signedMessage->withHeader('X-Foo', 'Bar');
        $this->assertTrue($freshVerifier->verify($withAddedHeader, self::SECRET));

        $tamperedSignedHeader = $signedMessage->withHeader(Specification::SIGN_HEADER, 'tampered,signed-headers,list');
        $this->assertFalse($freshVerifier->verify($tamperedSignedHeader, self::SECRET));

        $deletedSignedHeader = $signedMessage->withoutHeader(Specification::SIGN_HEADER);
        $this->assertFalse($freshVerifier->verify($deletedSignedHeader, self::SECRET));
    }
}
