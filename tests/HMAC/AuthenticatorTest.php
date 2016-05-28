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
        $expectedSignature = 'tCPmkWr72kpjh4x216PP4u2NCSlc7R9lXoAvx53ry4U=';

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
        $expectedSignature = 'ZAF0hNrFxjXtbOjN8RVtd1eprtcExMQdliCge5kIIfA=';

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
        $expectedSignature = '4dhPH2FFisH9yKcAfwZaMz2jV/JrhH94mSQ3Sic+Kz0=';

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
        $expectedSignature = '0ZFlbbKtptgztZEEc3o2xqFENTp4mtm86bSP6YeK59I=';

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
        $expectedSignature = 'UTcToy5vEIE2GjRFEALercuBpxHzcvrxHNR2Tv+lfnw=';

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
        $expectedSignature = 'Kv1016HrkvAmeNtTXvK2ca6LmSSwQ6wZ1ZCUBKNQVOk=';

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
        $expectedSignature = 'rDiQbpdSirmDobA8kEAmeYGkFLcxfyQ76MWKhg9WoJg=';

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
        $expectedSignature = 'jO4l3Ao0TBeY98jRDJD3WoXft84UNRKRJdn94C4iipo=';

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
        $expectedSignature = 'xbOAlEPMfclLIF6vj+I12SLV0YJogdUkkp8g1+TxeAY=';

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
        $expectedSignature = 'SeuANaHf2lYMDyErejDuXwfZsowx5sVdfbhWZIQKbEc=';

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
