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
    private $authA;

    /**
     * @var Authenticator
     */
    private $authB;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->authA = new Authenticator(self::SECRET);
        $this->authB = new Authenticator('an0ther $ecr3t');
    }

    public function testMissingAuthorizationHeader()
    {
        $request = new GuzzleRequest('GET', 'http://example.com');

        $this->assertFalse($this->authA->verify($request));
    }

    public function testBadlyFormattedSignature()
    {
        $request = new GuzzleRequest('GET', 'http://example.com', [Specification::AUTH_HEADER => Specification::AUTH_PREFIX.' herpder=']);

        $this->assertFalse($this->authA->verify($request));
    }

    /**
     * @dataProvider simplestRequestProvider
     *
     * @param RequestInterface $request
     */
    public function testSimplestRequest(RequestInterface $request)
    {
        $expectedSignature = 'gQ40JfujwnnE5/pjfb0Et2uHzxGYMJbODuUb8cFLxrA=';

        $signedRequest = $this->authA->sign($request);

        $this->assertRequestHasSignature($signedRequest, $expectedSignature);
        $this->assertTrue($this->authA->verify($signedRequest));
        $this->assertFalse($this->authB->verify($signedRequest));
    }

    /**
     * @dataProvider simplestResponseProvider
     *
     * @param ResponseInterface $response
     */
    public function testSimplestResponse(ResponseInterface $response)
    {
        $expectedSignature = 'ItmODW3lxpRTblMD4MT6zxC0oblu2RezNkun8Tr4D+Q=';

        $signedResponse = $this->authA->sign($response);

        $this->assertRequestHasSignature($signedResponse, $expectedSignature);
        $this->assertTrue($this->authA->verify($signedResponse));
        $this->assertFalse($this->authB->verify($signedResponse));
    }

    /**
     * @dataProvider emptyRequestWithHeadersProvider
     *
     * @param RequestInterface $request
     */
    public function testEmptyRequestWithHeaders(RequestInterface $request)
    {
        $expectedSignature = 'eqzqnfLxcnxSj8zaUqNaFVwObLEgmZSAkq6T6CyvaWE=';

        $signedRequest = $this->authA->sign($request);

        $this->assertRequestHasSignature($signedRequest, $expectedSignature);
        $this->assertTrue($this->authA->verify($signedRequest));
        $this->assertFalse($this->authB->verify($signedRequest));
    }

    /**
     * @dataProvider emptyResponseWithHeadersProvider
     *
     * @param ResponseInterface $response
     */
    public function testEmptyResponseWithHeaders(ResponseInterface $response)
    {
        $expectedSignature = 'sQJZRllkAlcqNOTXBOamAMskxrjZdCiqk5dYqP0uizk=';

        $signedResponse = $this->authA->sign($response);

        $this->assertRequestHasSignature($signedResponse, $expectedSignature);
        $this->assertTrue($this->authA->verify($signedResponse));
        $this->assertFalse($this->authB->verify($signedResponse));
    }

    /**
     * @dataProvider simpleFormRequestProvider
     *
     * @param RequestInterface $request
     */
    public function testSimpleFormRequest(RequestInterface $request)
    {
        $expectedSignature = 'vc6RVMtO8KvxcsT1itDVy1tvApMPfV8/jaSuHfrmi80=';

        $signedRequest = $this->authA->sign($request);

        $this->assertRequestHasSignature($signedRequest, $expectedSignature);
        $this->assertTrue($this->authA->verify($signedRequest));
        $this->assertFalse($this->authB->verify($signedRequest));
    }

    /**
     * @dataProvider binaryRequestProvider
     *
     * @param RequestInterface $request
     */
    public function testBinaryRequest(RequestInterface $request)
    {
        $expectedSignature = 'Ix+BdOyDHLANIAbBhvSRPS9DzhXJN2JAFWzlflj8XJE=';

        $signedRequest = $this->authA->sign($request);

        $this->assertRequestHasSignature($signedRequest, $expectedSignature);
        $this->assertTrue($this->authA->verify($signedRequest));
        $this->assertFalse($this->authB->verify($signedRequest));
    }

    /**
     * @dataProvider binaryResponseProvider
     *
     * @param ResponseInterface $response
     */
    public function testBinaryResponse(ResponseInterface $response)
    {
        $expectedSignature = 'zxw8sFPd/bFS3HKGcyGCbh4jp57nGn+DCf/k9MCh6ak=';

        $signedResponse = $this->authA->sign($response);

        $this->assertRequestHasSignature($signedResponse, $expectedSignature);
        $this->assertTrue($this->authA->verify($signedResponse));
        $this->assertFalse($this->authB->verify($signedResponse));
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
