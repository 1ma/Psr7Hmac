<?php

declare(strict_types=1);

namespace UMA\Psr7Hmac\Psr15;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
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
    private $noKeyHandler;

    /**
     * @var RequestHandlerInterface
     */
    private $noSecretHandler;

    /**
     * @var RequestHandlerInterface
     */
    private $badSigHandler;

    /**
     * @var Verifier
     */
    private $hmacVerifier;

    public function __construct(
        KeyProviderInterface $keyProvider,
        SecretProviderInterface $secretProvider,
        RequestHandlerInterface $noKeyHandler,
        RequestHandlerInterface $noSecretHandler,
        RequestHandlerInterface $badSigHandler
    )
    {
        $this->keyProvider = $keyProvider;
        $this->secretProvider = $secretProvider;
        $this->noKeyHandler = $noKeyHandler;
        $this->noSecretHandler = $noSecretHandler;
        $this->badSigHandler = $badSigHandler;
        $this->hmacVerifier = new Verifier();
    }

    /**
     * {@inheritdoc}
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (null === $key = $this->keyProvider->getKeyFrom($request)) {
            return $this->noKeyHandler->handle($request);
        }

        if (null === $secret = $this->secretProvider->getSecretFor($key)) {
            return $this->noSecretHandler->handle($request);
        }

        if (false === $this->hmacVerifier->verify($request, $secret)) {
            return $this->badSigHandler->handle($request);
        }

        return $handler->handle($request);
    }
}
