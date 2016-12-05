<?php

namespace UMA\Tests\Psr7Hmac;

use Psr\Http\Message\RequestInterface;
use UMA\Tests\Psr7Hmac\Factory\GuzzleFactory;
use UMA\Tests\Psr7Hmac\Factory\KamboFactory;
use UMA\Tests\Psr7Hmac\Factory\MiBadgerFactory;
use UMA\Tests\Psr7Hmac\Factory\RingCentralFactory;
use UMA\Tests\Psr7Hmac\Factory\SlimFactory;
use UMA\Tests\Psr7Hmac\Factory\SymfonyFactory;
use UMA\Tests\Psr7Hmac\Factory\WanduFactory;
use UMA\Tests\Psr7Hmac\Factory\WindwalkerFactory;
use UMA\Tests\Psr7Hmac\Factory\ZendFactory;

trait RequestsProvider
{
    public function simplestRequestProvider()
    {
        return $this->requests('GET', 'http://www.example.com/index.html');
    }

    public function emptyRequestWithHeadersProvider()
    {
        $headers = [
            'User-Agent' => 'PHP/5.6.21',
            'Accept' => '*/*',
            'Connection' => 'keep-alive',
            'Accept-Encoding' => ['gzip', 'deflate'],
        ];

        return $this->requests('GET', 'http://www.example.com/index.html', $headers);
    }

    public function queryParamsRequestProvider()
    {
        $headers = [
            'Accept' => 'application/json; charset=utf-8',
        ];

        return $this->requests('GET', 'http://www.example.com/search?q=search+term&limit=10&offset=50', $headers);
    }

    public function jsonRequestProvider()
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

        return $this->requests('POST', 'http://www.example.com/api/record.php', $headers, json_encode($body));
    }

    public function simpleFormRequestProvider()
    {
        $headers = [
            'Content-Length' => 51,
            'Content-Type' => 'application/x-www-form-urlencoded; charset=utf-8',
        ];

        $body = 'user=john.doe&password=battery+horse+correct+staple';

        return $this->requests('POST', 'http://www.example.com/login.php', $headers, $body);
    }

    public function binaryRequestProvider()
    {
        $fh = fopen(__DIR__.'/Resources/avatar.png', 'r');

        $headers = [
            'Content-Type' => 'image/png',
            'Content-Length' => 13360,
        ];

        return $this->requests('POST', 'http://www.example.com/avatar/upload.php', $headers, stream_get_contents($fh));
    }

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
            GuzzleFactory::requestClass() => [GuzzleFactory::request($method, $url, $headers, $body)],
            KamboFactory::requestClass() => [KamboFactory::request($method, $url, $headers, $body)],
            MiBadgerFactory::requestClass() => [MiBadgerFactory::request($method, $url, $headers, $body)],
            RingCentralFactory::requestClass() => [RingCentralFactory::request($method, $url, $headers, $body)],
            SlimFactory::requestClass() => [SlimFactory::request($method, $url, $headers, $body)],
            SymfonyFactory::requestClass() => [SymfonyFactory::request($method, $url, $headers, $body)],
            WanduFactory::requestClass() => [WanduFactory::request($method, $url, $headers, $body)],
            WindwalkerFactory::requestClass() => [WindwalkerFactory::request($method, $url, $headers, $body)],
            ZendFactory::requestClass() => [ZendFactory::request($method, $url, $headers, $body)],
        ];
    }
}
