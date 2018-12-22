<?php

declare(strict_types=1);

namespace UMA\Tests\Psr7Hmac\Factory;

use Psr\Http\Message\RequestInterface;
use Zend\Diactoros\Request;
use Zend\Diactoros\Stream;

class ZendFactory implements FactoryInterface
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
