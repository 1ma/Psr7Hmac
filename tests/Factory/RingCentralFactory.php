<?php

namespace UMA\Tests\Psr7Hmac\Factory;

use RingCentral\Psr7\Request;
use RingCentral\Psr7\Response;

class RingCentralFactory implements FactoryInterface
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
     *
     * @return Response
     */
    public static function response($statusCode, array $headers = [], $body = null)
    {
        return new Response($statusCode, $headers, $body);
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
