<?php

namespace UMA;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\RequestInterface;

class HMACAuth
{
    const AUTH_HEADER = 'Authorization';
    const HMAC_ALGO = 'sha256';

    /**
     * @param MessageInterface $message
     * @param string           $secret
     *
     * @return MessageInterface The signed message.
     *
     * @throws \InvalidArgumentException When $message is an implementation of
     *                                   MessageInterface that cannot be
     *                                   serialized and thus neither signed.
     */
    public static function sign(MessageInterface $message, $secret)
    {
        $message = $message
            ->withHeader('Signed-Headers', self::getSignedHeadersList($message));

        return $message
            ->withHeader(
                self::AUTH_HEADER,
                self::calculateHMACSig(MessageSerializer::serialize($message), $secret)
            );
    }

    /**
     * @param MessageInterface $message
     * @param string           $secret
     *
     * @return bool Signature verification outcome.
     *
     * @throws \InvalidArgumentException When $message is an implementation of
     *                                   MessageInterface that cannot be
     *                                   serialized and thus neither verified.
     */
    public static function verify(MessageInterface $message, $secret)
    {
        if (empty($clientSideSignature = $message->getHeaderLine(self::AUTH_HEADER))) {
            return false;
        }

        $serverSideSignature = self::calculateHMACSig(
            MessageSerializer::serialize($message->withoutHeader(self::AUTH_HEADER)),
            $secret
        );

        return hash_equals($serverSideSignature, $clientSideSignature);
    }

    private static function calculateHMACSig($payload, $secret)
    {
        return base64_encode(hash_hmac(self::HMAC_ALGO, $payload, $secret, true));
    }

    private static function getSignedHeadersList(MessageInterface $message)
    {
        $headers = $message->getHeaders();

        // Some of the tested RequestInterface implementations do not include
        // the Host header in $message->getHeaders(), so it is explicitly set
        if ($message instanceof RequestInterface) {
            $headers['Host'] = $message->getUri()->getHost();
        }

        // There is no guarantee about the order of the headers returned by
        // $message->getHeaders(), so they are explicitly sorted in order
        // to get the same signature every time
        ksort($headers);

        return empty(implode('|', array_keys($headers))) ?
            '(none)' : implode('|', array_keys($headers));
    }
}
