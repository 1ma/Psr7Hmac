<?php

namespace UMA\Psr\Http\Message\HMAC;

use Psr\Http\Message\MessageInterface;
use UMA\Psr\Http\Message\Internal\HeaderValidator;
use UMA\Psr\Http\Message\Serializer\MessageSerializer;

class Verifier
{
    /**
     * @var Calculator
     */
    private $calculator;

    /**
     * @var HeaderValidator
     */
    private $validator;

    public function __construct()
    {
        $this->calculator = new Calculator();
        $this->validator = (new HeaderValidator())
            ->addRule(Specification::AUTH_HEADER, Specification::AUTH_REGEXP)
            ->addRule(Specification::SIGN_HEADER, Specification::SIGN_REGEXP);
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
        if (false === $matches = $this->validator->conforms($message)) {
            return false;
        }

        $clientSideSignature = $matches[Specification::AUTH_HEADER][1];

        $serverSideSignature = $this->calculator
            ->hmac(MessageSerializer::serialize($this->withoutUnsignedHeaders($message)), $secret);

        return hash_equals($serverSideSignature, $clientSideSignature);
    }

    /**
     * @param MessageInterface $message
     *
     * @return MessageInterface
     */
    private function withoutUnsignedHeaders(MessageInterface $message)
    {
        $signedHeaders = array_filter(explode(',', $message->getHeaderLine(Specification::SIGN_HEADER)));

        foreach ($message->getHeaders() as $name => $value) {
            if (!in_array(mb_strtolower($name), $signedHeaders)) {
                $message = $message->withoutHeader($name);
            }
        }

        return $message;
    }
}
