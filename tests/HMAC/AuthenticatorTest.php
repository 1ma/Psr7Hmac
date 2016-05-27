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

    const SECRET = '$ecr3t';

    /**
     * @var Authenticator
     */
    private $authenticatorA;

    /**
     * @var Authenticator
     */
    private $authenticatorB;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->authenticatorA = new Authenticator(self::SECRET);
        $this->authenticatorB = new Authenticator('SuperSecret');
    }

    public function testMissingAuthorizationHeader()
    {
        $request = new GuzzleRequest('GET', 'http://example.com');

        $this->assertFalse($this->authenticatorA->verify($request));
    }

    public function testBadlyFormattedSignature()
    {
        $request = new GuzzleRequest('GET', 'http://example.com', [Specification::AUTH_HEADER => Specification::AUTH_PREFIX.' herpder=']);

        $this->assertFalse($this->authenticatorA->verify($request));
    }

    /**
     * @dataProvider simplestRequestProvider
     *
     * @param RequestInterface $request
     */
    public function testSimplestRequest(RequestInterface $request)
    {
        $expectedSignature = 'gQ40JfujwnnE5/pjfb0Et2uHzxGYMJbODuUb8cFLxrA=';

        $signedRequest = $this->authenticatorA->sign($request);

        $this->assertRequestHasSignature($signedRequest, $expectedSignature);
        $this->assertTrue($this->authenticatorA->verify($signedRequest));
        $this->assertFalse($this->authenticatorB->verify($signedRequest));
    }

    /**
     * @dataProvider simplestResponseProvider
     *
     * @param ResponseInterface $response
     */
    public function testSimplestResponse(ResponseInterface $response)
    {
        $expectedSignature = 'ItmODW3lxpRTblMD4MT6zxC0oblu2RezNkun8Tr4D+Q=';

        $signedResponse = $this->authenticatorA->sign($response);

        $this->assertRequestHasSignature($signedResponse, $expectedSignature);
        $this->assertTrue($this->authenticatorA->verify($signedResponse));
        $this->assertFalse($this->authenticatorB->verify($signedResponse));
    }

    /**
     * @dataProvider emptyRequestWithHeadersProvider
     *
     * @param RequestInterface $request
     */
    public function testEmptyRequestWithHeaders(RequestInterface $request)
    {
        $expectedSignature = 'eqzqnfLxcnxSj8zaUqNaFVwObLEgmZSAkq6T6CyvaWE=';

        $signedRequest = $this->authenticatorA->sign($request);

        $this->assertRequestHasSignature($signedRequest, $expectedSignature);
        $this->assertTrue($this->authenticatorA->verify($signedRequest));
        $this->assertFalse($this->authenticatorB->verify($signedRequest));
    }

    /**
     * @dataProvider emptyResponseWithHeadersProvider
     *
     * @param ResponseInterface $response
     */
    public function testEmptyResponseWithHeaders(ResponseInterface $response)
    {
        $expectedSignature = 'sQJZRllkAlcqNOTXBOamAMskxrjZdCiqk5dYqP0uizk=';

        $signedResponse = $this->authenticatorA->sign($response);

        $this->assertRequestHasSignature($signedResponse, $expectedSignature);
        $this->assertTrue($this->authenticatorA->verify($signedResponse));
        $this->assertFalse($this->authenticatorB->verify($signedResponse));
    }

    /**
     * @dataProvider bodiedRequestProvider
     *
     * @param RequestInterface $request
     */
    public function testBodiedRequest(RequestInterface $request)
    {
        $expectedSignature = 'Ix+BdOyDHLANIAbBhvSRPS9DzhXJN2JAFWzlflj8XJE=';

        $signedRequest = $this->authenticatorA->sign($request);

        $this->assertRequestHasSignature($signedRequest, $expectedSignature);
        $this->assertTrue($this->authenticatorA->verify($signedRequest));
        $this->assertFalse($this->authenticatorB->verify($signedRequest));
    }

    /**
     * @dataProvider bodiedResponseProvider
     *
     * @param ResponseInterface $response
     */
    public function testBodiedResponse(ResponseInterface $response)
    {
        $expectedSignature = 'zxw8sFPd/bFS3HKGcyGCbh4jp57nGn+DCf/k9MCh6ak=';

        $signedResponse = $this->authenticatorA->sign($response);

        $this->assertRequestHasSignature($signedResponse, $expectedSignature);
        $this->assertTrue($this->authenticatorA->verify($signedResponse));
        $this->assertFalse($this->authenticatorB->verify($signedResponse));
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
