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
            [new \GuzzleHttp\Psr7\Request('GET', 'http://example.com')],
            [new \Zend\Diactoros\Request('http://example.com', 'GET')],
        ];
    }
}
