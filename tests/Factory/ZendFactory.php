<?php

namespace UMA\Tests\Psr7Hmac\Factory;

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
    public static function request($method, $url, array $headers = [], $body = null)
    {
        return new Request($url, $method, new Stream(self::stream($body)), $headers);
    }

    /**
     * {@inheritdoc}
     */
    public static function requestClass()
    {
        return Request::class;
    }
}
