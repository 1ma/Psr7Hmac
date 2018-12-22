<?php

declare(strict_types=1);

namespace UMA\Psr7Hmac\Internal;

use UMA\Psr7Hmac\Specification;

final class HashCalculator
{
    /**
     * @param string $data The string to be signed
     * @param string $key  The secret with which to sign it
     *
     * @return string A base64-encoded SHA256 hash (so it is guaranteed to be 44 bytes long)
     */
    public function hmac(string $data, string $key): string
    {
        return \base64_encode(\hash_hmac(Specification::HASH_ALGORITHM, $data, $key, true));
    }
}
