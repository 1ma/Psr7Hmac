<?php

declare(strict_types=1);

namespace UMA\Tests\Psr7Hmac\Factory;

use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\ServerRequest as NyholmRequest;
use Psr\Http\Message\RequestInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

final class SymfonyFactory implements FactoryInterface
{
    /**
     * {@inheritdoc}
     *
     * @return NyholmRequest
     */
    public static function request(string $method, string $url, array $headers = [], string $body = null): RequestInterface
    {
        $symfonyRequest = SymfonyRequest::create($url, $method, [], [], [], [], $body);

        $symfonyRequest->headers->remove('accept');
        $symfonyRequest->headers->remove('accept-charset');
        $symfonyRequest->headers->remove('accept-language');
        $symfonyRequest->headers->remove('user-agent');

        $symfonyRequest->headers->add($headers);

        $factory = new Psr17Factory();
        return (new PsrHttpFactory($factory, $factory, $factory, $factory))
            ->createRequest($symfonyRequest);
    }

    /**
     * {@inheritdoc}
     */
    public static function requestClass(): string
    {
        // This is indeed a white lie, as the HttpFoundation component
        // is not a PSR-7 implementation.

        // Instead, the returned requests are actually Nyholm\Psr7\ServerRequest
        // instances produced by Symfony's own PSR-7 bridge.
        return SymfonyRequest::class;
    }
}
