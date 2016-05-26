<?php

namespace UMA\Tests\Psr\Http\Message\Serializer;

use UMA\Psr\Http\Message\Serializer\MessageSerializer;
use UMA\Tests\Psr\Http\Message\AbstractTestCase;

class RequestSerializationTest extends AbstractTestCase
{
    /**
     * @dataProvider requestsProvider
     *
     * @param string   $method
     * @param string   $url
     * @param string[] $headers
     * @param string   $expectedSerialization
     */
    public function testRequests($method, $url, array $headers, $expectedSerialization)
    {
        foreach (self::$requestProvider->shotgun($method, $url, $headers, null) as $request) {
            $actualSerialization = MessageSerializer::serialize($request);

            $this->assertSame($expectedSerialization, $actualSerialization, get_class($request));
        }
    }

    public function requestsProvider()
    {
        return [
            'simple requests' => [
                'GET',
                'http://www.example.com/index.html',
                [],
                "GET /index.html HTTP/1.1\r\nHost: www.example.com\r\n\r\n",
            ],

            'headed requests' => [
                'GET',
                'http://www.example.com/index.html',
                [
                    'User-Agent' => 'PHP/5.6.21',
                    'Accept' => '*/*',
                    'Connection' => 'keep-alive',
                    'Accept-Encoding' => 'gzip, deflate',
                ],
                "GET /index.html HTTP/1.1\r\nHost: www.example.com\r\nAccept: */*\r\nAccept-Encoding: gzip, deflate\r\nConnection: keep-alive\r\nUser-Agent: PHP/5.6.21\r\n\r\n",
            ],
        ];
    }
}