<?php

declare(strict_types=1);

namespace UMA\Tests\Psr7Hmac\Factory;

use Psr\Http\Message\RequestInterface;
use Wandu\Http\Psr\Request;
use Wandu\Http\Psr\Stream;
use Wandu\Http\Psr\Uri;

final class WanduFactory implements FactoryInterface
{
    use StringifierHelper;

    /**
     * {@inheritdoc}
     *
     * @return Request
     */
    public static function request(string $method, string $url, array $headers = [], string $body = null): RequestInterface
    {
        $streamedBody = new Stream('php://memory', 'r+');
        $streamedBody->write($body);

        return new Request($method, new Uri($url), $streamedBody, self::stringify($headers), '1.1');
    }

    /**
     * {@inheritdoc}
     */
    public static function requestClass(): string
    {
        return Request::class;
    }
}
