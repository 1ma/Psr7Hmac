<?php

namespace UMA\Tests\Psr\Http\Message\HMAC;

use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\MessageInterface;
use UMA\Psr\Http\Message\HMAC\Signer;
use UMA\Psr\Http\Message\HMAC\Verifier;
use UMA\Tests\Psr\Http\Message\Monitor\ArrayMonitor;

class VerifierTest extends \PHPUnit_Framework_TestCase
{
    const SECRET = '$ecr3t';

    /**
     * @var MessageInterface
     */
    private $signedRequest;

    protected function setUp()
    {
        $request = new Request('GET', 'http://www.example.com/index.html');

        $this->signedRequest = (new Signer(self::SECRET))->sign($request);
    }

    public function testDelayedMessageDetection()
    {
        $timeSensitiveVerifier = (new Verifier())->setMaximumDelay(0);

        $this->assertTrue($timeSensitiveVerifier->verify($this->signedRequest, self::SECRET));

        sleep(1);

        $this->assertFalse($timeSensitiveVerifier->verify($this->signedRequest, self::SECRET));
    }

    public function testReplayedMessageDetection()
    {
        $regularVerifier = (new Verifier());
        $this->assertTrue($regularVerifier->verify($this->signedRequest, self::SECRET));
        $this->assertTrue($regularVerifier->verify($this->signedRequest, self::SECRET));
        $this->assertTrue($regularVerifier->verify($this->signedRequest, self::SECRET));
        $this->assertTrue($regularVerifier->verify($this->signedRequest, self::SECRET));

        $monitoredVerifier = (new Verifier())->setMonitor(new ArrayMonitor());
        $this->assertTrue($monitoredVerifier->verify($this->signedRequest, self::SECRET));
        $this->assertFalse($monitoredVerifier->verify($this->signedRequest, self::SECRET));
        $this->assertFalse($monitoredVerifier->verify($this->signedRequest, self::SECRET));
        $this->assertFalse($monitoredVerifier->verify($this->signedRequest, self::SECRET));
    }
}
