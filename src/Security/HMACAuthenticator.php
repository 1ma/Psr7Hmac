<?php

namespace UMA\Psr\Http\Message\Security;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\RequestInterface;
use UMA\Psr\Http\Message\Serializer\MessageSerializer;

class HMACAuthenticator
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
        $preSignedMessage = $message->withHeader('Signed-Headers', $this->getSignedHeadersString($message));

        return $preSignedMessage->withHeader(
            HMACSpecification::AUTH_HEADER,
            HMACSpecification::AUTH_PREFIX.' '.HMACSpecification::doHMACSignature(MessageSerializer::serialize($preSignedMessage), $secret)
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
        if (empty($authHeader = $message->getHeaderLine(HMACSpecification::AUTH_HEADER))) {
            return false;
        }

        if (0 === preg_match('#^'.HMACSpecification::AUTH_PREFIX.' ([+/0-9A-Za-z]{43}=)$#', $authHeader, $matches)) {
            return false;
        }

        $clientSideSignature = $matches[1];

        $serverSideSignature = HMACSpecification::doHMACSignature(
            MessageSerializer::serialize($message->withoutHeader(HMACSpecification::AUTH_HEADER)),
            $secret
        );

        return hash_equals($serverSideSignature, $clientSideSignature);
    }

    /**
     * @param MessageInterface $message
     *
     * @return string
     */
    private function getSignedHeadersString(MessageInterface $message)
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
