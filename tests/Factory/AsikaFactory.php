<?php

namespace UMA\Tests\Psr\Http\Message\Factory;

use Asika\Http\Request;
use Asika\Http\Response;

class AsikaFactory implements RequestFactoryInterface, ResponseFactoryInterface
{
    /**
     * {@inheritdoc}
     *
     * @return Request
     */
    public function createRequest($method, $url, array $headers = [], $body = null)
    {
        return new Request($url, $method, 'php://memory', $headers);
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
        return new Response('php://memory', $statusCode, $headers);
    }

    /**
     * {@inheritdoc}
     */
    public function responseType()
    {
        return Response::class;
    }
}
