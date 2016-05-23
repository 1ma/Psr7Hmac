<?php

namespace UMA\Psr\Http\Message\Security;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\RequestInterface;
use UMA\Psr\Http\Message\Serializer\MessageSerializer;

class HMACAuth
{
    const AUTH_HEADER = 'Authorization';
    const AUTH_PREFIX = 'HMAC-SHA256';
    const HMAC_ALGO = 'SHA256';

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
        $preSignedMessage = $message->withHeader('Signed-Headers', self::getSignedHeadersString($message));

        return $preSignedMessage->withHeader(
            self::AUTH_HEADER,
            self::AUTH_PREFIX.' '.self::calculateHMACSig(MessageSerializer::serialize($preSignedMessage), $secret)
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
        if (empty($authHeader = $message->getHeaderLine(self::AUTH_HEADER))) {
            return false;
        }

        if (0 === preg_match('#^'.self::AUTH_PREFIX.' ([+/0-9A-Za-z]{43}=)$#', $authHeader, $matches)) {
            return false;
        }

        $clientSideSignature = $matches[1];

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

    private static function getSignedHeadersString(MessageInterface $message)
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

        return empty(implode(',', array_keys($headers))) ?
            '(none)' : implode(',', array_keys($headers));
    }
}
