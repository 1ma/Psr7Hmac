<?php

namespace UMA\Psr7Hmac;

use Psr\Http\Message\RequestInterface;
use UMA\Psr7Hmac\Internal\HashCalculator;
use UMA\Psr7Hmac\Internal\RequestSerializer;

class Signer
{
    /**
     * @var string
     */
    private $secret;

    /**
     * @var HashCalculator
     */
    private $calculator;

    /**
     * @param string $secret
     */
    public function __construct($secret)
    {
        $this->secret = $secret;
        $this->calculator = new HashCalculator();
    }

    /**
     * @param RequestInterface $request
     *
     * @return RequestInterface The signed request.
     */
    public function sign(RequestInterface $request)
    {
        $serialization = RequestSerializer::serialize(
            $preSignedMessage = $this->withSignedHeadersHeader($request)
        );

        return $preSignedMessage->withHeader(
            Specification::AUTH_HEADER,
            Specification::AUTH_PREFIX.$this->calculator->hmac($serialization, $this->secret)
        );
    }

    /**
     * @param RequestInterface $request
     *
     * @return RequestInterface
     */
    private function withSignedHeadersHeader(RequestInterface $request)
    {
        $headers = array_keys(array_change_key_case($request->getHeaders(), CASE_LOWER));
        array_push($headers, mb_strtolower(Specification::SIGN_HEADER));

        // Some of the tested RequestInterface implementations do not include
        // the Host header in $message->getHeaders(), so it is explicitly set when needed
        if ($request instanceof RequestInterface && !in_array('host', $headers)) {
            array_push($headers, 'host');
        }

        // There is no guarantee about the order of the headers returned by
        // $message->getHeaders(), so they are explicitly sorted in order
        // to produce the exact same string regardless of the underlying implementation
        sort($headers);

        return $request->withHeader(Specification::SIGN_HEADER, implode(',', $headers));
    }
}
