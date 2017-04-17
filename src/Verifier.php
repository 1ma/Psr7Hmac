<?php

namespace UMA\Psr7Hmac;

use Psr\Http\Message\RequestInterface;
use UMA\Psr7Hmac\Inspector\DefaultInspector;
use UMA\Psr7Hmac\Inspector\InspectorInterface;
use UMA\Psr7Hmac\Internal\HashCalculator;
use UMA\Psr7Hmac\Internal\HeaderNameNormalizer;
use UMA\Psr7Hmac\Internal\HeaderValidator;
use UMA\Psr7Hmac\Internal\RequestSerializer;

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
     * @var HeaderNameNormalizer
     */
    private $normalizer;

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
        $this->normalizer = new HeaderNameNormalizer();
        $this->validator = (new HeaderValidator())
            ->addRule(Specification::AUTH_HEADER, Specification::AUTH_REGEXP)
            ->addRule(Specification::SIGN_HEADER, Specification::SIGN_REGEXP);
    }

    /**
     * @param RequestInterface $request
     * @param string           $secret
     *
     * @return bool Signature verification outcome.
     */
    public function verify(RequestInterface $request, $secret)
    {
        if (false === $matches = $this->validator->conforms($request)) {
            return false;
        }

        $clientSideSignature = $matches[Specification::AUTH_HEADER][1];

        $serverSideSignature = $this->calculator
            ->hmac(RequestSerializer::serialize($this->withoutUnsignedHeaders($request)), $secret);

        $vetted = $this->inspector
            ->vet($request, $verified = hash_equals($serverSideSignature, $clientSideSignature));

        return $vetted && $verified;
    }

    /**
     * @param RequestInterface $request
     *
     * @return RequestInterface
     */
    private function withoutUnsignedHeaders(RequestInterface $request)
    {
        $signedHeaders = array_filter(explode(',', $request->getHeaderLine(Specification::SIGN_HEADER)));

        foreach (array_keys($request->getHeaders()) as $headerName) {
            if (!in_array($this->normalizer->normalize($headerName), $signedHeaders)) {
                $request = $request->withoutHeader($headerName);
            }
        }

        return $request;
    }
}
