<?php

namespace UMA\Tests\Psr\Http\Message\Factory;

use Asika\Http\Request;
use Asika\Http\Response;
use Asika\Http\Stream\Stream;

class AsikaFactory implements FactoryInterface
{
    use StreamTrait;

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
