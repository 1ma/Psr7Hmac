<?php

declare(strict_types=1);

namespace UMA\Tests\Psr7Hmac\Factory;

use Nyholm\Psr7\Request as NyholmRequest;
use Psr\Http\Message\RequestInterface;

final class NyholmFactory implements FactoryInterface
{

    /**
     * {@inheritdoc}
     *
     * @return NyholmRequest
     */
    public static function request(string $method, string $url, array $headers = [], string $body = null): RequestInterface
    {
        return new NyholmRequest($method, $url, $headers, $body, '1.1');
    }

    /**
     * {@inheritdoc}
     */
    public static function requestClass(): string
    {
        return NyholmRequest::class;
    }
}
