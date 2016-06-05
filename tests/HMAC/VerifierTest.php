<?php

namespace UMA\Tests\Psr\Http\Message\HMAC;

use GuzzleHttp\Psr7\Request;
use UMA\Psr\Http\Message\HMAC\Signer;
use UMA\Psr\Http\Message\HMAC\Verifier;

class VerifierTest extends \PHPUnit_Framework_TestCase
{
    public function testMaxDelay()
    {
        $request = new Request('GET', 'http://www.example.com/index.html');

        $signedRequest = (new Signer('a secret'))->sign($request);

        $verifier = (new Verifier())->setMaximumDelay(0);

        $this->assertTrue($verifier->verify($signedRequest, 'a secret'));

        sleep(1);

        $this->assertFalse($verifier->verify($signedRequest, 'a secret'));
    }
}
