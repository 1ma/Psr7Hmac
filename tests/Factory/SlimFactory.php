<?php

namespace UMA\Tests\Psr\Http\Message\Factory;

use Slim\Http\Headers;
use Slim\Http\Request;
use Slim\Http\RequestBody;
use Slim\Http\Response;
use Slim\Http\Uri;

class SlimFactory implements RequestFactoryInterface, ResponseFactoryInterface
{
    /**
     * {@inheritdoc}
     *
     * @return Request
     */
    public function createRequest($method, $url, array $headers = [], $body = null)
    {
        $parseUrl = parse_url($url);

        $uri = new Uri($parseUrl['scheme'], $parseUrl['host'], null, $parseUrl['path']);

        return new Request($method, $uri, new Headers($headers), [], [], new RequestBody());
    }

    /**
     * {@inheritdoc}
     */
    public function requestType()
    {
        return Request::class;
    }

    /**
     * {@inheritdoc}
     *
     * @return Response
     */
    public function createResponse($statusCode, array $headers = [], $body = null)
    {
        return new Response($statusCode, new Headers($headers));
    }

    /**
     * {@inheritdoc}
     */
    public function responseType()
    {
        return Response::class;
    }
}
