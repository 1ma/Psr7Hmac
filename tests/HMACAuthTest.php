<?php

namespace UMA\Tests;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use UMA\HMACAuth;

class HMACAuthTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider simpleRequestsProvider
     */
    public function testSimpleRequests(RequestInterface $request)
    {
        $signedRequest = HMACAuth::sign($request, '$ecr3t');

        $this->assertHasSignature($signedRequest, 'PueTWqaIii0VrFEFJRN4fLKP0qyTC2hFUIqEmqsSASs=');

        $this->assertTrue(HMACAuth::verify($signedRequest, '$ecr3t'));
        $this->assertFalse(HMACAuth::verify($signedRequest, 'wr0ng_$ecr3t'));
    }

    public function simpleRequestsProvider()
    {
        return [
            [new \Asika\Http\Request('http://example.com/foo', 'GET')],
            [new \GuzzleHttp\Psr7\Request('GET', 'http://example.com/foo')],
            [new \Phyrexia\Http\Request('GET', 'http://example.com/foo')],
            [new \RingCentral\Psr7\Request('GET', 'http://example.com/foo')],
            [new \Slim\Http\Request(
                'GET',
                new \Slim\Http\Uri('http', 'example.com', null, '/foo'),
                new \Slim\Http\Headers(),
                [],
                [],
                new \Slim\Http\RequestBody())],
            [new \Wandu\Http\Psr\Request('GET', new \Wandu\Http\Psr\Uri('http://example.com/foo'))],
            [new \Zend\Diactoros\Request('http://example.com/foo', 'GET')],
        ];
    }

    /**
     * @dataProvider simpleResponsesProvider
     */
    public function testSimpleResponses(ResponseInterface $response)
    {
        $signedResponse = HMACAuth::sign($response, '$ecr3t');

        $this->assertHasSignature($signedResponse, 'VyDIPfyx+SO53fiQc3lNq03urAKIgeDyiGGZww9ccRU=');

        $this->assertTrue(HMACAuth::verify($signedResponse, '$ecr3t'));
        $this->assertFalse(HMACAuth::verify($signedResponse, 'wr0ng_$ecr3t'));
    }

    public function simpleResponsesProvider()
    {
        return [
            [new \Asika\Http\Response()],
            [new \GuzzleHttp\Psr7\Response()],
            [new \Phyrexia\Http\Response()],
            [new \RingCentral\Psr7\Response()],
            [new \Slim\Http\Response()],
            [new \Wandu\Http\Psr\Response()],
            [new \Zend\Diactoros\Response()],
        ];
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
