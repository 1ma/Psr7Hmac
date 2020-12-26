<?php

declare(strict_types=1);

namespace UMA\Tests\Psr7Hmac;

use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use UMA\Psr7Hmac\Signer;
use UMA\Psr7Hmac\Specification;
use UMA\Psr7Hmac\Verifier;

final class FullSpectrumTest extends TestCase
{
    use RequestsProvider;

    private const SECRET = '$ecr3t';

    /**
     * @var Signer
     */
    private $signer;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->signer = new Signer(self::SECRET);
    }

    /**
     * @dataProvider simplestRequestProvider
     */
    public function testSimplestRequest(RequestInterface $request): void
    {
        $this->inspectSignedRequest($this->signer->sign($request));
    }

    /**
     * @dataProvider emptyRequestWithHeadersProvider
     */
    public function testEmptyRequestWithHeaders(RequestInterface $request): void
    {
        $this->inspectSignedRequest($this->signer->sign($request));
    }

    /**
     * @dataProvider jsonRequestProvider
     */
    public function testJsonRequest(RequestInterface $request): void
    {
        $this->inspectSignedRequest($this->signer->sign($request));
    }

    /**
     * @dataProvider queryParamsRequestProvider
     */
    public function testQueryParamsRequest(RequestInterface $request): void
    {
        $this->inspectSignedRequest($this->signer->sign($request));
    }

    /**
     * @dataProvider simpleFormRequestProvider
     */
    public function testSimpleFormRequest(RequestInterface $request): void
    {
        $this->inspectSignedRequest($this->signer->sign($request));
    }

    /**
     * @dataProvider binaryRequestProvider
     */
    public function testBinaryRequest(RequestInterface $request): void
    {
        $this->inspectSignedRequest($this->signer->sign($request));
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
