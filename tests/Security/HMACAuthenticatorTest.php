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
        $this->authenticator = new HMACAuthenticator();
    }

    /**
     * @dataProvider requestsProvider
     *
     * @param string   $method
     * @param string   $url
     * @param string[] $headers
     * @param string   $expectedSignature
     */
    public function testRequests($method, $url, array $headers, $expectedSignature)
    {
        $secret = '$ecr3t';

        foreach ($this->psr7RequestShotgun($method, $url, $headers) as $request) {
            $signedRequest = $this->authenticator->sign($request, $secret);

            $this->assertRequestHasSignature($signedRequest, $expectedSignature);
            $this->assertTrue($this->authenticator->verify($signedRequest, $secret));
            $this->assertFalse($this->authenticator->verify($signedRequest, 'wr0ng_$ecr3t'));
        }
    }

    public function requestsProvider()
    {
        return [
            'simple requests' => [
                'GET',
                'http://www.example.com/index.html',
                [],
                'l0xjvO5WlJLQnanAQ3UVeujg70qPTjRr6AIQDOW1Grg=',
            ],

            'headed requests' => [
                'GET',
                'http://www.example.com/index.html',
                [
                    'User-Agent' => 'PHP/5.6.21',
                    'Accept' => '*/*',
                    'Connection' => 'keep-alive',
                    'Accept-Encoding' => 'gzip, deflate',
                ],
                '6QzCTRpsU3N4I0K49QAJU23VdFLme22cp8kFORQnTBg=',
            ],
        ];
    }

    /**
     * @dataProvider responsesProvider
     *
     * @param int      $statusCode
     * @param string[] $headers
     * @param string   $expectedSignature
     */
    public function testResponses($statusCode, array $headers, $expectedSignature)
    {
        $secret = '$ecr3t';

        foreach ($this->psr7ResponseShotgun($statusCode, $headers) as $response) {
            $signedResponse = $this->authenticator->sign($response, $secret);

            $this->assertRequestHasSignature($signedResponse, $expectedSignature);
            $this->assertTrue($this->authenticator->verify($signedResponse, $secret));
            $this->assertFalse($this->authenticator->verify($signedResponse, 'wr0ng_$ecr3t'));
        }
    }

    public function responsesProvider()
    {
        return [
            'simple responses' => [
                200,
                [],
                'HSH6h1ORWt5ig0SnSW4COvUGodu3lBHYBC/iLiQyxcE=',
            ],

            'headed responses' => [
                200,
                [
                    'Content-Type' => 'text/html',
                    'Content-Encoding' => 'gzip',
                    'Accept-Ranges' => 'bytes',
                    'Content-Length' => '606',
                ],
                'btpfn0fDZgVURq3sAFaoUzXidl16U27tiCJO7Ntzavw=',
            ],
        ];
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
