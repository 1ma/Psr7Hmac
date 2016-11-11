<?php

namespace UMA\Tests\Psr7Hmac\Factory;

use Windwalker\Http\Request\Request;
use Windwalker\Http\Response\Response;
use Windwalker\Http\Stream\Stream;

class WindwalkerFactory implements FactoryInterface
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
     *
     * @return Response
     */
    public static function response($statusCode, array $headers = [], $body = null)
    {
        return new Response(new Stream(self::stream($body)), $statusCode, $headers);
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
