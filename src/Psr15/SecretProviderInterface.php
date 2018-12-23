<?php

declare(strict_types=1);

namespace UMA\Psr7Hmac\Psr15;

/**
 * Contract for classes that find the secret associated
 * to a given key. The HmacMiddleware will use this secret to
 * verify the incoming request.
 */
interface SecretProviderInterface
{
    /**
     * Retrieves the secret associated to a given key. If no such
     * secret exists the implementer must return null.
     */
    public function getSecretFor(string $key): ?string;
}
