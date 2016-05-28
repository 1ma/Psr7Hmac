<?php

namespace UMA\Psr\Http\Message\HMAC;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\RequestInterface;
use UMA\Psr\Http\Message\Serializer\MessageSerializer;

class Authenticator
{
    /**
     * @var string
     */
    private $secret;

    /**
     * @param string $secret
     */
    public function __construct($secret)
    {
        $this->secret = $secret;
    }

    /**
     * @param MessageInterface $message
     *
     * @return MessageInterface The signed message.
     *
     * @throws \InvalidArgumentException When $message is an implementation of
     *                                   MessageInterface that cannot be
     *                                   serialized and thus neither signed.
     */
    public function sign(MessageInterface $message)
    {
        $preSignedMessage = $message->withHeader(
            Specification::SIGN_HEADER,
            $this->getSignedHeadersString($message)
        );

        $serialization = MessageSerializer::serialize($preSignedMessage);

        return $preSignedMessage->withHeader(
            Specification::AUTH_HEADER,
            Specification::AUTH_PREFIX.' '.Specification::doHMACSignature($serialization, $this->secret)
        );
    }

    /**
     * @param MessageInterface $message
     *
     * @return bool Signature verification outcome.
     *
     * @throws \InvalidArgumentException When $message is an implementation of
     *                                   MessageInterface that cannot be
     *                                   serialized and thus neither verified.
     */
    public function verify(MessageInterface $message)
    {
        if (empty($authHeader = $message->getHeaderLine(Specification::AUTH_HEADER))) {
            return false;
        }

        if (0 === preg_match('#^'.Specification::AUTH_PREFIX.' ([+/0-9A-Za-z]{43}=)$#', $authHeader, $matches)) {
            return false;
        }

        $coveredHeaders = explode(',', $message->getHeaderLine(Specification::SIGN_HEADER));

        foreach ($message->getHeaders() as $name => $value) {
            if (!in_array(mb_strtolower($name), $coveredHeaders)) {
                $message = $message->withoutHeader($name);
            }
        }

        $clientSideSignature = $matches[1];

        $serverSideSignature = Specification::doHMACSignature(
            MessageSerializer::serialize($message),
            $this->secret
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
