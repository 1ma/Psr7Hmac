<?php

declare(strict_types=1);

namespace UMA\Tests\Psr7Hmac\Factory;

use miBadger\Http\Request;
use miBadger\Http\Stream;
use miBadger\Http\URI;
use Psr\Http\Message\RequestInterface;

final class MiBadgerFactory implements FactoryInterface
{
    use StreamHelper;

    /**
     * {@inheritdoc}
     *
     * @return Request
     */
    public static function request(string $method, string $url, array $headers = [], string $body = null): RequestInterface
    {
        return new Request($method, new URI($url), Request::DEFAULT_VERSION, $headers, new Stream(self::stream($body)));
    }

    /**
     * {@inheritdoc}
     */
    public static function requestClass(): string
    {
        return Request::class;
    }
}
