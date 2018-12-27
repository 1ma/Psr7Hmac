<?php

declare(strict_types=1);

namespace UMA\Psr7Hmac;

use Psr\Http\Message\RequestInterface;
use UMA\Psr7Hmac\Internal\HashCalculator;
use UMA\Psr7Hmac\Internal\HeaderNameNormalizer;
use UMA\Psr7Hmac\Internal\HeaderValidator;
use UMA\Psr7Hmac\Internal\RequestSerializer;

final class Verifier
{
    /**
     * @var HashCalculator
     */
    private $calculator;

    /**
     * @var HeaderNameNormalizer
     */
    private $normalizer;

    /**
     * @var HeaderValidator
     */
    private $validator;

    public function __construct()
    {
        $this->calculator = new HashCalculator();
        $this->normalizer = new HeaderNameNormalizer();
        $this->validator = (new HeaderValidator())
            ->addRule(Specification::AUTH_HEADER, Specification::AUTH_REGEXP)
            ->addRule(Specification::SIGN_HEADER, Specification::SIGN_REGEXP);
    }

    public function verify(RequestInterface $request, string $secret): bool
    {
        if (false === $matches = $this->validator->conforms($request)) {
            return false;
        }

        $clientSideSignature = $matches[Specification::AUTH_HEADER][1];

        $serverSideSignature = $this->calculator
            ->hmac(RequestSerializer::serialize($this->withoutUnsignedHeaders($request)), $secret);

        return \hash_equals($serverSideSignature, $clientSideSignature);
    }

    private function withoutUnsignedHeaders(RequestInterface $request): RequestInterface
    {
        $signedHeaders = \array_filter(\explode(',', $request->getHeaderLine(Specification::SIGN_HEADER)));

        foreach (\array_keys($request->getHeaders()) as $headerName) {
            if (!\in_array($this->normalizer->normalize($headerName), $signedHeaders, true)) {
                $request = $request->withoutHeader($headerName);
            }
        }

        return $request;
    }
}
