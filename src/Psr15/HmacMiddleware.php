<?php

declare(strict_types=1);

namespace UMA\Psr7Hmac\Psr15;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use UMA\Psr7Hmac\Specification;
use UMA\Psr7Hmac\Verifier;

final class HmacMiddleware implements MiddlewareInterface
{
    /**
     * @var KeyProviderInterface
     */
    private $keyProvider;

    /**
     * @var SecretProviderInterface
     */
    private $secretProvider;

    /**
     * @var RequestHandlerInterface
     */
    private $unauthenticatedHandler;

    /**
     * @var Verifier
     */
    private $hmacVerifier;

    public function __construct(
        KeyProviderInterface $keyProvider,
        SecretProviderInterface $secretProvider,
        RequestHandlerInterface $unauthenticatedHandler,
        Verifier $hmacVerifier = null
    )
    {
        $this->keyProvider = $keyProvider;
        $this->secretProvider = $secretProvider;
        $this->unauthenticatedHandler = $unauthenticatedHandler;
        $this->hmacVerifier = $hmacVerifier ?? new Verifier();
    }

    /**
     * {@inheritdoc}
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (null === $key = $this->keyProvider->getKeyFrom($request)) {
            return $this->unauthenticatedHandler->handle(
                $request->withAttribute(Specification::HMAC_ERROR, Specification::ERR_NO_KEY)
            );
        }

        if (null === $secret = $this->secretProvider->getSecretFor($key)) {
            return $this->unauthenticatedHandler->handle(
                $request->withAttribute(Specification::HMAC_ERROR, Specification::ERR_NO_SECRET)
            );
        }

        if (false === $this->hmacVerifier->verify($request, $secret)) {
            return $this->unauthenticatedHandler->handle(
                $request->withAttribute(Specification::HMAC_ERROR, Specification::ERR_BROKEN_SIG)
            );
        }

        return $handler->handle($request);
    }
}
