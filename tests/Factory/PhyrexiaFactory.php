<?php

namespace UMA\Tests\Psr\Http\Message\Factory;

use Phyrexia\Http\Request;
use Phyrexia\Http\Response;

class PhyrexiaFactory implements RequestFactoryInterface, ResponseFactoryInterface
{
    /**
     * {@inheritdoc}
     *
     * @return Request
     */
    public function createRequest($method, $url, array $headers = [], $body = null)
    {
        return new Request($method, $url, $headers);
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
        return new Response($statusCode, $headers);
    }

    /**
     * {@inheritdoc}
     */
    public function responseType()
    {
        return Response::class;
    }
}
