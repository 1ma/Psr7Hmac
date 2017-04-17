<?php

namespace UMA\Tests\Psr7Hmac\Factory;

use Windwalker\Http\Request\Request;
use Windwalker\Http\Stream\Stream;

class WindwalkerFactory implements FactoryInterface
{
    use StreamHelper;
    use StringifierHelper;

    /**
     * {@inheritdoc}
     *
     * @return Request
     */
    public static function request($method, $url, array $headers = [], $body = null)
    {
        return new Request($url, $method, new Stream(self::stream($body)), self::stringify($headers));
    }

    /**
     * {@inheritdoc}
     */
    public static function requestClass()
    {
        return Request::class;
    }
}
