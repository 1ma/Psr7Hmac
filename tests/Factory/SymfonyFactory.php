<?php

namespace UMA\Tests\Psr\Http\Message\Factory;

use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Zend\Diactoros\ServerRequest;

class SymfonyFactory implements FactoryInterface
{
    /**
     * {@inheritdoc}
     *
     * @return ServerRequest
     */
    public static function request($method, $url, array $headers = [], $body = null)
    {
        $symfonyRequest = Request::create($url, $method, [], [], [], [], $body);

        $symfonyRequest->headers->remove('accept');
        $symfonyRequest->headers->remove('accept-charset');
        $symfonyRequest->headers->remove('accept-language');
        $symfonyRequest->headers->remove('user-agent');

        $symfonyRequest->headers->add($headers);

        return (new DiactorosFactory())
            ->createRequest($symfonyRequest);
    }

    /**
     * {@inheritdoc}
     *
     * @return \Zend\Diactoros\Response
     */
    public static function response($statusCode, array $headers = [], $body = null)
    {
        $symfonyResponse = Response::create($body, $statusCode, $headers);

        $symfonyResponse->setProtocolVersion('1.1');
        $symfonyResponse->headers->remove('cache-control');
        $symfonyResponse->headers->add($headers);

        return (new DiactorosFactory())
            ->createResponse($symfonyResponse);
    }

    /**
     * {@inheritdoc}
     */
    public static function requestClass()
    {
        // This is indeed a white lie, as the HttpFoundation component
        // is not a PSR-7 implementation.
        //
        // Instead, the returned requests are actually Zend\Diactoros\ServerRequest
        // instances produced by Symfony's own PSR-7 bridge.
        return Request::class;
    }

    /**
     * {@inheritdoc}
     */
    public static function responseClass()
    {
        // Likewise
        return Response::class;
    }
}
