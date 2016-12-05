<?php

namespace UMA\Tests\Psr7Hmac;

use Psr\Http\Message\ResponseInterface;
use UMA\Tests\Psr7Hmac\Factory\GuzzleFactory;
use UMA\Tests\Psr7Hmac\Factory\KamboFactory;
use UMA\Tests\Psr7Hmac\Factory\MiBadgerFactory;
use UMA\Tests\Psr7Hmac\Factory\RingCentralFactory;
use UMA\Tests\Psr7Hmac\Factory\SlimFactory;
use UMA\Tests\Psr7Hmac\Factory\SymfonyFactory;
use UMA\Tests\Psr7Hmac\Factory\WanduFactory;
use UMA\Tests\Psr7Hmac\Factory\WindwalkerFactory;
use UMA\Tests\Psr7Hmac\Factory\ZendFactory;

trait ResponsesProvider
{
    public function simplestResponseProvider()
    {
        return $this->responses(200);
    }

    public function emptyResponseWithHeadersProvider()
    {
        $headers = [
            'Content-Type' => 'text/html',
            'Content-Encoding' => 'gzip',
            'Accept-Ranges' => 'bytes',
            'Content-Length' => '606',
        ];

        return $this->responses(200, $headers);
    }

    public function jsonResponseProvider()
    {
        $headers = [
            'Content-Type' => 'application/json; charset=utf-8',
            'Content-Length' => 134,
        ];

        $body = [
            'employees' => [
                [
                    'firstName' => 'John',
                    'lastName' => 'Doe',
                ],
                [
                    'firstName' => 'Anna',
                    'lastName' => 'Smith',
                ],
                [
                    'firstName' => 'Peter',
                    'lastName' => 'Jones',
                ],
            ],
        ];

        return $this->responses(200, $headers, json_encode($body));
    }

    public function binaryResponseProvider()
    {
        $fh = fopen(__DIR__.'/Resources/avatar.png', 'r');

        $headers = [
            'Content-Type' => 'image/png',
            'Content-Length' => 13360,
        ];

        return $this->responses(200, $headers, stream_get_contents($fh));
    }

    /**
     * @param int         $statusCode
     * @param string[]    $headers
     * @param string|null $body
     *
     * @return ResponseInterface[]
     */
    private function responses($statusCode, array $headers = [], $body = null)
    {
        return [
            GuzzleFactory::responseClass() => [GuzzleFactory::response($statusCode, $headers, $body)],
            KamboFactory::responseClass() => [KamboFactory::response($statusCode, $headers, $body)],
            MiBadgerFactory::responseClass() => [MiBadgerFactory::response($statusCode, $headers, $body)],
            RingCentralFactory::responseClass() => [RingCentralFactory::response($statusCode, $headers, $body)],
            SlimFactory::responseClass() => [SlimFactory::response($statusCode, $headers, $body)],
            SymfonyFactory::responseClass() => [SymfonyFactory::response($statusCode, $headers, $body)],
            WanduFactory::responseClass() => [WanduFactory::response($statusCode, $headers, $body)],
            WindwalkerFactory::responseClass() => [WindwalkerFactory::response($statusCode, $headers, $body)],
            ZendFactory::responseClass() => [ZendFactory::response($statusCode, $headers, $body)],
        ];
    }
}
