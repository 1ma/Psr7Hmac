<?php

namespace UMA\Tests\Psr\Http\Message\HMAC;

use Psr\Http\Message\MessageInterface;
use UMA\Psr\Http\Message\HMAC\Authenticator;
use UMA\Psr\Http\Message\HMAC\Specification;
use UMA\Tests\Psr\Http\Message\BaseTestCase;

class AuthenticatorTest extends BaseTestCase
{
    /**
     * @var Authenticator
     */
    private $authenticator;

    protected function setUp()
    {
        $this->authenticator = new Authenticator();
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
                'gQ40JfujwnnE5/pjfb0Et2uHzxGYMJbODuUb8cFLxrA=',
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
                'eqzqnfLxcnxSj8zaUqNaFVwObLEgmZSAkq6T6CyvaWE=',
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
                'ItmODW3lxpRTblMD4MT6zxC0oblu2RezNkun8Tr4D+Q=',
            ],

            'headed responses' => [
                200,
                [
                    'Content-Type' => 'text/html',
                    'Content-Encoding' => 'gzip',
                    'Accept-Ranges' => 'bytes',
                    'Content-Length' => '606',
                ],
                'sQJZRllkAlcqNOTXBOamAMskxrjZdCiqk5dYqP0uizk=',
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
        $request = new \GuzzleHttp\Psr7\Request('GET', 'http://example.com', [Specification::AUTH_HEADER => 'HMAC-SHA256 herpder=']);

        $this->assertFalse($this->authenticator->verify($request, 'irrelevant'));
    }

    /**
     * @param MessageInterface $signedMessage
     * @param string           $signature
     */
    private function assertRequestHasSignature(MessageInterface $signedMessage, $signature)
    {
        $this->assertTrue($signedMessage->hasHeader(Specification::AUTH_HEADER));
        $this->assertTrue($signedMessage->hasHeader(Specification::SIGN_HEADER));
        $this->assertSame(Specification::AUTH_PREFIX.' '.$signature, $signedMessage->getHeaderLine(Specification::AUTH_HEADER), get_class($signedMessage));
    }
}
