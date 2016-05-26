<?php

namespace UMA\Tests\Psr\Http\Message\Factory;

use RingCentral\Psr7\Request;
use RingCentral\Psr7\Response;

class RingCentralFactory implements RequestFactoryInterface, ResponseFactoryInterface
{
    /**
     * {@inheritdoc}
     *
     * @return Request
     */
    public function createRequest($method, $url, array $headers = [], $body = null)
    {
        return new Request($method, $url, $headers, $body);
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
        return new Response($statusCode, $headers, $body);
    }

    /**
     * {@inheritdoc}
     */
    public function responseType()
    {
        return Response::class;
    }
}
