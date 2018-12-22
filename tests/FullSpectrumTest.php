<?php

declare(strict_types=1);

namespace UMA\Tests\Psr7Hmac;

use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use UMA\Psr7Hmac\Internal\HashCalculator;
use UMA\Psr7Hmac\Signer;
use UMA\Psr7Hmac\Specification;
use UMA\Psr7Hmac\Verifier;

final class FullSpectrumTest extends TestCase
{
    use ReflectionUtil;
    use RequestsProvider;

    private const SECRET = '$ecr3t';

    /**
     * @var HashCalculator|MockObject
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
     */
    public function testSimplestRequest(RequestInterface $request): void
    {
        $this->setExpectedSerialization(
            "GET /index.html HTTP/1.1\r\nhost: www.example.com\r\nsigned-headers: host,signed-headers\r\n\r\n"
        );

        $this->inspectSignedRequest($this->signer->sign($request));
    }

    /**
     * @dataProvider emptyRequestWithHeadersProvider
     */
    public function testEmptyRequestWithHeaders(RequestInterface $request): void
    {
        $this->setExpectedSerialization(
            "GET /index.html HTTP/1.1\r\nhost: www.example.com\r\naccept: */*\r\naccept-encoding: gzip,deflate\r\nconnection: keep-alive\r\nsigned-headers: accept,accept-encoding,connection,host,signed-headers,user-agent\r\nuser-agent: PHP/5.6.21\r\n\r\n"
        );

        $this->inspectSignedRequest($this->signer->sign($request));
    }

    /**
     * @dataProvider jsonRequestProvider
     */
    public function testJsonRequest(RequestInterface $request): void
    {
        $this->setExpectedSerialization(
            "POST /api/record.php HTTP/1.1\r\nhost: www.example.com\r\ncontent-length: 134\r\ncontent-type: application/json; charset=utf-8\r\nsigned-headers: content-length,content-type,host,signed-headers\r\n\r\n{\"employees\":[{\"firstName\":\"John\",\"lastName\":\"Doe\"},{\"firstName\":\"Anna\",\"lastName\":\"Smith\"},{\"firstName\":\"Peter\",\"lastName\":\"Jones\"}]}"
        );

        $this->inspectSignedRequest($this->signer->sign($request));
    }

    /**
     * @dataProvider queryParamsRequestProvider
     */
    public function testQueryParamsRequest(RequestInterface $request): void
    {
        $this->setExpectedSerialization(
            "GET /search?limit=10&offset=50&q=search+term HTTP/1.1\r\nhost: www.example.com\r\naccept: application/json; charset=utf-8\r\nsigned-headers: accept,host,signed-headers\r\n\r\n"
        );

        $this->inspectSignedRequest($this->signer->sign($request));
    }

    /**
     * @dataProvider simpleFormRequestProvider
     */
    public function testSimpleFormRequest(RequestInterface $request): void
    {
        $this->setExpectedSerialization(
            "POST /login.php HTTP/1.1\r\nhost: www.example.com\r\ncontent-length: 51\r\ncontent-type: application/x-www-form-urlencoded; charset=utf-8\r\nsigned-headers: content-length,content-type,host,signed-headers\r\n\r\nuser=john.doe&password=battery+horse+correct+staple"
        );

        $this->inspectSignedRequest($this->signer->sign($request));
    }

    /**
     * @dataProvider binaryRequestProvider
     */
    public function testBinaryRequest(RequestInterface $request): void
    {
        $fh = fopen(__DIR__.'/resources/avatar.png', 'r');

        $this->setExpectedSerialization(
            "POST /avatar/upload.php HTTP/1.1\r\nhost: www.example.com\r\ncontent-length: 13360\r\ncontent-type: image/png\r\nsigned-headers: content-length,content-type,host,signed-headers\r\n\r\n".stream_get_contents($fh)
        );

        $this->inspectSignedRequest($this->signer->sign($request));
    }

    private function setExpectedSerialization(string $serialization): void
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
     * @throws ExpectationFailedException
     */
    private function inspectSignedRequest(RequestInterface $signedRequest): void
    {
        $freshVerifier = new Verifier();

        self::assertTrue($signedRequest->hasHeader(Specification::AUTH_HEADER));
        self::assertTrue($signedRequest->hasHeader(Specification::SIGN_HEADER));

        self::assertTrue($freshVerifier->verify($signedRequest, self::SECRET));

        self::assertFalse($freshVerifier->verify($signedRequest, 'an0ther $ecr3t'));

        $withAddedHeader = $signedRequest->withHeader('X-Foo', 'Bar');
        self::assertTrue($freshVerifier->verify($withAddedHeader, self::SECRET));

        $tamperedSignedHeader = $signedRequest->withHeader(Specification::SIGN_HEADER, 'tampered,signed-headers,list');
        self::assertFalse($freshVerifier->verify($tamperedSignedHeader, self::SECRET));

        $deletedSignedHeader = $signedRequest->withoutHeader(Specification::SIGN_HEADER);
        self::assertFalse($freshVerifier->verify($deletedSignedHeader, self::SECRET));
    }
}
