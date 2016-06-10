<?php

namespace UMA\Tests\Psr\Http\Message\HMAC;

use Psr\Http\Message\RequestInterface;
use UMA\Psr\Http\Message\HMAC\Signer;
use UMA\Psr\Http\Message\HMAC\Verifier;
use UMA\Tests\Psr\Http\Message\Monitor\ArrayMonitor;
use UMA\Tests\Psr\Http\Message\RequestsProvider;

class VerifierTest extends \PHPUnit_Framework_TestCase
{
    const SECRET = '$ecr3t';

    use RequestsProvider;

    /**
     * @dataProvider simplestRequestProvider
     *
     * @param RequestInterface $request
     */
    public function testDelayedMessageDetection(RequestInterface $request)
    {
        $signedRequest = (new Signer(self::SECRET))
            ->sign($request);

        $timeSensitiveVerifier = (new Verifier())->setMaximumDelay(0);

        $this->assertTrue($timeSensitiveVerifier->verify($signedRequest, self::SECRET));

        sleep(1);

        $this->assertFalse($timeSensitiveVerifier->verify($signedRequest, self::SECRET));
    }

    /**
     * @dataProvider simplestRequestProvider
     *
     * @param RequestInterface $request
     */
    public function testReplayedMessageDetection(RequestInterface $request)
    {
        $signedRequest = (new Signer(self::SECRET))
            ->sign($request);

        $regularVerifier = (new Verifier());
        $this->assertTrue($regularVerifier->verify($signedRequest, self::SECRET));
        $this->assertTrue($regularVerifier->verify($signedRequest, self::SECRET));
        $this->assertTrue($regularVerifier->verify($signedRequest, self::SECRET));
        $this->assertTrue($regularVerifier->verify($signedRequest, self::SECRET));

        $monitoredVerifier = (new Verifier())->setMonitor(new ArrayMonitor());
        $this->assertTrue($monitoredVerifier->verify($signedRequest, self::SECRET));
        $this->assertFalse($monitoredVerifier->verify($signedRequest, self::SECRET));
        $this->assertFalse($monitoredVerifier->verify($signedRequest, self::SECRET));
        $this->assertFalse($monitoredVerifier->verify($signedRequest, self::SECRET));
    }
}
