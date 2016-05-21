<?php

namespace UMA\Tests;

use UMA\HMACAuth;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class HMACAuthTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider simpleRequestsProvider
     */
    public function testSimpleRequests(RequestInterface $request)
    {
        $signedRequest = HMACAuth::sign($request, '$ecr3t');

        $this->assertTrue(HMACAuth::verify($signedRequest, '$ecr3t'));
        $this->assertFalse(HMACAuth::verify($signedRequest, 'wr0ng_$ecr3t'));
    }

    public function simpleRequestsProvider()
    {
        return [
            [new \Asika\Http\Request('http://example.com', 'GET')],
            [new \GuzzleHttp\Psr7\Request('GET', 'http://example.com')],
            [new \Phyrexia\Http\Request('GET', 'http://example.com')],
            [new \RingCentral\Psr7\Request('GET', 'http://example.com')],
            [new \Slim\Http\Request(
                'GET',
                new \Slim\Http\Uri('http', 'example.com'),
                new \Slim\Http\Headers(),
                [],
                [],
                new \Slim\Http\RequestBody())],
            [new \Wandu\Http\Psr\Request('GET', new \Wandu\Http\Psr\Uri('http://example.com'))],
            [new \Zend\Diactoros\Request('http://example.com', 'GET')],
        ];
    }

    /**
     * @dataProvider simpleResponsesProvider
     */
    public function testSimpleResponses(ResponseInterface $response)
    {
        $signedResponse = HMACAuth::sign($response, '$ecr3t');

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

    public function testSignNeitherRequestNorResponse()
    {
        $this->setExpectedException(\InvalidArgumentException::class);

        HMACAuth::sign(new \Asika\Http\Test\Stub\StubMessage(), '$ecr3t');
    }
}
