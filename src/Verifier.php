<?php

namespace UMA\Psr7Hmac;

use Psr\Http\Message\MessageInterface;
use UMA\Psr7Hmac\Inspector\DefaultInspector;
use UMA\Psr7Hmac\Inspector\InspectorInterface;
use UMA\Psr7Hmac\Internal\HashCalculator;
use UMA\Psr7Hmac\Internal\HeaderValidator;
use UMA\Psr7Hmac\Internal\MessageSerializer;

class Verifier
{
    /**
     * @var HashCalculator
     */
    private $calculator;

    /**
     * @var InspectorInterface
     */
    private $inspector;

    /**
     * @var HeaderValidator
     */
    private $validator;

    /**
     * @param InspectorInterface|null $inspector
     */
    public function __construct(InspectorInterface $inspector = null)
    {
        $this->calculator = new HashCalculator();
        $this->inspector = null === $inspector ?
            new DefaultInspector() : $inspector;
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

        $vetted = $this->inspector
            ->vet($message, $verified = hash_equals($serverSideSignature, $clientSideSignature));

        return $vetted && $verified;
    }

    /**
     * @param MessageInterface $message
     *
     * @return MessageInterface
     */
    private function withoutUnsignedHeaders(MessageInterface $message)
    {
        $signedHeaders = array_filter(explode(',', $message->getHeaderLine(Specification::SIGN_HEADER)));

        foreach (array_keys($message->getHeaders()) as $headerName) {
            if (!in_array(mb_strtolower($headerName), $signedHeaders)) {
                $message = $message->withoutHeader($headerName);
            }
        }

        return $message;
    }
}
