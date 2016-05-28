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
        $this->authA = new Authenticator('$ecr3t');
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
        $expectedSignature = 'hCMNbSsUWmyt8bGrNWGOz4tZ9wZfyc8Boiv/pZxFeuI=';

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
        $expectedSignature = 'Wu7OtxsSWYWk547ChUiRKIS7Vcbwesz1HwNUwW9LOe8=';

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
        $expectedSignature = 'XQPzyELbSxz2iprdoTiyP6hJqyvD6mz+Ho51UZp4nE4=';

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
        $expectedSignature = 'BAiw7Lk3ImTu9qmEPbZXnJcqYmgky09alQWzCCQ3E3k=';

        $signedResponse = $this->authA->sign($response);

        $this->assertRequestHasSignature($signedResponse, $expectedSignature);
        $this->assertTrue($this->authA->verify($signedResponse));
        $this->assertFalse($this->authB->verify($signedResponse));
    }

    /**
     * @dataProvider jsonRequestProvider
     *
     * @param RequestInterface $request
     */
    public function testJsonRequest(RequestInterface $request)
    {
        $expectedSignature = 'iL3PfGt8crLuUEubRXbXfXEnE2GyQhuNuEirTMrIiFY=';

        $signedRequest = $this->authA->sign($request);

        $this->assertRequestHasSignature($signedRequest, $expectedSignature);
        $this->assertTrue($this->authA->verify($signedRequest));
        $this->assertFalse($this->authB->verify($signedRequest));
    }

    /**
     * @dataProvider jsonResponseProvider
     *
     * @param ResponseInterface $response
     */
    public function testJsonResponse(ResponseInterface $response)
    {
        $expectedSignature = '37m7Y5EtVot5tP8vMw+nk+2lRUW5muM25E2sKevkbCk=';

        $signedResponse = $this->authA->sign($response);

        $this->assertRequestHasSignature($signedResponse, $expectedSignature);
        $this->assertTrue($this->authA->verify($signedResponse));
        $this->assertFalse($this->authB->verify($signedResponse));
    }

    /**
     * @dataProvider queryParamsRequestProvider
     *
     * @param RequestInterface $request
     */
    public function testQueryParamsRequest(RequestInterface $request)
    {
        $expectedSignature = 'U8ELXTtMz1El1KIVJbQk+F5uLonKbRts/CSGXQY80Ro=';

        $signedRequest = $this->authA->sign($request);

        $this->assertRequestHasSignature($signedRequest, $expectedSignature);
        $this->assertTrue($this->authA->verify($signedRequest));
        $this->assertFalse($this->authB->verify($signedRequest));
    }

    /**
     * @dataProvider simpleFormRequestProvider
     *
     * @param RequestInterface $request
     */
    public function testSimpleFormRequest(RequestInterface $request)
    {
        $expectedSignature = 'OS0lrf0CI6/HSoipkdoyPdmtjBBAfhZF12UWva3LTYg=';

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
        $expectedSignature = 'EAAUAb/VY6pSHVJheEcD1RA6/YhRXgVph8H3fQ+GjQc=';

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
        $expectedSignature = 'Q0MkgkQpQ8diQKu3v31PvIaVPz4PxZ6zxzSvP0NceUE=';

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
