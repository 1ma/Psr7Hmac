<?php

namespace UMA\Tests;

use UMA\MessageSerializer;

class MessageSerializerTest extends BaseTestCase
{
    public function testSimpleRequests()
    {
        $expectedSerialization = "GET /index.html HTTP/1.1\r\nHost: www.example.com\r\n\r\n";

        foreach ($this->psr7RequestShotgun('GET', 'http://www.example.com/index.html') as $request) {
            $actualSerialization = MessageSerializer::serialize($request);

            $this->assertSame($expectedSerialization, $actualSerialization);
        }
    }

    public function testSimpleResponses()
    {
        $expectedSerialization = "HTTP/1.1 200 OK\r\n\r\n";

        foreach ($this->psr7ResponseShotgun(200) as $response) {
            $actualSerialization = MessageSerializer::serialize($response);

            $this->assertSame($expectedSerialization, $actualSerialization);
        }
    }

    public function testSerializeNeitherRequestNorResponse()
    {
        $this->setExpectedException(\InvalidArgumentException::class);

        MessageSerializer::serialize(new \Asika\Http\Test\Stub\StubMessage());
    }
}
