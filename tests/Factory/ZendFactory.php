<?php

namespace UMA\Tests\Psr\Http\Message\Factory;

use Zend\Diactoros\Request;
use Zend\Diactoros\Response;
use Zend\Diactoros\Stream;

class ZendFactory implements RequestFactoryInterface, ResponseFactoryInterface
{
    use StreamTrait;

    /**
     * {@inheritdoc}
     *
     * @return Request
     */
    public function createRequest($method, $url, array $headers = [], $body = null)
    {
        $streamedBody = $this->createStream($body);

        return new Request($url, $method, new Stream($streamedBody), $headers);
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

        return new Response(new Stream($streamedBody), $statusCode, $headers);
    }

    /**
     * {@inheritdoc}
     */
    public function responseType()
    {
        return Response::class;
    }
}
