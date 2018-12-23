<?php

declare(strict_types=1);

namespace UMA\Psr7Hmac\Psr15;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Contract for classes that extract a key from the
 * HTTP request. The HmacMiddleware will use this key to
 * find the appropriate secret for verifying that very same request.
 */
interface KeyProviderInterface
{
    /**
     * If the key is not present in the request the implementer
     * must return null.
     */
    public function getKeyFrom(ServerRequestInterface $request): ?string;
}
