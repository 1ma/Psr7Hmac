<?php

declare(strict_types=1);

namespace UMA\Tests\Psr7Hmac\Psr15;

use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use UMA\Psr7Hmac\Psr15\HmacMiddleware;
use UMA\Psr7Hmac\Signer;
use UMA\Tests\Psr7Hmac\Fixtures\HeaderKeyProvider;
use UMA\Tests\Psr7Hmac\Fixtures\KeyValueSecretProvider;
use UMA\Tests\Psr7Hmac\Fixtures\FakeRequestHandler;

final class HmacMiddlewareTest extends TestCase
{
    public function testIt()
    {
        $request = new ServerRequest('GET', '/data.json', ['rarara' => 'wololo']);

        $middleware = new HmacMiddleware(
            new HeaderKeyProvider('rarara'),
            new KeyValueSecretProvider(['wololo' => 'sikrit']),
            new FakeRequestHandler(true)
        );

        $signer = new Signer('sikrit');

        $response = $middleware->process(
            $signer->sign($request),
            new FakeRequestHandler(false, 202)
        );

        self::assertSame(202, $response->getStatusCode());
    }
}
