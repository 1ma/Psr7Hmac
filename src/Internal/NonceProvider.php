<?php

namespace UMA\Psr\Http\Message\Internal;

class NonceProvider
{
    const ENTROPY_BITS = 96;

    /**
     * @return string
     */
    public function randomNonce()
    {
        return base64_encode(random_bytes(self::ENTROPY_BITS / 8));
    }
}
