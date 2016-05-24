<?php

namespace UMA\Tests\Psr\Http\Message\Serializer;

use UMA\Psr\Http\Message\Serializer\MessageSerializer;
use UMA\Tests\Psr\Http\Message\BaseTestCase;

class ResponseSerializationTest extends BaseTestCase
{
    public function testSimpleResponses()
    {
        $expectedSerialization = "HTTP/1.1 200 OK\r\n\r\n";

        foreach ($this->psr7ResponseShotgun(200) as $response) {
            $actualSerialization = MessageSerializer::serialize($response);

            $this->assertSame($expectedSerialization, $actualSerialization);
        }
    }

    public function testHeadedResponses()
    {
        $expectedSerialization = "HTTP/1.1 200 OK\r\nAccept-Ranges: bytes\r\nContent-Encoding: gzip\r\nContent-Length: 606\r\nContent-Type: text/html\r\n\r\n";

        $requests = $this->psr7ResponseShotgun(200, [
            'Content-Type' => 'text/html',
            'Content-Encoding' => 'gzip',
            'Accept-Ranges' => 'bytes',
            'Content-Length' => '606',
        ]);

        foreach ($requests as $request) {
            $actualSerialization = MessageSerializer::serialize($request);

            $this->assertSame($expectedSerialization, $actualSerialization);
        }
    }
}
