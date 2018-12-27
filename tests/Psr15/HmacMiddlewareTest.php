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
    public function testMiddlewareHappyPath(): void
    {
        /** @var ServerRequestInterface $signedRequest */
        $signedRequest = (new Signer('6b9550e6b2e7c5041e0ed344b0181fed'))
            ->sign(new ServerRequest('GET', '/data.json', ['X-Api-Key' => '57e00897-3979-4218-aac7-3de4d626f723']));

        $middleware = new HmacMiddleware(
            new HeaderKeyProvider('X-Api-Key'),
            new KeyValueSecretProvider(['57e00897-3979-4218-aac7-3de4d626f723' => '6b9550e6b2e7c5041e0ed344b0181fed']),
            new FakeRequestHandler($this, true)
        );

        $response = $middleware->process(
            $signedRequest,
            new FakeRequestHandler($this, false, 202)
        );

        self::assertSame(202, $response->getStatusCode());
    }

    public function testApiKeyMissingInRequest(): void
    {
        /** @var ServerRequestInterface $signedRequest */
        $signedRequest = (new Signer('6b9550e6b2e7c5041e0ed344b0181fed'))
            ->sign(new ServerRequest('GET', '/data.json'));

        $middleware = new HmacMiddleware(
            new HeaderKeyProvider('X-Api-Key'),
            new KeyValueSecretProvider(['57e00897-3979-4218-aac7-3de4d626f723' => '6b9550e6b2e7c5041e0ed344b0181fed']),
            new FakeRequestHandler($this, false, 400)
        );

        $response = $middleware->process(
            $signedRequest,
            new FakeRequestHandler($this, true)
        );

        self::assertSame(400, $response->getStatusCode());
    }

    public function testApiKeyNotMappedToAnySecret(): void
    {
        /** @var ServerRequestInterface $signedRequest */
        $signedRequest = (new Signer('6b9550e6b2e7c5041e0ed344b0181fed'))
            ->sign(new ServerRequest('GET', '/data.json', ['X-Api-Key' => '57e00897-3979-4218-aac7-3de4d626f723']));

        $middleware = new HmacMiddleware(
            new HeaderKeyProvider('X-Api-Key'),
            new KeyValueSecretProvider([]),
            new FakeRequestHandler($this, false, 401)
        );

        $response = $middleware->process(
            $signedRequest,
            new FakeRequestHandler($this, true)
        );

        self::assertSame(401, $response->getStatusCode());
    }

    public function testBrokenHmacSignature(): void
    {
        /** @var ServerRequestInterface $signedRequest */
        $signedRequest = (new Signer('6b9550e6b2e7c5041e0ed344b0181fed'))
            ->sign(new ServerRequest('GET', '/data.json', ['X-Api-Key' => '57e00897-3979-4218-aac7-3de4d626f723']))
            ->withBody(Stream::create('ouch'));

        $middleware = new HmacMiddleware(
            new HeaderKeyProvider('X-Api-Key'),
            new KeyValueSecretProvider(['57e00897-3979-4218-aac7-3de4d626f723' => '6b9550e6b2e7c5041e0ed344b0181fed']),
            new FakeRequestHandler($this, false, 403)
        );

        $response = $middleware->process(
            $signedRequest,
            new FakeRequestHandler($this, true)
        );

        self::assertSame(403, $response->getStatusCode());
    }
}
