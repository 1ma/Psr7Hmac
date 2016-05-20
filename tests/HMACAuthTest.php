<?php

namespace OneMA\Tests;

use OneMA\HMACAuth;
use Psr\Http\Message\MessageInterface;

class HMACAuthTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider simpleRequestProvider
     */
    public function testSimpleRequest(MessageInterface $request)
    {
        $signedRequest = HMACAuth::sign($request, '$ecr3t');

        $this->assertTrue(HMACAuth::verify($signedRequest, '$ecr3t'));
        $this->assertFalse(HMACAuth::verify($signedRequest, 'wr0ng_$ecr3t'));
    }

    public function simpleRequestProvider()
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
}
