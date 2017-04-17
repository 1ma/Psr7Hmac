<?php

namespace UMA\Tests\Psr7Hmac\Factory;

use Psr\Http\Message\RequestInterface;

interface FactoryInterface
{
    /**
     * @param string      $method
     * @param string      $url
     * @param string[]    $headers
     * @param string|null $body
     *
     * @return RequestInterface
     */
    public static function request($method, $url, array $headers = [], $body = null);

    /**
     * @return string
     */
    public static function requestClass();
}
