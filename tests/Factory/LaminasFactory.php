<?php

declare(strict_types=1);

namespace UMA\Tests\Psr7Hmac\Factory;

use Laminas\Diactoros\Request;
use Laminas\Diactoros\Stream;
use Psr\Http\Message\RequestInterface;

class LaminasFactory implements FactoryInterface
{
    use StreamHelper;

    /**
     * {@inheritdoc}
     *
     * @return Request
     */
    public static function request(string $method, string $url, array $headers = [], string $body = null): RequestInterface
    {
        return new Request($url, $method, new Stream(self::stream($body)), $headers);
    }

    /**
     * {@inheritdoc}
     */
    public static function requestClass(): string
    {
        return Request::class;
    }
}
