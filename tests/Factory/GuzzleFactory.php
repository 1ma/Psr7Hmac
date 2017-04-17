<?php

namespace UMA\Tests\Psr7Hmac\Factory;

use GuzzleHttp\Psr7\Request;

class GuzzleFactory implements FactoryInterface
{
    /**
     * {@inheritdoc}
     *
     * @return Request
     */
    public static function request($method, $url, array $headers = [], $body = null)
    {
        return new Request($method, $url, $headers, $body);
    }

    /**
     * {@inheritdoc}
     */
    public static function requestClass()
    {
        return Request::class;
    }
}
