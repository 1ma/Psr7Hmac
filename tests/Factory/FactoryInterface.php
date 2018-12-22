<?php

declare(strict_types=1);

namespace UMA\Tests\Psr7Hmac\Factory;

use Psr\Http\Message\RequestInterface;

interface FactoryInterface
{
    /**
     * Return a brand new RequestInterface implementation
     */
    public static function request(string $method, string $url, array $headers = [], string $body = null): RequestInterface;

    /**
     * Return the fully qualified class name of the RequestInterface
     * objects produced by the request() method.
     */
    public static function requestClass(): string;
}
