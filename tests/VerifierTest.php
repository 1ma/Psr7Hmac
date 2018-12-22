<?php

declare(strict_types=1);

namespace UMA\Tests\Psr7Hmac;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use UMA\Psr7Hmac\Internal\HashCalculator;
use UMA\Psr7Hmac\Signer;
use UMA\Psr7Hmac\Specification;
use UMA\Psr7Hmac\Verifier;

final class VerifierTest extends TestCase
{
    private const SECRET = '$ecr3t';

    use ReflectionUtil;
    use RequestsProvider;

    /**
     * @var HashCalculator|MockObject
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
     */
    public function testMissingAuthorizationHeader(RequestInterface $request): void
    {
        $this->calculator
            ->expects($this->never())
            ->method('hmac');

        $request = (new Signer(self::SECRET))
            ->sign($request)
            ->withoutHeader(Specification::AUTH_HEADER);

        self::assertFalse($this->verifier->verify($request, "irrelevant, won't be even checked"));
    }

    /**
     * @dataProvider simplestRequestProvider
     */
    public function testMissingSignedHeadersHeader(RequestInterface $request): void
    {
        $this->calculator
            ->expects($this->never())
            ->method('hmac');

        $request = (new Signer(self::SECRET))
            ->sign($request)
            ->withoutHeader(Specification::SIGN_HEADER);

        self::assertFalse($this->verifier->verify($request, "irrelevant, won't be even checked"));
    }

    /**
     * @dataProvider simplestRequestProvider
     */
    public function testBadlyFormattedSignature(RequestInterface $request): void
    {
        $this->calculator
            ->expects($this->never())
            ->method('hmac');

        $request = (new Signer(self::SECRET))
            ->sign($request)
            ->withHeader(Specification::AUTH_HEADER, Specification::AUTH_PREFIX.'herpder=');

        self::assertFalse($this->verifier->verify($request, "irrelevant, won't be even checked"));
    }
}
