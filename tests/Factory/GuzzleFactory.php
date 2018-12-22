<?php

declare(strict_types=1);

namespace UMA\Tests\Psr7Hmac\Factory;

use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;

final class GuzzleFactory implements FactoryInterface
{
    /**
     * {@inheritdoc}
     *
     * @return Request
     */
    public static function request(string $method, string $url, array $headers = [], string $body = null): RequestInterface
    {
        return new Request($method, $url, $headers, $body);
    }

    /**
     * {@inheritdoc}
     */
    public static function requestClass(): string
    {
        return Request::class;
    }
}
