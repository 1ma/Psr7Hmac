<?php

namespace UMA\Tests\Psr\Http\Message\Security;

use Psr\Http\Message\MessageInterface;
use UMA\Psr\Http\Message\Security\HMACAuth;
use UMA\Tests\Psr\Http\Message\BaseTestCase;

class HMACAuthTest extends BaseTestCase
{
    public function testSimpleRequests()
    {
        $expectedSignature = 'ws9P+LKeAplOT2ergYhdJpb9QeXZF3mUJSYcLEX40fI=';

        foreach ($this->psr7RequestShotgun('GET', 'http://www.example.com/index.html') as $request) {
            $signedRequest = HMACAuth::sign($request, '$ecr3t');

            $this->assertRequestHasSignature($signedRequest, $expectedSignature);
            $this->assertTrue(HMACAuth::verify($signedRequest, '$ecr3t'));
            $this->assertFalse(HMACAuth::verify($signedRequest, 'wr0ng_$ecr3t'));
        }
    }

    public function testSimpleResponses()
    {
        $expectedSignature = 'VyDIPfyx+SO53fiQc3lNq03urAKIgeDyiGGZww9ccRU=';

        foreach ($this->psr7ResponseShotgun(200) as $response) {
            $signedResponse = HMACAuth::sign($response, '$ecr3t');

            $this->assertRequestHasSignature($signedResponse, $expectedSignature);
            $this->assertTrue(HMACAuth::verify($signedResponse, '$ecr3t'));
            $this->assertFalse(HMACAuth::verify($signedResponse, 'wr0ng_$ecr3t'));
        }
    }

    public function testMissingAuthorizationHeader()
    {
        $request = new \GuzzleHttp\Psr7\Request('GET', 'http://example.com');

        $this->assertFalse(HMACAuth::verify($request, 'irrelevant'));
    }

    public function testBadFormattedSignature()
    {
        $request = new \GuzzleHttp\Psr7\Request('GET', 'http://example.com', [HMACAuth::AUTH_HEADER => 'HMAC-SHA256 herpder=']);

        $this->assertFalse(HMACAuth::verify($request, 'irrelevant'));
    }

    /**
     * @param MessageInterface $signedMessage
     * @param string           $signature
     */
    private function assertRequestHasSignature(MessageInterface $signedMessage, $signature)
    {
        $this->assertTrue($signedMessage->hasHeader(HMACAuth::AUTH_HEADER));
        $this->assertSame(HMACAuth::AUTH_PREFIX.' '.$signature, $signedMessage->getHeaderLine(HMACAuth::AUTH_HEADER));
    }
}
