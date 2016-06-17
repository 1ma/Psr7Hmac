<?php

namespace UMA\Tests\Psr7Hmac\Factory;

use Wandu\Http\Psr\Request;
use Wandu\Http\Psr\Response;
use Wandu\Http\Psr\Stream;
use Wandu\Http\Psr\Uri;

class WanduFactory implements FactoryInterface
{
    /**
     * {@inheritdoc}
     *
     * @return Request
     */
    public static function request($method, $url, array $headers = [], $body = null)
    {
        $streamedBody = new Stream('php://memory', 'r+');
        $streamedBody->write($body);

        return new Request($method, new Uri($url), '1.1', $headers, $streamedBody);
    }

    /**
     * {@inheritdoc}
     *
     * @return Response
     */
    public static function response($statusCode, array $headers = [], $body = null)
    {
        $streamedBody = new Stream('php://memory', 'r+');
        $streamedBody->write($body);

        return new Response($statusCode, '', '1.1', $headers, $streamedBody);
    }

    /**
     * {@inheritdoc}
     */
    public static function requestClass()
    {
        return Request::class;
    }

    /**
     * {@inheritdoc}
     */
    public static function responseClass()
    {
        return Response::class;
    }
}
