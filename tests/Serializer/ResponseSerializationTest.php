<?php

namespace UMA\Tests\Psr\Http\Message\Serializer;

use UMA\Psr\Http\Message\Serializer\MessageSerializer;
use UMA\Tests\Psr\Http\Message\AbstractTestCase;

class ResponseSerializationTest extends AbstractTestCase
{
    /**
     * @dataProvider responsesProvider
     *
     * @param int      $statusCode
     * @param string[] $headers
     * @param string   $expectedSerialization
     */
    public function testResponses($statusCode, array $headers, $expectedSerialization)
    {
        foreach (self::$responseProvider->shotgun($statusCode, $headers, null) as $request) {
            $actualSerialization = MessageSerializer::serialize($request);

            $this->assertSame($expectedSerialization, $actualSerialization, get_class($request));
        }
    }

    public function responsesProvider()
    {
        return [
            'simple responses' => [
                200,
                [],
                "HTTP/1.1 200 OK\r\n\r\n",
            ],

            'headed responses' => [
                200,
                [
                    'Content-Type' => 'text/html',
                    'Content-Encoding' => 'gzip',
                    'Accept-Ranges' => 'bytes',
                    'Content-Length' => '606',
                ],
                "HTTP/1.1 200 OK\r\nAccept-Ranges: bytes\r\nContent-Encoding: gzip\r\nContent-Length: 606\r\nContent-Type: text/html\r\n\r\n",
            ],
        ];
    }
}
