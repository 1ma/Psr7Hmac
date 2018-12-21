<?php

namespace UMA\Tests\Psr7Hmac\Factory;

use Wandu\Http\Psr\Request;
use Wandu\Http\Psr\Stream;
use Wandu\Http\Psr\Uri;

class WanduFactory implements FactoryInterface
{
    use StringifierHelper;

    /**
     * {@inheritdoc}
     *
     * @return Request
     */
    public static function request($method, $url, array $headers = [], $body = null)
    {
        $streamedBody = new Stream('php://memory', 'r+');
        $streamedBody->write($body);

        return new Request($method, new Uri($url), $streamedBody, self::stringify($headers), '1.1');
    }

    /**
     * {@inheritdoc}
     */
    public static function requestClass()
    {
        return Request::class;
    }
}
