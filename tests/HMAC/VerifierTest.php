<?php

namespace UMA\Tests\Psr\Http\Message\HMAC;

use Psr\Http\Message\RequestInterface;
use UMA\Psr\Http\Message\HMAC\Signer;
use UMA\Psr\Http\Message\HMAC\Specification;
use UMA\Psr\Http\Message\HMAC\Verifier;
use UMA\Psr\Http\Message\Internal\HashCalculator;
use UMA\Tests\Psr\Http\Message\Monitor\ArrayMonitor;
use UMA\Tests\Psr\Http\Message\ReflectionUtil;
use UMA\Tests\Psr\Http\Message\RequestsProvider;

class VerifierTest extends \PHPUnit_Framework_TestCase
{
    const SECRET = '$ecr3t';

    use ReflectionUtil;
    use RequestsProvider;

    /**
     * @var HashCalculator|\PHPUnit_Framework_MockObject_MockObject
     */
    private $calculator;

    /**
     * @var Verifier
     */
    private $verifier;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->calculator = $this->getMockBuilder(HashCalculator::class)
            ->setMethods(['hmac'])
            ->getMock();

        $this->replaceInstanceProperty($this->verifier = new Verifier(), 'calculator', $this->calculator);
    }

    /**
     * @dataProvider simplestRequestProvider
     *
     * @param RequestInterface $request
     */
    public function testMissingAuthorizationHeader(RequestInterface $request)
    {
        $this->calculator
            ->expects($this->never())
            ->method('hmac');

        $request = (new Signer(self::SECRET))
            ->sign($request)
            ->withoutHeader(Specification::AUTH_HEADER);

        $this->assertFalse($this->verifier->verify($request, "irrelevant, won't be even checked"));
    }

    /**
     * @dataProvider simplestRequestProvider
     *
     * @param RequestInterface $request
     */
    public function testMissingSignedHeadersHeader(RequestInterface $request)
    {
        $this->calculator
            ->expects($this->never())
            ->method('hmac');

        $request = (new Signer(self::SECRET))
            ->sign($request)
            ->withoutHeader(Specification::SIGN_HEADER);

        $this->assertFalse($this->verifier->verify($request, "irrelevant, won't be even checked"));
    }

    /**
     * @dataProvider simplestRequestProvider
     *
     * @param RequestInterface $request
     */
    public function testBadlyFormattedSignature(RequestInterface $request)
    {
        $this->calculator
            ->expects($this->never())
            ->method('hmac');

        $request = (new Signer(self::SECRET))
            ->sign($request)
            ->withHeader(Specification::AUTH_HEADER, Specification::AUTH_PREFIX.'herpder=');

        $this->assertFalse($this->verifier->verify($request, "irrelevant, won't be even checked"));
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
