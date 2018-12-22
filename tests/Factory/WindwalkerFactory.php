<?php

declare(strict_types=1);

namespace UMA\Tests\Psr7Hmac\Factory;

use Psr\Http\Message\RequestInterface;
use Windwalker\Http\Request\Request;
use Windwalker\Http\Stream\Stream;

final class WindwalkerFactory implements FactoryInterface
{
    use StreamHelper;
    use StringifierHelper;

    /**
     * {@inheritdoc}
     *
     * @return Request
     */
    public static function request(string $method, string $url, array $headers = [], string $body = null): RequestInterface
    {
        return new Request($url, $method, new Stream(self::stream($body)), self::stringify($headers));
    }

    /**
     * {@inheritdoc}
     */
    public static function requestClass(): string
    {
        return Request::class;
    }
}
