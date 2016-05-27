<?php

namespace UMA\Tests\Psr\Http\Message\HMAC;

use GuzzleHttp\Psr7\Request as GuzzleRequest;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use UMA\Psr\Http\Message\HMAC\Authenticator;
use UMA\Psr\Http\Message\HMAC\Specification;
use UMA\Tests\Psr\Http\Message\RequestsProvider;
use UMA\Tests\Psr\Http\Message\ResponsesProvider;

class AuthenticatorTest extends \PHPUnit_Framework_TestCase
{
    use RequestsProvider;
    use ResponsesProvider;

    /**
     * @var Authenticator
     */
    private $authenticator;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->authenticator = new Authenticator();
    }

    public function testMissingAuthorizationHeader()
    {
        $request = new GuzzleRequest('GET', 'http://example.com');

        $this->assertFalse($this->authenticator->verify($request, 'irrelevant'));
    }

    public function testBadlyFormattedSignature()
    {
        $request = new GuzzleRequest('GET', 'http://example.com', [Specification::AUTH_HEADER => Specification::AUTH_PREFIX.' herpder=']);

        $this->assertFalse($this->authenticator->verify($request, 'irrelevant'));
    }

    /**
     * @dataProvider simplestRequestProvider
     *
     * @param RequestInterface $request
     */
    public function testSimplestRequest(RequestInterface $request)
    {
        $expectedSignature = 'gQ40JfujwnnE5/pjfb0Et2uHzxGYMJbODuUb8cFLxrA=';

        $signedRequest = $this->authenticator->sign($request, '$ecr3t');

        $this->assertRequestHasSignature($signedRequest, $expectedSignature);
        $this->assertTrue($this->authenticator->verify($signedRequest, '$ecr3t'));
        $this->assertFalse($this->authenticator->verify($signedRequest, 'wr0ng_$ecr3t'));
    }

    /**
     * @dataProvider simplestResponseProvider
     *
     * @param ResponseInterface $response
     */
    public function testSimplestResponse(ResponseInterface $response)
    {
        $expectedSignature = 'ItmODW3lxpRTblMD4MT6zxC0oblu2RezNkun8Tr4D+Q=';

        $signedResponse = $this->authenticator->sign($response, '$ecr3t');

        $this->assertRequestHasSignature($signedResponse, $expectedSignature);
        $this->assertTrue($this->authenticator->verify($signedResponse, '$ecr3t'));
        $this->assertFalse($this->authenticator->verify($signedResponse, 'wr0ng_$ecr3t'));
    }

    /**
     * @dataProvider emptyRequestWithHeadersProvider
     *
     * @param RequestInterface $request
     */
    public function testEmptyRequestWithHeaders(RequestInterface $request)
    {
        $expectedSignature = 'eqzqnfLxcnxSj8zaUqNaFVwObLEgmZSAkq6T6CyvaWE=';

        $signedRequest = $this->authenticator->sign($request, '$ecr3t');

        $this->assertRequestHasSignature($signedRequest, $expectedSignature);
        $this->assertTrue($this->authenticator->verify($signedRequest, '$ecr3t'));
        $this->assertFalse($this->authenticator->verify($signedRequest, 'wr0ng_$ecr3t'));
    }

    /**
     * @dataProvider emptyResponseWithHeadersProvider
     *
     * @param ResponseInterface $response
     */
    public function testEmptyResponseWithHeaders(ResponseInterface $response)
    {
        $expectedSignature = 'sQJZRllkAlcqNOTXBOamAMskxrjZdCiqk5dYqP0uizk=';

        $signedResponse = $this->authenticator->sign($response, '$ecr3t');

        $this->assertRequestHasSignature($signedResponse, $expectedSignature);
        $this->assertTrue($this->authenticator->verify($signedResponse, '$ecr3t'));
        $this->assertFalse($this->authenticator->verify($signedResponse, 'wr0ng_$ecr3t'));
    }

    /**
     * @dataProvider bodiedRequestProvider
     *
     * @param RequestInterface $request
     */
    public function testBodiedRequest(RequestInterface $request)
    {
        $expectedSignature = 'Ix+BdOyDHLANIAbBhvSRPS9DzhXJN2JAFWzlflj8XJE=';

        $signedRequest = $this->authenticator->sign($request, '$ecr3t');

        $this->assertRequestHasSignature($signedRequest, $expectedSignature);
        $this->assertTrue($this->authenticator->verify($signedRequest, '$ecr3t'));
        $this->assertFalse($this->authenticator->verify($signedRequest, 'wr0ng_$ecr3t'));
    }

    /**
     * @dataProvider bodiedResponseProvider
     *
     * @param ResponseInterface $response
     */
    public function testBodiedResponse(ResponseInterface $response)
    {
        $expectedSignature = 'zxw8sFPd/bFS3HKGcyGCbh4jp57nGn+DCf/k9MCh6ak=';

        $signedResponse = $this->authenticator->sign($response, '$ecr3t');

        $this->assertRequestHasSignature($signedResponse, $expectedSignature);
        $this->assertTrue($this->authenticator->verify($signedResponse, '$ecr3t'));
        $this->assertFalse($this->authenticator->verify($signedResponse, 'wr0ng_$ecr3t'));
    }

    /**
     * @param MessageInterface $signedMessage
     * @param string           $signature
     */
    private function assertRequestHasSignature(MessageInterface $signedMessage, $signature)
    {
        $this->assertTrue($signedMessage->hasHeader(Specification::AUTH_HEADER));
        $this->assertTrue($signedMessage->hasHeader(Specification::SIGN_HEADER));
        $this->assertSame(Specification::AUTH_PREFIX.' '.$signature, $signedMessage->getHeaderLine(Specification::AUTH_HEADER));
    }
}
