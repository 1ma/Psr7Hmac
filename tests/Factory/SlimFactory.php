<?php

namespace UMA\Tests\Psr\Http\Message\Factory;

use Slim\Http\Body;
use Slim\Http\Headers;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\Uri;

class SlimFactory implements RequestFactoryInterface, ResponseFactoryInterface
{
    use StreamTrait;

    /**
     * {@inheritdoc}
     *
     * @return Request
     */
    public function createRequest($method, $url, array $headers = [], $body = null)
    {
        $parseUrl = parse_url($url);

        $uri = new Uri($parseUrl['scheme'], $parseUrl['host'], null, $parseUrl['path']);

        $streamedBody = $this->createStream($body);

        return new Request($method, $uri, new Headers($headers), [], [], new Body($streamedBody));
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
        $streamedBody = $this->createStream($body);

        return new Response($statusCode, new Headers($headers), new Body($streamedBody));
    }

    /**
     * {@inheritdoc}
     */
    public function responseType()
    {
        return Response::class;
    }
}
