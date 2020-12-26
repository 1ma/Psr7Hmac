<?php

declare(strict_types=1);

namespace UMA\Tests\Psr7Hmac;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use UMA\Psr7Hmac\Signer;
use UMA\Psr7Hmac\Specification;
use UMA\Psr7Hmac\Verifier;

final class VerifierTest extends TestCase
{
    private const SECRET = '$ecr3t';

    use RequestsProvider;

    /**
     * @var Verifier
     */
    private $verifier;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->verifier = new Verifier();
    }

    /**
     * @dataProvider simplestRequestProvider
     */
    public function testMissingAuthorizationHeader(RequestInterface $request): void
    {
        $request = (new Signer(self::SECRET))
            ->sign($request)
            ->withoutHeader(Specification::AUTH_HEADER);

        self::assertFalse($this->verifier->verify($request, "irrelevant, won't be even checked"));
    }

    /**
     * @dataProvider simplestRequestProvider
     */
    public function testMissingSignedHeadersHeader(RequestInterface $request): void
    {
        $request = (new Signer(self::SECRET))
            ->sign($request)
            ->withoutHeader(Specification::SIGN_HEADER);

        self::assertFalse($this->verifier->verify($request, "irrelevant, won't be even checked"));
    }

    /**
     * @dataProvider simplestRequestProvider
     */
    public function testBadlyFormattedSignature(RequestInterface $request): void
    {
        $request = (new Signer(self::SECRET))
            ->sign($request)
            ->withHeader(Specification::AUTH_HEADER, Specification::AUTH_PREFIX.'herpder=');

        self::assertFalse($this->verifier->verify($request, "irrelevant, won't be even checked"));
    }
}
