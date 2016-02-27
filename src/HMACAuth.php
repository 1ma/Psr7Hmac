<?php

namespace OneMA;

use Psr\Http\Message\MessageInterface;

class HMACAuth
{
    const AUTH_HEADER = 'Authorization';
    const HMAC_HASH = 'sha256';

    /**
     * @param MessageInterface $message
     * @param string           $secret
     *
     * @return MessageInterface The signed message
     */
    public static function sign(MessageInterface $message, $secret)
    {
        return $message->withHeader(
            self::AUTH_HEADER,
            self::calculateHMACSig(\GuzzleHttp\Psr7\str($message), $secret)
        );
    }

    /**
     * @param MessageInterface $message
     * @param string           $secret
     *
     * @return bool Signature verification outcome
     */
    public static function verify(MessageInterface $message, $secret)
    {
        if (empty($clientSideSignature = $message->getHeaderLine(self::AUTH_HEADER))) {
            return false;
        }

        $serverSideSignature = self::calculateHMACSig(
            \GuzzleHttp\Psr7\str($message->withoutHeader(self::AUTH_HEADER)),
            $secret
        );

        return hash_equals($serverSideSignature, $clientSideSignature);
    }

    private static function calculateHMACSig($payload, $secret)
    {
        return base64_encode(hash_hmac(self::HMAC_HASH, $payload, $secret, true));
    }
}
