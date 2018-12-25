<?php

declare(strict_types=1);

namespace UMA\Psr7Hmac\Psr15;

final class Errors
{
    /**
     * Error attribute name.
     */
    public const HMAC_ERROR = 'hmac.error';

    /**
     * The KeyProvider could not retrieve a key from
     * the incoming request.
     */
    public const NO_KEY = 0;

    /**
     * The SecretProvider could not find a secret matching
     * the key received in the request (i.e. it is a made up key).
     */
    public const NO_SECRET = 1;

    /**
     * The HMAC signature did not match, therefore the request
     * might have been tampered in-flight or the client is making
     * up the value of the HMAC signature.
     */
    public const BROKEN_SIGNATURE = 2;
}
