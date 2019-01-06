<?php

declare(strict_types=1);

namespace UMA\Tests\Psr7Hmac\Psr15;

use Nyholm\Psr7\ServerRequest;
use Nyholm\Psr7\Stream;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use UMA\Psr7Hmac\Psr15\HmacMiddleware;
use UMA\Psr7Hmac\Signer;
use UMA\Tests\Psr7Hmac\Fixtures\HeaderKeyProvider;
use UMA\Tests\Psr7Hmac\Fixtures\KeyValueSecretProvider;
use UMA\Tests\Psr7Hmac\Fixtures\FakeRequestHandler;

final class HmacMiddlewareTest extends TestCase
{
    private const SAMPLE_API_KEY = '57e00897-3979-4218-aac7-3de4d626f723';
    private const SAMPLE_SECRET = '6b9550e6b2e7c5041e0ed344b0181fed';

    /**
     * @var Signer
     */
    private $signer;

    protected function setUp()
    {
        $this->signer = new Signer(self::SAMPLE_SECRET);
    }

    public function testMiddlewareHappyPath(): void
    {
        /** @var ServerRequestInterface $signedRequest */
        $signedRequest = $this->signer->sign(
            new ServerRequest('GET', '/data.json', ['X-Api-Key' => self::SAMPLE_API_KEY])
        );

        $middleware = new HmacMiddleware(
            new HeaderKeyProvider('X-Api-Key'),
            new KeyValueSecretProvider([self::SAMPLE_API_KEY => self::SAMPLE_SECRET]),
            new FakeRequestHandler(true),
            new FakeRequestHandler(true),
            new FakeRequestHandler(true)
        );

        $response = $middleware->process(
            $signedRequest,
            new FakeRequestHandler(false, 202)
        );

        self::assertSame(202, $response->getStatusCode());
    }

    public function testApiKeyMissingInRequest(): void
    {
        /** @var ServerRequestInterface $signedRequest */
        $signedRequest = $this->signer->sign(
            new ServerRequest('GET', '/data.json')
        );

        $middleware = new HmacMiddleware(
            new HeaderKeyProvider('X-Api-Key'),
            new KeyValueSecretProvider([self::SAMPLE_API_KEY => self::SAMPLE_SECRET]),
            new FakeRequestHandler(false, 400),
            new FakeRequestHandler(true),
            new FakeRequestHandler(true)
        );

        $response = $middleware->process(
            $signedRequest,
            new FakeRequestHandler(true)
        );

        self::assertSame(400, $response->getStatusCode());
    }

    public function testApiKeyNotMappedToAnySecret(): void
    {
        /** @var ServerRequestInterface $signedRequest */
        $signedRequest = $this->signer->sign(
            new ServerRequest('GET', '/data.json', ['X-Api-Key' => self::SAMPLE_API_KEY])
        );

        $middleware = new HmacMiddleware(
            new HeaderKeyProvider('X-Api-Key'),
            new KeyValueSecretProvider([]),
            new FakeRequestHandler(true),
            new FakeRequestHandler(false, 401),
            new FakeRequestHandler(true)
        );

        $response = $middleware->process(
            $signedRequest,
            new FakeRequestHandler(true)
        );

        self::assertSame(401, $response->getStatusCode());
    }

    public function testBrokenHmacSignature(): void
    {
        /** @var ServerRequestInterface $signedRequest */
        $signedRequest = $this->signer->sign(
            new ServerRequest('GET', '/data.json', ['X-Api-Key' => self::SAMPLE_API_KEY])
        )->withBody(Stream::create('ouch'));

        $middleware = new HmacMiddleware(
            new HeaderKeyProvider('X-Api-Key'),
            new KeyValueSecretProvider([self::SAMPLE_API_KEY => self::SAMPLE_SECRET]),
            new FakeRequestHandler(true),
            new FakeRequestHandler(true),
            new FakeRequestHandler(false, 403)
        );

        $response = $middleware->process(
            $signedRequest,
            new FakeRequestHandler(true)
        );

        self::assertSame(403, $response->getStatusCode());
    }
}
