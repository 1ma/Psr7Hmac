<?php

namespace UMA\Tests\Psr\Http\Message;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use UMA\Tests\Psr\Http\Message\Factory\AsikaFactory;
use UMA\Tests\Psr\Http\Message\Factory\GuzzleFactory;
use UMA\Tests\Psr\Http\Message\Factory\RingCentralFactory;
use UMA\Tests\Psr\Http\Message\Factory\SlimFactory;
use UMA\Tests\Psr\Http\Message\Factory\WanduFactory;
use UMA\Tests\Psr\Http\Message\Factory\ZendFactory;

trait MessageProviderTrait
{
    /**
     * @param string      $method
     * @param string      $url
     * @param string[]    $headers
     * @param string|null $body
     *
     * @return RequestInterface[]
     */
    private function requests($method, $url, array $headers = [], $body = null)
    {
        return [
            AsikaFactory::requestClass() => [AsikaFactory::request($method, $url, $headers, $body)],
            GuzzleFactory::requestClass() => [GuzzleFactory::request($method, $url, $headers, $body)],
            RingCentralFactory::requestClass() => [RingCentralFactory::request($method, $url, $headers, $body)],
            SlimFactory::requestClass() => [SlimFactory::request($method, $url, $headers, $body)],
            WanduFactory::requestClass() => [WanduFactory::request($method, $url, $headers, $body)],
            ZendFactory::requestClass() => [ZendFactory::request($method, $url, $headers, $body)],
        ];
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
            AsikaFactory::responseClass() => [AsikaFactory::response($statusCode, $headers, $body)],
            GuzzleFactory::responseClass() => [GuzzleFactory::response($statusCode, $headers, $body)],
            RingCentralFactory::responseClass() => [RingCentralFactory::response($statusCode, $headers, $body)],
            SlimFactory::responseClass() => [SlimFactory::response($statusCode, $headers, $body)],
            WanduFactory::responseClass() => [WanduFactory::response($statusCode, $headers, $body)],
            ZendFactory::responseClass() => [ZendFactory::response($statusCode, $headers, $body)],
        ];
    }
}
