<?php

namespace UMA\Tests\Psr\Http\Message\Factory;

use Wandu\Http\Psr\Request;
use Wandu\Http\Psr\Response;
use Wandu\Http\Psr\Stream;
use Wandu\Http\Psr\Uri;

class WanduFactory implements RequestFactoryInterface, ResponseFactoryInterface
{
    /**
     * {@inheritdoc}
     *
     * @return Request
     */
    public function createRequest($method, $url, array $headers = [], $body = null)
    {
        $streamedBody = new Stream('php://memory', 'r+');
        $streamedBody->write($body);

        return new Request('GET', new Uri($url), '1.1', $headers, $streamedBody);
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
        $streamedBody = new Stream('php://memory', 'r+');
        $streamedBody->write($body);

        return new Response($statusCode, '', '1.1', $headers, $streamedBody);
    }

    /**
     * {@inheritdoc}
     */
    public function responseType()
    {
        return Response::class;
    }
}
