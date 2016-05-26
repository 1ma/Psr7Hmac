<?php

namespace UMA\Tests\Psr\Http\Message\Factory;

use Slim\Http\Body;
use Slim\Http\Headers;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\Uri;

class SlimFactory implements FactoryInterface
{
    use StreamTrait;

    /**
     * {@inheritdoc}
     *
     * @return Request
     */
    public static function request($method, $url, array $headers = [], $body = null)
    {
        $parseUrl = parse_url($url);

        $uri = new Uri($parseUrl['scheme'], $parseUrl['host'], null, $parseUrl['path']);

        return new Request($method, $uri, new Headers($headers), [], [], new Body(self::stream($body)));
    }

    /**
     * {@inheritdoc}
     *
     * @return Response
     */
    public static function response($statusCode, array $headers = [], $body = null)
    {
        return new Response($statusCode, new Headers($headers), new Body(self::stream($body)));
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
