<?php

namespace UMA\Psr\Http\Message\HMAC;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\RequestInterface;
use UMA\Psr\Http\Message\Serializer\MessageSerializer;

class Authenticator
{
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
    public function sign(MessageInterface $message, $secret)
    {
        $preSignedMessage = $message->withHeader(
            Specification::SIGN_HEADER,
            $this->getSignedHeadersString($message)
        );

        $serialization = MessageSerializer::serialize($preSignedMessage);

        return $preSignedMessage->withHeader(
            Specification::AUTH_HEADER,
            Specification::AUTH_PREFIX.' '.$this->doHMACSignature($serialization, $secret)
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
    public function verify(MessageInterface $message, $secret)
    {
        if (0 === preg_match(
            '#^'.Specification::AUTH_PREFIX.' ([+/0-9A-Za-z]{43}=)$#',
            $message->getHeaderLine(Specification::AUTH_HEADER), $matches)
        ) {
            return false;
        }

        $signedHeaders = array_filter(explode(',', $message->getHeaderLine(Specification::SIGN_HEADER)));

        foreach ($message->getHeaders() as $name => $value) {
            if (!in_array(mb_strtolower($name), $signedHeaders)) {
                $message = $message->withoutHeader($name);
            }
        }

        $clientSideSignature = $matches[1];

        $serverSideSignature = $this->doHMACSignature(
            MessageSerializer::serialize($message), $secret
        );

        return hash_equals($serverSideSignature, $clientSideSignature);
    }

    /**
     * @param string $data The string to be signed
     * @param string $key  The secret with which to sign it
     *
     * @return string A base64-encoded SHA256 hash (so it is guaranteed to be 44 bytes long)
     */
    protected function doHMACSignature($data, $key)
    {
        return base64_encode(hash_hmac(Specification::HASH_ALGORITHM, $data, $key, true));
    }

    /**
     * @param MessageInterface $message
     *
     * @return string
     */
    private function getSignedHeadersString(MessageInterface $message)
    {
        $headers = array_keys(array_change_key_case($message->getHeaders(), CASE_LOWER));
        array_push($headers, mb_strtolower(Specification::SIGN_HEADER));

        // Some of the tested RequestInterface implementations do not include
        // the Host header in $message->getHeaders(), so it is explicitly set when needed
        if ($message instanceof RequestInterface && !in_array('host', $headers)) {
            array_push($headers, 'host');
        }

        // There is no guarantee about the order of the headers returned by
        // $message->getHeaders(), so they are explicitly sorted in order
        // to produce the exact same string regardless of the underlying implementation
        sort($headers);

        return implode(',', $headers);
    }
}
