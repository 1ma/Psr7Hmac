<?php

namespace UMA\Tests\Psr7Hmac\Factory;

use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Zend\Diactoros\ServerRequest as ZendRequest;

class SymfonyFactory implements FactoryInterface
{
    /**
     * {@inheritdoc}
     *
     * @return ZendRequest
     */
    public static function request($method, $url, array $headers = [], $body = null)
    {
        $symfonyRequest = SymfonyRequest::create($url, $method, [], [], [], [], $body);

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
     */
    public static function requestClass()
    {
        // This is indeed a white lie, as the HttpFoundation component
        // is not a PSR-7 implementation.

        // Instead, the returned requests are actually Zend\Diactoros\ServerRequest
        // instances produced by Symfony's own PSR-7 bridge.
        return SymfonyRequest::class;
    }
}
