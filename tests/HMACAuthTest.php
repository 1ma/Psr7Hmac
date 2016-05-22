<?php

namespace UMA\Tests;

use Psr\Http\Message\MessageInterface;
use UMA\HMACAuth;

class HMACAuthTest extends HMACTestCase
{
    public function testSimpleRequests()
    {
        $expectedSignature = 'PueTWqaIii0VrFEFJRN4fLKP0qyTC2hFUIqEmqsSASs=';

        foreach ($this->psr7RequestShotgun('GET', 'http://example.com/foo') as $request) {
            $signedRequest = HMACAuth::sign($request, '$ecr3t');

            $this->assertHasSignature($signedRequest, $expectedSignature);

            $this->assertTrue(HMACAuth::verify($signedRequest, '$ecr3t'));
            $this->assertFalse(HMACAuth::verify($signedRequest, 'wr0ng_$ecr3t'));
        }
    }

    public function testSimpleResponses()
    {
        $expectedSignature = 'VyDIPfyx+SO53fiQc3lNq03urAKIgeDyiGGZww9ccRU=';

        foreach ($this->psr7ResponseShotgun(200) as $response) {
            $signedResponse = HMACAuth::sign($response, '$ecr3t');

            $this->assertHasSignature($signedResponse, $expectedSignature);

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
    private function assertHasSignature(MessageInterface $signedMessage, $signature)
    {
        $this->assertTrue($signedMessage->hasHeader(HMACAuth::AUTH_HEADER));
        $this->assertSame(HMACAuth::AUTH_PREFIX.' '.$signature, $signedMessage->getHeaderLine(HMACAuth::AUTH_HEADER));
    }
}
