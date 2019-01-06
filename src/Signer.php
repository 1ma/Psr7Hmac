<?php

declare(strict_types=1);

namespace UMA\Psr7Hmac;

use Psr\Http\Message\RequestInterface;
use UMA\Psr7Hmac\Internal\HashCalculator;
use UMA\Psr7Hmac\Internal\RequestSerializer;

final class Signer
{
    /**
     * @var string
     */
    private $secret;

    public function __construct(string $secret)
    {
        $this->secret = $secret;
    }

    public function sign(RequestInterface $request): RequestInterface
    {
        $serialization = RequestSerializer::serialize(
            $preSignedMessage = $this->withSignedHeadersHeader($request)
        );

        return $preSignedMessage->withHeader(
            Specification::AUTH_HEADER,
            Specification::AUTH_PREFIX.HashCalculator::hmac($serialization, $this->secret)
        );
    }

    private function withSignedHeadersHeader(RequestInterface $request): RequestInterface
    {
        $headers = \array_keys(\array_change_key_case($request->getHeaders(), CASE_LOWER));
        $headers[] = \mb_strtolower(Specification::SIGN_HEADER);

        // Some of the tested RequestInterface implementations do not include
        // the Host header in $message->getHeaders(), so it is explicitly set when needed
        if (!\in_array('host', $headers, true)) {
            $headers[] = 'host';
        }

        // There is no guarantee about the order of the headers returned by
        // $message->getHeaders(), so they are explicitly sorted in order
        // to produce the exact same string regardless of the underlying implementation
        \sort($headers);

        return $request->withHeader(Specification::SIGN_HEADER, \implode(',', $headers));
    }
}
