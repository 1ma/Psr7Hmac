<?php

namespace UMA\Tests\Psr\Http\Message\Security;

use Psr\Http\Message\MessageInterface;
use UMA\Psr\Http\Message\Security\HMACAuthenticator;
use UMA\Psr\Http\Message\Security\HMACSpecification;
use UMA\Tests\Psr\Http\Message\BaseTestCase;

class HMACAuthenticatorTest extends BaseTestCase
{
    /**
     * @var HMACAuthenticator
     */
    private $authenticator;

    protected function setUp()
    {
        parent::setUp();

        $this->authenticator = new HMACAuthenticator();
    }

    public function testSimpleRequests()
    {
        $expectedSignature = 'gQ40JfujwnnE5/pjfb0Et2uHzxGYMJbODuUb8cFLxrA=';

        foreach ($this->psr7RequestShotgun('GET', 'http://www.example.com/index.html') as $request) {
            $signedRequest = $this->authenticator->sign($request, '$ecr3t');

            $this->assertRequestHasSignature($signedRequest, $expectedSignature);
            $this->assertTrue($this->authenticator->verify($signedRequest, '$ecr3t'));
            $this->assertFalse($this->authenticator->verify($signedRequest, 'wr0ng_$ecr3t'));
        }
    }

    public function testSimpleResponses()
    {
        $expectedSignature = 'ItmODW3lxpRTblMD4MT6zxC0oblu2RezNkun8Tr4D+Q=';

        foreach ($this->psr7ResponseShotgun(200) as $response) {
            $signedResponse = $this->authenticator->sign($response, '$ecr3t');

            $this->assertRequestHasSignature($signedResponse, $expectedSignature);
            $this->assertTrue($this->authenticator->verify($signedResponse, '$ecr3t'));
            $this->assertFalse($this->authenticator->verify($signedResponse, 'wr0ng_$ecr3t'));
        }
    }

    public function testMissingAuthorizationHeader()
    {
        $request = new \GuzzleHttp\Psr7\Request('GET', 'http://example.com');

        $this->assertFalse($this->authenticator->verify($request, 'irrelevant'));
    }

    public function testBadFormattedSignature()
    {
        $request = new \GuzzleHttp\Psr7\Request('GET', 'http://example.com', [HMACSpecification::AUTH_HEADER => 'HMAC-SHA256 herpder=']);

        $this->assertFalse($this->authenticator->verify($request, 'irrelevant'));
    }

    /**
     * @param MessageInterface $signedMessage
     * @param string           $signature
     */
    private function assertRequestHasSignature(MessageInterface $signedMessage, $signature)
    {
        $this->assertTrue($signedMessage->hasHeader(HMACSpecification::AUTH_HEADER));
        $this->assertTrue($signedMessage->hasHeader(HMACSpecification::SIGN_HEADER));
        $this->assertSame(HMACSpecification::AUTH_PREFIX.' '.$signature, $signedMessage->getHeaderLine(HMACSpecification::AUTH_HEADER), get_class($signedMessage));
    }
}
